$("#model").change(function(){
	if(0 == $(this).val()){
		$('.model_box').hide();
		return;
	}
	$.getJSON(getmodelinfourl,{'id':$(this).val()},function(data){

		if(data){
			$('.model_box').show();
			var modelRowHtml = template('modelRowTemplate', {'templateData': data});
			$('#modelBaseBody').html(modelRowHtml); 
		}
	})
});
