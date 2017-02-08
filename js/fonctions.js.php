<script type="text/javascript">

$(window).resize(function() {
	resizeAll();
});

$(document).ready(function( event, ui ) {
	resizeAll();
	checkLoginStatus();
	$('.tab-content').on('click',"[name*=addOneProduct]",function() {
		//console.log($("#codereader"), $(this).attr('barcode'));
		
		$('[lastClicked]').removeAttr('lastClicked');
		$(this).closest('tr').attr('lastClicked',1);
		
		$("#codereader").val($(this).attr('barcode'));
		
		var e = $.Event('keypress');
	    e.which = 13; // Character 'Enter'
	    $('#codereader').trigger(e);
	
	});
	$('.tab-content').on('click',"[name*=delOneProduct]",function() {
		//console.log($("#codereader"), $(this).attr('barcode'));
		$('[lastClicked]').removeAttr('lastClicked');
		$(this).closest('tr').attr('lastClicked',1);
		
		$("#codereaderDelete").val($(this).attr('barcode'));
		
		var e = $.Event('keypress');
	    e.which = 13; // Character 'Enter'
	    $('#codereaderDelete').trigger(e);
	
	});
} );

var fk_shipping_selected = 0;
var fk_reception_selected = 0;

var scan_mode = "shipping";

function setShipping(id) {
	fk_shipping_selected = id;
	scan_mode = "shipping";
	
	$("#search_shipping").val(id);
	$('ul#list-shipping li').removeClass('active');
	$('ul#list-shipping li[exp-id='+id+']').addClass('active');
	$('#codereader').val('');
	reload_list_shipping_details(id);
	
}
function setReception(id) {
	fk_reception_selected = id;
	
	scan_mode = "reception";
	
	$("#search_reception").val(id);
	$('ul#list-reception li').removeClass('active');
	$('ul#list-reception li[reception-id='+id+']').addClass('active');
	$('#codereader').val('');
	reload_list_reception_details(id);
	
}


function changeUser(fk_user) {
	
	$("#search_user").val(fk_user);
	$("#user-name").html( $('li[user-id='+fk_user+']').attr('login') );
	reload_liste_tache('user');
	reload_liste_tache('workstation');
	reload_liste_of();
	
}


function resizeAll() {
	
	
	var doc_width = $(window).width();
	var doc_height = $(window).height();
	
	if(doc_width>768) {
		
		nb_user = $('#select-user-list>li').length;
	
		if(nb_user>10) {
			
			if(doc_width>800 && nb_user>30) {
				$('#select-user-list').width( 600 );
				$('#select-user-list>li').removeClass('col-md-6').addClass('col-md-4').width(160);
			}
			else if(doc_width>500) {
				$('#select-user-list').width( 400 );
				$('#select-user-list>li').removeClass('col-md-4').addClass('col-md-6').width(160);
			}
		}
		
		if($('#select-user-list').height()>doc_height) {
			$('#select-user-list').css('overflow-y', 'scroll').height( doc_height - 200 );
		}
		
	}
	else {
		$('#select-user-list').css('height', null).css('overflow-y',null);
	}
}

function aff_popup(id_task,onglet,action){
	
	$("#confirm-add-time").modal({show: true});
	
	timespent = getTimeSpent(id_task,action);
	TTime = timespent.split(":");
	hour = TTime[0];
	minutes = TTime[1];
	$('#heure').val(hour);
	$('#minute').val(minutes);
	
	$('#valide_popup').unbind().click(function(event, ui){
		
		hour = $('#heure').val();
		minutes = $('#minute').val();
		
		if(action == 'stop'){
			stop_task(id_task,onglet,hour,minutes);
		}
		else{
			close_task(id_task,onglet,hour,minutes);
		}
		
		$('#confirm-add-time').modal('hide');
	});
	
}

function reload_list_reception_details(id) {
	$t = $('#list-reception-details>tbody');
	$t.empty();
	
	$.ajax({
		url: "script/interface.php",
		dataType: "json",
		crossDomain: true,
		async : false,
		data: {
			   get:'reception-details'
			   ,json : 1
			   ,id: id
		}
	})
	.then(function (data){
		//console.log(data);
	
		for(x in data) {
			obj = data[x];
			
			barcodef = '';
			for(y in obj.TSupplierPrice) {
				barcodef+=obj.TSupplierPrice[y].fourn_ref+',';
			}
			
			$t.append('<tr ref="'+obj.ref+'" fk-line="'+obj.id+'" fk-product="'+obj.fk_product+'" barcode="'+obj.barcode+'" barcodef="'+barcodef+'"><td rel="ean">'+(obj.barcode ? obj.barcode : obj.ref)+'</td><td rel="label">'+obj.product_label+'</td><td rel="toTest">'+obj.qty_receive+'</td><td rel="scanned">0</td><td class="state"><span class="glyphicon glyphicon-alert"></span></td></tr>');
		}

	});
}

