<?php

	require('config.php');
	dol_include_once('/user/class/usergroup.class.php');
	dol_include_once('/core/lib/date.lib.php');
	dol_include_once('/expedition/class/expedition.class.php');
	
	/*if (!($user->admin || $user->rights->tasklist->all->read)) {
    	accessforbidden();
	}
	*/
	
	$langs->load('poppy@poppy');

	$fk_shipping_selected = GETPOST('fk_shipping');

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
			if(!empty($conf->global->POPPY_RETRICT_TO_ONE) && $fk_shipping_selected>0) {
				null;	
			}
			else{
				require('./tpl/onglet.php');	
			}
			 
			?>
			<!-- Tab panes -->
			<div class="tab-content">
			  <div class="tab-pane active" id="list-expedition">
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
	                            <!-- Affichage de l'onglet "Postes de travail" -->
	                            
	                            <div class="col-md-4">
	                            	<?php require('./tpl/expedition.php'); ?>
	                            </div>
	                            
	                       <?php
	                       }
	                       ?>
	                            <div class="col-md-8">
	                            	<table  id="list-expedition-details" class="table table-striped" style="font-size:18px;">
								    <thead>
								      <tr>
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
			  
			</div>
		    
			<?php require('./tpl/popup.php'); ?>
		<div class="floating-buttons">	
			<button type="button" class="btn btn-default btn-circle btn-xl glyphicon glyphicon-plus" onclick="_focus_barcode();" id="codeflag" data-toggle="tooltip" data-placement="top"  title="<?php echo $langs->trans('addHelp'); ?>"></button>
			<button type="button" class="btn btn-default btn-circle btn-xl glyphicon glyphicon-trash" onclick="_focus_barcode_delete();" id="codeflagdelete" data-toggle="tooltip" data-placement="top"  title="<?php echo $langs->trans('removeHelp'); ?>"></button>
			<?php
				$parameters=array('fk_shipping_selected'=>$fk_shipping_selected);$action='';
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
				?>

				controlQty();

				_focus_barcode();	
			});
			
			
			</script>
		</div>
		</div>
		
		
		<script src="js/fonctions.js" type="text/javascript"></script>
		<script type="text/javascript">
			_focus_barcode();
		</script>
	</body>
</html>