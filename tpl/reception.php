<?php

	//Récupération de la liste des expédition
	
	$sql="SELECT rowid, ref FROM ".MAIN_DB_PREFIX."commande_fournisseur WHERE entity IN (".getEntity('supplierorder',1).") AND fk_statut IN (3,4,5)";
	$TReception = TRequeteCore::_get_id_by_sql($PDOdb, $sql,'ref','rowid');
	
?><input type="hidden" id="search_reception" value="" />
<ul class="list-group" id="list-reception"><?php

	foreach($TReception as $id=>$label) {
		
		echo '<li class="list-group-item" reception-id="'.$id.'" onclick="javascript:setReception('.$id.')"><a href="#">'.$label.'</a></li>';
		
	}

?></ul><?php

	