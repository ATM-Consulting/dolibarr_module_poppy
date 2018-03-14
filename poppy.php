<?php

	require('config.php');
	dol_include_once('/user/class/usergroup.class.php');
	dol_include_once('/core/lib/functions.lib.php');
	dol_include_once('/core/lib/date.lib.php');
	dol_include_once('/expedition/class/expedition.class.php');
	dol_include_once('/product/stock/class/entrepot.class.php');
	dol_include_once('/core/lib/fourn.lib.php');
	dol_include_once('/fourn/class/fournisseur.commande.class.php');
	dol_include_once('/fourn/class/fournisseur.commande.dispatch.class.php');

	$langs->load('poppy@poppy');
	$langs->load('expedition');
	$langs->load('commande');
	$langs->load('reception');

	$fk_shipping_selected = GETPOST('fk_shipping');
	$fk_reception_selected= GETPOST('fk_reception');
	$fk_order_selected= GETPOST('fk_order');

	$hookmanager->initHooks(array('poppy'));

	$object=stdClass;

	$PDOdb = new TPDOdb;

?><!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> -->
<!DOCTYPE html>
<html>
	<head>
		<title>Dolibarr - <?php echo $langs->trans('Poppy'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" href="css/style.css"/>
		<link rel="stylesheet" href="lib/normalize.css"/>
		<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" />

		<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
		<script src="js/jquery-ui-1.10.2.custom.min.js" type="text/javascript"></script>
		<script src="lib/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

	</head>
	<body>
	    <div class="container-fluid">

			<?php
			if(!empty($conf->global->POPPY_RETRICT_TO_ONE) && ($fk_shipping_selected>0 || $fk_reception_selected>0 || $fk_order_selected>0)) {
				null;
			}
			else{
				require('./tpl/onglet.php');
			}

			?>
			<!-- Tab panes -->
			<div class="tab-content">
			  <div class="tab-pane active" id="panel-expedition">
			  		<div class="row">
			  		<?php
	                    if($conf->expedition->enabled && $user->rights->expedition->lire){

							if(!empty($conf->global->POPPY_RETRICT_TO_ONE) && $fk_shipping_selected>0) {
								$object = new Expedition($db);
								$object->fetch($fk_shipping_selected);
								echo '<h1>'.$object->ref.'</h1>';
							}
							else {
						    ?>
	                            <!-- Affichage de l'onglet "Expédition" -->

	                            <div class="col-md-4">
	                            	<?php

						if(empty($conf->global->POPPY_RETRICT_TO_ONE) || $fk_shipping_selected>0) {
 
							require('./tpl/expedition.php'); 
						}
					?>
	                            </div>

	                       <?php
	                       }
	                       ?>
	                            <div class="col-md-8">
	                            	<table  id="list-expedition-details" class="table table-striped" style="font-size:18px;">
								    <thead>
								      <tr>
								        <th><?php echo $langs->trans('EAN'); ?></th>
								        <th><?php echo $langs->trans('Product'); ?></th>
								        <th><?php echo $langs->trans('QtyToShip'); ?></th>
								        <th><?php echo $langs->trans('QtyScanned'); ?></th>
								        <th>&nbsp;</th>
								      </tr>
								    </thead>
								    <tbody>
								    </tbody>
								    </table>

	                            </div>
	                        <?php
	                    }

	                ?>
	               </div>
			  </div>
			  <div class="tab-pane" id="panel-order">
			  		<div class="row">
			  		<?php
	                    if($conf->commande->enabled && $user->rights->commande->lire){

	                        if(!empty($conf->global->POPPY_RETRICT_TO_ONE) && $fk_order_selected>0) {
								$object = new Commande($db);
								$object->fetch($fk_order_selected);
								echo '<h1>'.$object->ref.'</h1>';
							}
							else {
						    ?>
	                            <!-- Affichage de l'onglet "Expédition" -->

	                            <div class="col-md-4">
	                            	<?php if(empty($conf->global->POPPY_RETRICT_TO_ONE) || $fk_order_selected>0) {
							require('./tpl/order.php'); 
					} ?>
	                            </div>

	                       <?php
	                       }
	                       ?>
	                            <div class="col-md-8">
	                            	<table  id="list-order-details" class="table table-striped" style="font-size:18px;">
								    <thead>
								      <tr>
								        <th><?php echo $langs->trans('EAN'); ?></th>
								        <th><?php echo $langs->trans('Product'); ?></th>
								        <th><?php echo $langs->trans('QtyAlreadyOrdered'); ?></th>
								        <th><?php echo $langs->trans('QtyScannedToAdd'); ?></th>
								        <th>&nbsp;</th>
								      </tr>
								    </thead>
								    <tbody>
								    </tbody>
								    </table>

	                            </div>
	                        <?php
	                    }

	                ?>
	               </div>
			  </div>
			  <div class="tab-pane" id="panel-reception">
			  		<div class="row">
			  		<?php
	                    if($conf->stock->enabled){

							if(!empty($conf->global->POPPY_RETRICT_TO_ONE) && $fk_reception_selected>0) {
								$object = new CommandeFournisseur($db);
								$object->fetch($fk_reception_selected);
								echo '<h1>'.$object->ref.'</h1>';
							}
							else {
						    ?>
	                            <!-- Affichage de l'onglet "Réception" -->

	                            <div class="col-md-4">
	                            	<?php if(empty($conf->global->POPPY_RETRICT_TO_ONE) || $fk_reception_selected>0) {
						require('./tpl/reception.php');
						} ?>
	                            </div>

	                       <?php
	                       }
	                       ?>
	                            <div class="col-md-8">
	                            	<table  id="list-reception-details" class="table table-striped" style="font-size:18px;">
								    <thead>
								      <tr>
								        <th><?php echo $langs->trans('EAN'); ?></th>
								        <th><?php echo $langs->trans('Product'); ?></th>
								        <th><?php echo $langs->trans('QtyToRecept'); ?></th>
								        <th><?php echo $langs->trans('QtyScanned'); ?></th>
								        <th>&nbsp;</th>
								      </tr>
								    </thead>
								    <tbody>
								    </tbody>
								    </table>

	                            </div>
	                        <?php
	                    }

	                ?>
	               </div>
			  </div>
			</div>
			<script type="text/javascript">
			<?php
		    if(!empty($conf->global->POPPY_RETRICT_TO_ONE)) {
		    	if($fk_shipping_selected>0) {
		    		echo '$("#panel-expedition").addClass("active");$("#panel-reception").removeClass("active");$("#panel-order").removeClass("active");';
		    	}
				elseif($fk_reception_selected>0){
					echo '$("#panel-reception").addClass("active");$("#panel-expedition").removeClass("active");$("#panel-order").removeClass("active");';
				}
				elseif($fk_order_selected>0){
				    echo '$("#panel-order").addClass("active");$("#panel-expedition").removeClass("active");$("#panel-reception").removeClass("active");';
				}
		    }


		    ?>
		    </script>

			<?php require('./tpl/popup.php'); ?>
		<div class="floating-buttons">
			<?php if ($fk_shipping_selected && !empty($conf->global->POPPY_CAN_APPLY_THE_QTY_SCANNED)) { ?>
				<button type="button" class="btn btn-default btn-circle btn-xl glyphicon glyphicon-check" onclick="_apply_qty();" id="codeflag_apply_qty" data-toggle="tooltip" data-placement="top"  title="<?php echo $langs->trans('applyQtyHelp'); ?>"></button>
			<?php }
			else if ($fk_order_selected>0) {
			?>
				<button type="button" class="btn btn-default btn-circle btn-xl glyphicon glyphicon-check" onclick="_apply_order_qty();" id="codeflag_apply_qty" data-toggle="tooltip" data-placement="top"  title="<?php echo $langs->trans('applyQtyOrderHelp'); ?>"></button>
			<?php
			}
			?>
			<button type="button" class="btn btn-default btn-circle btn-xl glyphicon glyphicon-plus" onclick="_focus_barcode();" id="codeflag" data-toggle="tooltip" data-placement="top"  title="<?php echo $langs->trans('addHelp'); ?>"></button>
			<button type="button" class="btn btn-default btn-circle btn-xl glyphicon glyphicon-trash" onclick="_focus_barcode_delete();" id="codeflagdelete" data-toggle="tooltip" data-placement="top"  title="<?php echo $langs->trans('removeHelp'); ?>"></button>
			<?php

				if($fk_reception_selected>0) {

					echo '<button type="button" class="btn btn-success btn-circle btn-xl glyphicon glyphicon-thumbs-up" onclick="_getQuantityToReception();" id="codeflagreception" data-toggle="tooltip" data-placement="top" title="'.$langs->trans('receptionDoneHelp').'"></button>';

				}

				$parameters=array('fk_shipping_selected'=>$fk_shipping_selected,'fk_reception_selected'=>$fk_reception_selected,'fk_order_selected'=>$fk_order_selected);$action='';
				$hookmanager->executeHooks('addMoreActionsPoppy',$parameters, $object,$action);
			?>
			<div style="position:absolute; top:-500px; left: -500px; overflow:hidden;width:1px;height:1px; ">
				<textarea id="codereader" cols="100" rows="10" onKeyPress="enterpressalert(event, this)"></textarea>
				<textarea id="codereaderDelete" cols="20" rows="2" onKeyPress="enterpressalert(event, this)"></textarea>
			</div>
			<script type="text/javascript">
			$(document).ready(function() {
				$('button[data-toggle="tooltip"]').tooltip();

				$('#codereader').focus(function(){
					$('#codeflag').addClass('activate');
				});

				$('#codereader').blur(function(){
					$('#codeflag').removeClass('activate');
				});

				$('#codereaderDelete').focus(function(){
					$('#codeflagdelete').addClass('activate');
				});

				$('#codereaderDelete').blur(function(){
					$('#codeflagdelete').removeClass('activate');
				});

				<?php
				if($fk_shipping_selected>0) {
					echo 'setShipping('.$fk_shipping_selected.');';
				}
				else if($fk_reception_selected>0){
					echo 'setReception('.$fk_reception_selected.');';
				}
				else if($fk_order_selected>0){
				    echo 'setOrder('.$fk_order_selected.');';
				}

				?>

				controlQty();

				_focus_barcode();
			});


			</script>
		</div>
		</div>


		<?php dol_include_once('/poppy/js/fonctions.js.php'); ?>
		<script type="text/javascript">
			_focus_barcode();
		</script>
	</body>
</html>
