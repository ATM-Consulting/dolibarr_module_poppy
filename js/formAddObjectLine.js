$(document).ready(function() {
	$('#search_idprod').on('keypress', function( e ) {
		if(e.which == 13) {
			alert('You pressed enter!');
			e.preventDefault();
			return false;
		}
	});
});
