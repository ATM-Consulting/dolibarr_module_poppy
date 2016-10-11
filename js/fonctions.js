
$(window).resize(function() {
	resizeAll();
});

$(document).ready(function( event, ui ) {
	resizeAll();
	checkLoginStatus();
	
} );

var fk_shipping_selected = 0;
var fk_reception_selected = 0;

function setShipping(id) {
	fk_shipping_selected = id;
	
	$("#search_shipping").val(id);
	$('ul#list-shipping li').removeClass('active');
	$('ul#list-shipping li[exp-id='+id+']').addClass('active');
	$('#codereader').val('');
	reload_list_shipping_details(id);
	
}
function setReception(id) {
	fk_reception_selected = id;
	
	$("#search_shipping").val(id);
	$('ul#list-shipping li').removeClass('active');
	$('ul#list-shipping li[exp-id='+id+']').addClass('active');
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
			$t.append('<tr ref="'+obj.ref+'" barcode="'+obj.barcode+'"><td rel="ean">'+(obj.barcode ? obj.barcode : obj.ref)+'</td><td rel="label">'+obj.product_label+'</td><td rel="toReceive">'+obj.qty_receive+'</td><td rel="scanned">0</td><td class="state"><span class="glyphicon glyphicon-alert"></span></td></tr>');
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
			$t.append('<tr ref="'+obj.ref+'" barcode="'+obj.barcode+'"><td rel="ean">'+(obj.barcode ? obj.barcode : obj.ref)+'</td><td rel="label">'+obj.product_label+'</td><td rel="toShip">'+obj.qty_shipped+'</td><td rel="scanned">0</td><td class="state"><span class="glyphicon glyphicon-alert"></span></td></tr>');
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
	
	$tr = $t.find('tr[barcode='+ref+'],tr[ref='+ref+']').first();
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
	qtyToShip = parseInt( $tr.find('td[rel="toShip"]').text() ) ;
	
	if(!$tr.hasClass('mistake')) {
		$tr.removeClass();
		if(qty<qtyToShip) {
			$tr.addClass('needMore');
		}
		else if(qty>qtyToShip) {
			$tr.addClass('tooMuch');
		}
		else if(qty == qtyToShip) {
			$tr.addClass('goodQty');
		}
		console.log(qty,qtyToShip);
		
	}
					
}

function addRefLine(ref, qty) {
	console.log('addRefLine', ref, qty);
	
	if(!qty) qty = 1;
	
	$tr = $t.find('tr[barcode='+ref+'],tr[ref='+ref+']').first(); // récupère le 1er avec code barre ou ref
		
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
	$t = $('#list-expedition-details>tbody');
	//$t.find('[rel=scanned]').html(0);
	
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

function controlQty() {
	$t = $('#list-expedition-details>tbody');
	
	var ok = true;
	$t.find('tr').each(function(i,item) {
		$tr = $(item);
		
		if($tr.hasClass('mistake') && parseInt($tr.find('[rel=scanned]').text()) == 0) {
			$tr.remove();	
		}
		else{
			qtyToShip = parseInt( $tr.find('td[rel="toShip"]').text() ) ;
			qtyScanned = parseInt( $tr.find('td[rel="scanned"]').text() ) ;
			
			if($tr.hasClass('tooMuch') || $tr.hasClass('needMore') || $tr.hasClass('mistake') || qtyToShip!=qtyScanned) {
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
