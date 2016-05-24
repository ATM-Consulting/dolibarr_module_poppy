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
            $TTask = _getShippingDetails($PDOdb,GETPOST('id'));
			__out($TTask, 'json');
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
	}
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

function _getShippingDetails(&$PDOdb, $id) {
	global $db,$langs,$user,$conf;
	$Tab=array();
	
	$expedition = new Expedition($db);
	$expedition->fetch($id);
	
	foreach($expedition->lines as &$line) {
		
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
