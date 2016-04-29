<textarea id="codereader" cols="100" rows="10" onKeyPress="enterpressalert(event, this)"></textarea>
<textarea id="codereaderDelete" cols="20" rows="2" onKeyPress="enterpressalert(event, this)"></textarea>
<script type="text/javascript">
$(document).ready(function() {
	$('#codereader').focus(function(){
		$('#codeflag').css({
			'background-color':'#33ff33'
		});
	});
	
	$('#codereader').blur(function(){
		$('#codeflag').css({
			'background-color':'orange'
		});
	});
	
	_focus_barcode();	
});
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
</script>
<div id="confirm-add-time"  class="modal" role="dialog">
    <div class="modal-dialog">
	    <div class="modal-content">
	    	<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Confirmez le temps passé</h4>
		    </div>
		    <div class="modal-body">
		        <form class="form-inline">
		        	
		        	<div class="form-group">
		              <?php if ($user->rights->tasklist->all->write){ ?>
		              	
		              	 <div class="input-group">
				              <input id="heure" value="" placeholder="H" class="form-control" type="text" /> 
				              <span class="input-group-addon" style="width:50%;" id="heure-addon1">Heure(s)</span>
			             </div>
			             <div class="input-group">
			              <input id="minute" value="" placeholder="m" class="form-control" type="text" /> 
			              <span class="input-group-addon" style="width:50%;" id="minute-addon" >Minute(s)</span>
			             </div>
		              <?php } else{ ?>
		              	
		              	 <div class="input-group ">
				              <input id="heure" value="" placeholder="H" class="form-control" type="text"  disabled="disabled" /> 
				              <span class="input-group-addon" style="width:50%;" id="heure-addon1">Heure(s)</span>
			             </div>
			             <div class="input-group ">
				              <input id="minute" value="" placeholder="m" class="form-control" type="text" disabled="disabled" /> 
				              <span class="input-group-addon" style="width:50%;" id="minute-addon" >Minute(s)</span>
			             </div>
		              	
		              <?php } ?>
		              
		               
		             
		             
		             
		             
		            </div>
		        </form> 
		       </div>
		       <div class="modal-footer">
		       	<a class="btn btn-default" id="valide_popup">Valider</a>
		       	</div>  
	    </div>
    </div>
</div>