function reload_list_shipping_details(id) {
	$t = $('#list-expedition-details>tbody');
	$t.empty();
	
	$.ajax({
		url: "script/interface.php",
		dataType: "json",
		crossDomain: true,
		async : false,
		data: {
			   get:'shipping-details'
			   ,json : 1
			   ,id: id
		}
	})
	.then(function (data){
		//console.log(data);
	
		for(x in data) {
			obj = data[x];
			if (obj.product.array_options['options_emplacement'] == null || obj.product.array_options['options_emplacement'] == 0) console.log('Extrafield "Adressage" non renseigné sur la fiche produit ['+obj.product.id+']');
			$t.append('<tr ref="'+obj.ref+'" barcode="'+obj.barcode+'"><td rel="ean">'+(obj.barcode ? obj.barcode : obj.ref)+'</td><td rel="label">'+obj.product_label+'</td><td rel="emplacement">'+obj.entrepot_name+'</td><td rel="qte_dispo">'+obj.product.stock_warehouse[obj.product.array_options['options_emplacement']].real+'</td><td rel="qty_asked">'+obj.qty_asked+'</td><td rel="toTest">'+obj.qty_shipped+'</td><td rel="scanned">0</td><td class="state"><span class="glyphicon glyphicon-alert"></span></td><td><button class="glyphicon glyphicon-minus btn-default" name="delOneProduct' + obj.fk_origin_line + '" type="button" value="-" barcode="'+(obj.barcode ? obj.barcode : obj.ref)+'" /></td><td><input class="glyphicon btn-default" name="addOneProduct' + obj.fk_origin_line + '" type="button" value="+" barcode="'+(obj.barcode ? obj.barcode : obj.ref)+'" /></td></tr>');
		}

	});
	
}


function _focus_barcode_delete() {
	
	$('#codereaderDelete').focus();
}

function _focus_barcode() {
	console.log('_focus_barcode');
	$('#codereader').focus();
}
function enterpressalert(e, textarea){
	var code = (e.keyCode ? e.keyCode : e.which);
	if(code == 13) { //Enter keycode
		refreshListStatus();
	}
}

function lessRefLine(ref,qty) {
	console.log('lessRefLine', ref, qty);
	
	if(!qty) qty = 1;
	
	$tr = null;
	
	$('body').find(getScanPattern(ref)).each(function(i,item) {
		var $item = $(item);
		
		if($item.attr('lastclicked')) {
			$tr = $item;
			return false;
		}
	});

	if(!$tr) {
		$('body').find(getScanPattern(ref)).each(function(i,item) {
			
			var $item = $(item);
			
			qty_to_test = parseInt( $item.find('td[rel="toTest"]').text() );
			qty_scan = parseInt( $item.find('td[rel="scanned"]').text() );
			
			if(qty_to_test>qty_scan) {
				$tr = $item;
				return false;
			}
			
		}); // récupère le 1er avec code barre ou ref
		
	}	 
		
	if(!$tr) $tr = $('body').find(getScanPattern(ref)).first();
	
	
	if($tr.length>0) {
	
		qty = parseInt( $tr.find('td[rel="scanned"]').text() ) - qty;
		
		if(qty>=0) {
			updateQtyLine($tr, qty);	
		}
		
	}
	else{
		console.log('lessRefLine::notExisteCheckPackage', ref, qty);
		
		$.ajax({
			url: "script/interface.php",
			dataType: "json",
			crossDomain: true,
			data: {
				get:'mistake-data'
				,ref:ref
			}
			
		}).done(function(data) {
			
			if(data.isPackage>0) {
				
				for(refSP in data.TProduct) {
					qtySP = parseFloat(data.TProduct[refSP]);
					console.log('appel addLine', refSP, qtySP);
					lessRefLine(refSP, qtySP*qty);
				}
				
			}
			
			controlQty();
		});
		
	}
}

function updateQtyLine($tr, qty) {
	$tr.find('td[rel="scanned"]').text(qty);
	qtytoTest = parseInt( $tr.find('td[rel="toTest"]').text() ) ;
	
	if(!$tr.hasClass('mistake')) {
		$tr.removeClass();
		if(qty<qtytoTest) {
			$tr.addClass('needMore');
		}
		else if(qty>qtytoTest) {
			$tr.addClass('tooMuch');
		}
		else if(qty == qtytoTest) {
			$tr.addClass('goodQty');
		}
		console.log(qty,qtytoTest);
		
	}
					
}

function getScanPattern(ref) {
	if(scan_mode == "reception") {
		return '#list-reception-details tr[barcode="'+ref+'"],tr[ref='+ref+'],tr[barcodef*="'+ref+',"]';
	}
	else{
		return '#list-expedition-details tr[barcode="'+ref+'"],tr[ref="'+ref+'"]';
	}
	
}

