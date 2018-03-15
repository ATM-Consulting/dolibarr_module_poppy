<nav class="navbar navbar-fixed-top navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-poppy" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand hidden-sm hidden-md hidden-lg" href="#">Menu</a>
	    </div>

		<div class="collapse navbar-collapse " id="menu-poppy">
		    <ul class="nav navbar-nav" role="tablist">

			  <?php  if(!empty($conf->expedition->enabled) && !empty($user->rights->expedition->lire)){ ?><li><a href="#panel-expedition" role="tab" data-toggle="tab"><?php echo $langs->trans('Shipping'); ?></a></li><?php } ?>
			  <?php  if(!empty($conf->stock->enabled)){ ?><li><a href="#panel-reception" role="tab" data-toggle="tab"><?php echo $langs->trans('Reception'); ?></a></li><?php } ?>
			  <?php  if(!empty($conf->commande->enabled) && !empty($user->rights->commande->creer) && !empty($conf->global->POPPY_ALLOW_TO_PREPARE_ORDER)){ ?><li><a href="#panel-order" role="tab" data-toggle="tab"><?php echo $langs->trans('Orders'); ?></a></li><?php } ?>
			</ul>

			<ul class="nav navbar-nav navbar-right">
					<li>
					<?php
					if($user->rights->poppy->user->read) {
					?>
									<div class="button-group">
								        <a type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <span id="user-name"><?php echo $user->login ?></span> <span class="caret"></span></a>
										<input id="search_user" type="hidden" value="<?php echo $user->id ?>" />

											<ul class="dropdown-menu" id="select-user-list">
					<?php
						global $conf;

						$sql = "SELECT DISTINCT u.rowid,u.login
								FROM ".MAIN_DB_PREFIX."user as u
								LEFT JOIN ".MAIN_DB_PREFIX."usergroup_user as uu ON (uu.fk_user = u.rowid)
								WHERE u.statut = 1 AND u.entity IN (0,".$conf->entity.")
								ORDER BY login";

						$TUser = $PDOdb->ExecuteAsArray($sql);
						foreach($TUser as $obj) {
							echo '<li class="btn" login="'.$obj->login.'" user-id="'.$obj->rowid.'" onclick="changeUser('.$obj->rowid.')">'. $obj->login .'</li>';
						}

						?>
										</ul>

								</div>
		<?php
		}
		else {
			echo '<p class="navbar-text navbar-right"><span class="glyphicon glyphicon-user"></span> <span id="user-name">'.$user->login.'&nbsp;</span></p>';
		}
		?>

					</li>
			</ul>
		</div>
	</div>
</nav>