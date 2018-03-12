<?php

	//Récupération de la liste des expédition

	$sql="SELECT rowid, ref FROM ".MAIN_DB_PREFIX."commande WHERE entity IN (".getEntity('commande',1).") AND fk_statut IN (0)";
	$TOrder = TRequeteCore::_get_id_by_sql($PDOdb, $sql,'ref','rowid');

?><input type="hidden" id="search_order" value="" />
<ul class="list-group" id="list-order"><?php

    foreach($TOrder as $id=>$label) {

		echo '<li class="list-group-item" order-id="'.$id.'" onclick="javascript:setOrder('.$id.')"><a href="#">'.$label.'</a></li>';

	}

?></ul><?php

