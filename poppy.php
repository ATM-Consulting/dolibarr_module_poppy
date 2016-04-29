<?php

	require('config.php');
	dol_include_once('/user/class/usergroup.class.php');
	dol_include_once('/core/lib/date.lib.php');
	
	/*if (!($user->admin || $user->rights->tasklist->all->read)) {
    	accessforbidden();
	}
	*/
	

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
			<?php require('./tpl/onglet.php'); ?>
			<!-- Tab panes -->
			<div class="tab-content">
			  <div class="tab-pane active" id="list-expedition">
			  		<div class="row">
			  		<?php 
	                    if($conf->expedition->enabled && $user->rights->expedition->lire){
	                        ?>
	                            <!-- Affichage de l'onglet "Postes de travail" -->
	                            <div class="col-md-4">
	                            	<?php require('./tpl/expedition.php'); ?>
	                            </div>
	                            <div class="col-md-8">
	                            	<table  id="list-expedition-details" class="table table-striped">
								    <thead>
								      <tr>
								        <th>Product</th>
								        <th>QtyToShiping</th>
								        <th>QtyScanned</th>
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
		</div>
		<script src="js/fonctions.js" type="text/javascript"></script>
	</body>
</html>