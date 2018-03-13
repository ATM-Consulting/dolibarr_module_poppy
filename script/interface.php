<?php
	ob_start();

	ini_set('display_errors','On');
	error_reporting(E_ALL);

	require('../config.php');
	dol_include_once('/user/class/usergroup.class.php');
	dol_include_once('/core/lib/date.lib.php');
	dol_include_once('/expedition/class/expedition.class.php');

	ob_clean();

	$PDOdb = new TPDOdb;

	$get = isset($_REQUEST['get'])?$_REQUEST['get']:'';
	$put = isset($_REQUEST['put'])?$_REQUEST['put']:'';

	_get($PDOdb,$get);
	_put($PDOdb,$put);

function _get(&$PDOdb,$case) {
	switch ($case) {
		case 'shipping-details':
            $Tab = _getShippingDetails($PDOdb,GETPOST('id'));
			__out($Tab, 'json');
			break;

		case 'reception-details';
    		$Tab = _getReceptionDetails($PDOdb,GETPOST('id'));
    		__out($Tab, 'json');
    		break;

		case 'order-details';
    		$Tab = _getOrderDetails($PDOdb,GETPOST('id'));
    		__out($Tab, 'json');
    		break;

		case 'logged-status':
			print 'ok';

			break;
		case 'mistake-data':
			__out(_getMistakeData($PDOdb, GETPOST('ref')),'json');

			break;

		default:

			break;
	}

}

function _put(&$PDOdb,$case) {
	global $db,$langs,$conf,$user;

	switch ($case) {
		case 'shipping-prepared':
			$e=new Expedition($db);
			if($e->fetch(GETPOST('fk_shipping'))>0) {
				$e->array_options['options_isPrepared'] = GETPOST('isPrepared');
				$e->insertExtraFields();
				var_dump($e);
				echo 1;
			}
			else{
				echo 0;
			}

			break;
		case 'updateShippingQty':
			__out(_updateShippingQty(GETPOST('fk_shipping', 'int'), GETPOST('TDetIdQty', 'array')));
			break;

		case 'updateOrderQty':
		    __out(_updateOrderQty(GETPOST('fk_order', 'int'), GETPOST('TLineQtyAdded', 'array'), GETPOST('TLineQtyToAdd', 'array')));
		    break;
	}
}

function _updateOrderQty($fk_order, $TLineQtyAdded, $TLineToAdd) {

    global $db, $conf, $langs,$user;

    $langs->load('poppy@poppy');

    $response = new stdClass();
    $response->error = 0;
    $response->TError = array();
    $response->lasterror = '';

    if (empty($fk_order))
    {
        $response->lasterror = $langs->transnoentities('poppy_error_fk_shipping_empty');
        $response->TError[] = $response->lasterror; $response->error++;
    }

    if (empty($response->error))
    {
        $order = new Commande($db);
        if ($order->fetch($fk_order) > 0)
        {

            if ($order->statut == 0) // brouillon
            {
                $db->begin();

                foreach($TLineQtyAdded as $lineqtyadded) {

                    foreach ($order->lines as &$line)
                    {
                        if ($lineqtyadded[0] == $line->id && $lineqtyadded[1]>0)
                        {

                            $line->fetch_optionals($line->id);

                            $res = $order->updateline($line->id, $line->desc, $line->subprice, $line->qty+$lineqtyadded[1], $line->remise_percent, $line->tva_tx,
                                $line->localtax1_tx, $line->localtax2_tx, 'HT', $line->info_bits, $line->date_start, $line->date_end, $line->product_type,$line->fk_parent_line,
                                0, $line->fk_fournprice, $line->pa_ht, $line->label, $line->special_code, $line->array_options,$line->fk_unit);

                            if ($res < 0)
                            {
                                $response->lasterror = $line->db->lasterror;
                                $response->TError[] = $response->lasterror; $response->error++;
                                break;
                            }
                        }
                    }

                }

                foreach($TLineToAdd as $linetoadd) {

                    $product = new Product($db);
                    $product->fetch($linetoadd[0]);
                    if($product->id>0) {
                        $order->addline('', $product->price, $linetoadd[1], $product->tva_tx,0,0,$product->id);
                    }
                    else {
                        $response->lasterror = $order->db->lasterror;
                        $response->TError[] = $response->lasterror; $response->error++;
                    }

                }

                if (empty($response->error)) {
                    $db->commit();

                    if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
                        $outputlangs = $langs;

                        if (! empty($conf->global->MAIN_MULTILANGS)) $newlang = $order->thirdparty->default_lang;
                        if (! empty($newlang)) {
                            $outputlangs = new Translate("", $conf);
                            $outputlangs->setDefaultLang($newlang);
                        }

                        $order->fetch_lines();
                        $res = $order->generateDocument($order->modelpdf, $outputlangs, 0, 0, 0);

                    }


                }
                else $db->rollback();
            }
            else
            {
                $response->lasterror = $langs->transnoentities('poppy_warning_order_not_in_draft');
                $response->TError[] = $response->lasterror; $response->error++;
            }
        }
        else
        {
            $response->lasterror = $order->error;
            $response->TError[] = $response->lasterror; $response->error++;
        }
    }

    return $response;
}

