
$(window).resize(function() {
	resizeAll();
});

$(document).ready(function( event, ui ) {
	resizeAll();
	checkLoginStatus();
	
} );

function setShipping(id) {
	
	$("#search_shipping").val(id);
	$('ul#list-shipping li').removeClass('active');
	$('ul#list-shipping li[exp-id='+id+']').addClass('active');
	
	reload_list_shipping_details(id);
	
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
			$t.append('<tr ref="'+obj.ref+'"><td>'+obj.product_label+'</td><td>'+obj.qty_shipped+'</td><td rel="scanned">0</td></tr>');
		}

	});
	
}



function refreshListStatus() {
	$t = $('#list-expedition-details>tbody');
	$t.find('[rel=scanned]').html(0);
	$t.find('tr.mistake').remove();
	
	var TCode = $('#codereader').val().split("\n");
	
	for(x in TCode) {
		ref = TCode[x];
	
		$tr = $t.find('tr[ref='+ref+']');
		
		if($tr.length>0) {
			console.log(ref);	
			qty = parseInt( $tr.find('td[rel="scanned"]').text() ) + 1 ;
			$tr.find('td[rel="scanned"]').html(qty);
		}
		else{
			console.log('mistake',ref);
			$t.append('<tr style="background-color:red;" ref="'+ref+'" class="mistake"><td>'+ref+'</td><td>0</td><td rel="scanned">1</td></tr>');
		}
		
	}
	
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
