<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_poppy.class.php
 * \ingroup poppy
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsPoppy
 */
class ActionsPoppy
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf;

		$error = 0; // Error counter

		if (in_array('receptionstockcard', explode(':', $parameters['context']))) {
			global $langs;
			$langs->load('poppy@poppy');

			if ($object->statut == 3 || $object->statut == 4 || $object->statut == 5) {

			$res = $hookmanager->executeHooks('addMoreActionsPoppyPopup',$parameters,$object,$action);
			$TButton = array();
			if(!empty($hookmanager->resArray)) {
				$TButton = $hookmanager->resArray;
			}

			$buttons = json_encode($TButton);

			?>
		  	<script type="text/javascript">
		  	$(document).ready(function() {
			  	$a = $('<a href="javascript:popPoppy()" class="butAction"><?php echo $langs->trans('btScannet') ?></a>');
			  	$('tr.liste_titre td[rel=QtyToDispatchShort]').first().append($a);
		  	});

		  	function popPoppy() {

		  		$("#popPoppy").remove();
		  		$div = $('<div id="popPoppy"><iframe width="100%" height="100%" frameborder="0" src="<?php echo dol_buildpath('/poppy/poppy.php?fk_reception='.$object->id,1) ?>"></iframe></div>');

				$('body').append($div);

				$("#popPoppy").dialog({
					modal:true
					,width:"90%"
					,height:$(window).height() - 50
					,buttons:<?php echo $buttons ?>
				});

		  	}

		  	</script>
		  	<?php

			}
		}
        else if (in_array('expeditioncard', explode(':', $parameters['context'])))
		{
			global $langs;

			$langs->load('poppy@poppy');
			if ($object->statut==1 && empty($conf->global->POPPY_ADD_BUTTON_ON_DRAFT_SHIPPING)
				|| empty($object->statut) && !empty($conf->global->POPPY_ADD_BUTTON_ON_DRAFT_SHIPPING)) {

				$res = $hookmanager->executeHooks('addMoreActionsPoppyPopup',$parameters,$object,$action);
				$TButton = array();
				if(!empty($hookmanager->resArray)) {
					$TButton = $hookmanager->resArray;
				}

				$buttons = json_encode($TButton);

			  	?>
			  	<script type="text/javascript">

			  	function popPoppy() {
			  		$div = $('<div id="popPoppy"><iframe width="100%" height="100%" frameborder="0" src="<?php echo dol_buildpath('/poppy/poppy.php?fk_shipping='.$object->id,1) ?>"></iframe></div>');

					$div.dialog({
						modal:true
						,width:"90%"
						,height:$(window).height() - 50
						,buttons:<?php echo $buttons ?>
						<?php if (!empty($conf->global->POPPY_CAN_APPLY_THE_QTY_SCANNED)) { ?>
						,close: function(event, ui) {
							window.location.href = window.location.pathname + window.location.search + window.location.hash;
						}
						<?php } ?>
					});

			  	}

			  	</script>
			  	<?php
			}
		}

		else if (in_array('ordercard', explode(':', $parameters['context'])))
		{
		    global $langs;

		    $langs->load('poppy@poppy');

		    if ($object->statut==0) { //onkly draft order

		            $res = $hookmanager->executeHooks('addMoreActionsPoppyPopup',$parameters,$object,$action);
		            $TButton = array();
		            if(!empty($hookmanager->resArray)) {
		                $TButton = $hookmanager->resArray;
		            }

		            $buttons = json_encode($TButton);

		            ?>
			  	<script type="text/javascript">
			  	$(document).ready(function() {
				  	$a = $('<a href="javascript:popPoppy()" class="butAction"><?php echo $langs->trans('ScanProductToOrder') ?></a>');
				  	$('div.fiche div.tabsAction').first().append($a);
			  	});

			  	function popPoppy() {
			  		$div = $('<div id="popPoppy"><iframe width="100%" height="100%" frameborder="0" src="<?php echo dol_buildpath('/poppy/poppy.php?fk_order='.$object->id,1) ?>"></iframe></div>');

					$div.dialog({
						modal:true
						,width:"90%"
						,height:$(window).height() - 50
						,buttons:<?php echo $buttons ?>
						,close: function(event, ui) {
							addPoppyToOrder();
						}

					});

			  	}

			  	function addPoppyToOrder() {
console.log('addPoppyToOrder');


			  	}

			  	</script>
			  	<?php
			}

		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
	}

	/**
	 * Inclusion d'un JS sur le formulaire d'ajout de ligne pour permettre l'ajout de ligne via un scan douchette
	 */
	function formAddObjectLine($parameters, &$object, &$action, $hookmanager) {
		global $conf;
		?>
		<script type="text/javascript">

			$(document).ready(function() {
				$('#search_idprod').on('keypress', function( e ) {
					if(e.which == 13) {

						poppySelectProd($('#search_idprod').val());

						e.preventDefault();
						return false;
					}
				});
			});

			function poppySelectProd(id_prod) {

				$.ajax({
					url:"<?php echo dol_buildpath('/product/ajax/products.php',1); ?>"
					,data:{
						htmlname:"idprod"
						,outjson:1
						,price_level:<?php echo (int)$object->thirdparty->price_level ?>
						,type:""
						,mode:1
						,status:1
						,finished:2
						,idprod:id_prod
						,
					}
					,dataType:"json"
				}).done(function(data) {
					console.log(data);
					$('#idprod').val(data[0].key);
					$('#idprod').change();
					<?php
					if(!empty($conf->global->POPPY_GO_TO_QTY_AFTER_SELECT_PRODUCT)) {
						echo '$("#qty").focus().select();';
					}
					else{
						echo 'window.setTimeout(function() { $("#addline").click() }, 300);';
					}

					?>
				});

			}

		</script>
		<?php
	}

	function addMoreActionsButtons($parameters, $object, $action, $hookmanager){

		global $langs, $conf;

		if (in_array('expeditioncard', explode(':', $parameters['context'])))
		{
			//Ajout bouton "Préparer le colis"
			if ($object->statut==1 && empty($conf->global->POPPY_ADD_BUTTON_ON_DRAFT_SHIPPING)
				|| empty($object->statut) && !empty($conf->global->POPPY_ADD_BUTTON_ON_DRAFT_SHIPPING)) {

				print '<a href="javascript:popPoppy()" class="butAction">' . $langs->trans('PreparePackage') . '</a>';

			}
		}
	}
}