function _getMistakeData(&$PDOdb, $ref) {

	global $langs,$db,$user,$conf;

	$Tab=array('label'=>$ref, 'isPackage'=>0,'TProduct'=>array());

	$PDOdb->Execute("SELECT rowid FROM ".MAIN_DB_PREFIX."product WHERE barcode=:ref OR ref=:ref ",array('ref'=>$ref));
	$obj = $PDOdb->Get_line();

	if($obj->rowid>0) {
		$p=new Product($db);
		$p->fetch($obj->rowid);
		$Tab['label'] = $p->label;
		$Tab['rowid'] = $p->id;

		$TColis = $p->getChildsArbo($p->id, true);
		if(count($TColis)>0) {
			$Tab['isPackage'] = 1;

			foreach($TColis as $idSousProd=>$data) {
				$ps = new Product($db);
				if($ps->fetch($idSousProd)>0) {
					$Tab['TProduct'][$ps->ref] = $data[1];
				}

			}
		}

	}
	else{
		$Tab['label'].=' '.$langs->trans('Unknown');
	}

	return $Tab;
}

function _getReceptionDetails(&$PDOdb, $id) {

	global $db,$langs,$user,$conf;
	$Tab=array();

	dol_include_once('/fourn/class/fournisseur.commande.class.php');
	dol_include_once('/fourn/class/fournisseur.product.class.php');

	$object = new CommandeFournisseur($db);
	$object->fetch($id);
	foreach($object->lines as &$line) {

		if($line->fk_product>0) {
			$addLine = true;

			$line->product = new Product($db);
			$line->product->fetch($line->fk_product);

			$product_supplier = new ProductFournisseur($db);
			$line->TSupplierPrice = $product_supplier->list_product_fournisseur_price($line->fk_product);
		//	var_dump($line->product->ref,$line->TSupplierPrice);exit;
			$line->barcode = $line->product->barcode;
			if($conf->categorie->enabled && !empty($conf->global->POPPY_EXCLUDE_CATEGORY)) {

				$TCatExclude = explode(',',$conf->global->POPPY_EXCLUDE_CATEGORY);

				dol_include_once('/categories/class/categorie.class.php');
				$c = new Categorie($db);
				$cats = $c->containing($line->fk_product,Categorie::TYPE_PRODUCT);
				foreach($cats as $cat) {
					if(in_array($cat->id, $TCatExclude)) {
						$addLine= false;
						break;
					}
				}
			}

			$line->qty_receive = $line->qty; //TODO remain quantity

			if($addLine) $Tab[] = $line;
		}



	}

	return $Tab;
}

function _getShippingDetails(&$PDOdb, $id) {
	global $db,$langs,$user,$conf;
	$Tab=array();

	$object = new Expedition($db);
	$object->fetch($id);

	foreach($object->lines as &$line) {

		if($line->fk_product>0) {

			$addLine = true;

			$line->product = new Product($db);
			$line->product->fetch($line->fk_product);

			$line->barcode = $line->product->barcode;
			if($conf->categorie->enabled) {

				$TCatExclude = explode(',',$conf->global->POPPY_EXCLUDE_CATEGORY);

				dol_include_once('/categories/class/categorie.class.php');
				$c = new Categorie($db);
				$cats = $c->containing($line->fk_product,Categorie::TYPE_PRODUCT);
				foreach($cats as $cat) {
					if(in_array($cat->id, $TCatExclude)) {
						$addLine= false;
						break;
					}
				}
			}

			if($addLine) $Tab[] = $line;
		}



	}

	return $Tab;
}

function _getOrderDetails(&$PDOdb, $id) {
    global $db,$langs,$user,$conf;
    $Tab=array();

    $object = new Commande($db);
    $object->fetch($id);

    foreach($object->lines as &$line) {

        if($line->fk_product>0) {

            $addLine = true;

            $line->product = new Product($db);
            $line->product->fetch($line->fk_product);

            $line->barcode = $line->product->barcode;
            if($conf->categorie->enabled) {

                $TCatExclude = explode(',',$conf->global->POPPY_EXCLUDE_CATEGORY);

                dol_include_once('/categories/class/categorie.class.php');
                $c = new Categorie($db);
                $cats = $c->containing($line->fk_product,Categorie::TYPE_PRODUCT);
                foreach($cats as $cat) {
                    if(in_array($cat->id, $TCatExclude)) {
                        $addLine= false;
                        break;
                    }
                }
            }

            if($addLine) $Tab[] = $line;
        }



    }

    return $Tab;
}

function _updateShippingQty($fk_shipping, $TDetIdQty)
{
	global $db, $conf, $langs;

	$langs->load('poppy@poppy');

	$response = new stdClass();
	$response->error = 0;
	$response->TError = array();
	$response->lasterror = '';

	if (empty($conf->global->POPPY_CAN_APPLY_THE_QTY_SCANNED))
	{
		$response->lasterror = $langs->transnoentities('poppy_error_trying_use_disabled_method');
		$response->TError[] = $response->lasterror; $response->error++;
	}

	if (empty($fk_shipping))
	{
		$response->lasterror = $langs->transnoentities('poppy_error_fk_shipping_empty');
		$response->TError[] = $response->lasterror; $response->error++;
	}

	if (empty($response->error))
	{
		$shipping = new Expedition($db);
		if ($shipping->fetch($fk_shipping) > 0)
		{

			if ($shipping->statut == 0) // brouillon
			{
				$db->begin();

				foreach ($shipping->lines as &$line)
				{
					if (isset($TDetIdQty[$line->id]))
					{
						$res = $line->setValueFrom('qty', $TDetIdQty[$line->id]);
						if ($res < 0)
						{
							$response->lasterror = $line->db->lasterror;
							$response->TError[] = $response->lasterror; $response->error++;
							break;
						}
					}
				}

				if (empty($response->error)) $db->commit();
				else $db->rollback();
			}
			else
			{
				$response->lasterror = $langs->transnoentities('poppy_warning_shipping_not_in_draft');
				$response->TError[] = $response->lasterror; $response->error++;
			}
		}
		else
		{
			$response->lasterror = $shipping->error;
			$response->TError[] = $response->lasterror; $response->error++;
		}
	}

	return $response;
}