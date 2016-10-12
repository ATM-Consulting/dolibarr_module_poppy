<?php

	//Récupération de la liste des expédition
	
	$sql="SELECT rowid, ref FROM ".MAIN_DB_PREFIX."expedition WHERE entity IN (".getEntity('shipping',1).") AND fk_statut IN (0,1)";
	$TShipping = TRequeteCore::_get_id_by_sql($PDOdb, $sql,'ref','rowid');
	
?><input type="hidden" id="search_shipping" value="" />
<ul class="list-group" id="list-shipping"><?php

	foreach($TShipping as $id=>$label) {
		
		echo '<li class="list-group-item" exp-id="'.$id.'" onclick="javascript:setShipping('.$id.')"><a href="#">'.$label.'</a></li>';
		
	}

?></ul><?php

	