function addRefLine(ref, qty) {
	console.log('addRefLine', ref, qty);
	
	if(!qty) qty = 1;
	
	
	$tr = null;
	
	$('body').find(getScanPattern(ref)).each(function(i,item) {
		var $item = $(item);
		
		if($item.attr('lastclicked')) {
			$tr = $item;
			return false;
		}
	});

	if(!$tr) {
		$('body').find(getScanPattern(ref)).each(function(i,item) {
			
			var $item = $(item);
			
			qty_to_test = parseInt( $item.find('td[rel="toTest"]').text() );
			qty_scan = parseInt( $item.find('td[rel="scanned"]').text() );
			
			if(qty_to_test>qty_scan) {
				$tr = $item;
				return false;
			}
			
		}); // récupère le 1er avec code barre ou ref
		
	}	 
		
	if(!$tr) $tr = $('body').find(getScanPattern(ref)).first();
		
	if($tr.length>0) {
		console.log('lineExist', ref);	
		qty = parseInt( $tr.find('td[rel="scanned"]').text() ) + qty;
		
		var res = updateQtyLine($tr, qty);
	}
	else{
		console.log('mistake',ref);
		var $trMistake = $('<tr class="mistake" barcode="'+ref+'" class="mistake"><td rel="ean">'+ref+'</td><td rel="label">'+ref+'</td><td>0</td><td rel="scanned">'+qty+'</td><td><span class="glyphicon glyphicon-question-sign"></td></tr>');
		$t.append($trMistake);
		
		$.ajax({
			url: "script/interface.php",
			dataType: "json",
			crossDomain: true,
			data: {
				get:'mistake-data'
				,ref:ref
			}
			
		}).done(function(data) {
			
			if(data.isPackage>0) {
				
				for(refSP in data.TProduct) {
					qtySP = parseFloat(data.TProduct[refSP]);
					console.log('appel addLine', refSP, qtySP);
					addRefLine(refSP, qtySP*qty);
				}
				
				$trMistake.remove();
			
			}
			else{
				$trMistake.find('[rel=label]').html(data.label);
				
			}
			controlQty();
		});
		
	}
}

function refreshListStatus() {
	
	var TCode = $('#codereader').val().split("\n");
	
	var TDelete = $('#codereaderDelete').val().split("\n");
	
	for(d in TDelete) {
		refDelete = TDelete[d];
		if(!refDelete) continue;
		lessRefLine(refDelete,1);
	}
	
	$('#codereaderDelete,#codereader').val('');
	
	var codes = "";
	
	for(x in TCode) {
		ref = TCode[x];
		
		if(!ref) continue;

		codes+=ref+"\n";
	
		addRefLine(ref);
		
	}
	
//	$('#codereader').val(codes);
	controlQty();
	
}

function _getQuantityToReception() {
	
	$t = $('#list-reception-details>tbody');
	$t.find('tr').each(function(i,item) {
		$tr = $(item);
		qtyScanned = parseInt( $tr.find('td[rel="scanned"]').text() ) ;
		fk_line = $tr.attr('fk-line');
		
		if(qtyScanned>0)window.parent.$("input[name='TOrderLine["+fk_line+"\][qty]']").val(qtyScanned).css({ 'background-color' : '#5cb85c'});
		
		
	});
	
	window.parent.$("#popPoppy").dialog("close");
	
}

function controlQty() {
	
	if(scan_mode == 'reception') {
		$t = $('#list-reception-details>tbody');
	}
	else{
		$t = $('#list-expedition-details>tbody');
	}
	
	
	var ok = true;
	$t.find('tr').each(function(i,item) {
		$tr = $(item);
		
		if($tr.hasClass('mistake') && parseInt($tr.find('[rel=scanned]').text()) == 0) {
			$tr.remove();	
		}
		else{
			qtyToTest = parseInt( $tr.find('td[rel="toTest"]').text() ) ;
			qtyScanned = parseInt( $tr.find('td[rel="scanned"]').text() ) ;
			
			if($tr.hasClass('tooMuch') || $tr.hasClass('needMore') || $tr.hasClass('mistake') || qtyToTest!=qtyScanned) {
				ok = false;	
			}
		}
		
		
	});
		console.log('controlQty',ok);
	if(ok) {
		
		isPrepared = 1;
		$('[control-poppy=ifok]').show();	
	}
	else{
		isPrepared = 0;
		
		$('[control-poppy=ifok]').hide();
	}
	
	
	if(scan_mode == 'shipping') {
	
		$.ajax({
				url: "script/interface.php",
				dataType: "json",
				crossDomain: true,
				data: {
					put:'shipping-prepared'
					,isPrepared:isPrepared
					,fk_shipping:fk_shipping_selected
				}
				
		});
	
	}
	
	<?php if(!empty($conf->global->POPPY_ADD_BUTTON_ON_DRAFT_SHIPPING) && !empty($conf->global->POPPY_SEND_ON_SHIPPING_VALIDATION_CARD_IF_ALL_SHIPPED)) { ?>
	if(ok) {
		window.parent.location.href='<?php echo dol_buildpath('/expedition/card.php', 2).'?action=valid&id='; ?>'+fk_shipping_selected;
	}
	<?php } ?>
	
}

function checkLoginStatus() {
	
	$.ajax({
		url: "script/interface.php",
		dataType: "html",
		crossDomain: true,
		data: {
			get:'logged-status'
		}
	})
	.then(function (data){
		
		if(data!='ok') {
			document.location.href = document.location.href; // reload car la session est expirée		
		}
		else {
			setTimeout(function() {
				checkLoginStatus();
			}, 30000);
		}
		
	});

}

</script>