function getTask()
{
    $(document).ready(function() {

		var module 	= $('#module').val();

		var url = ROOT_URL + '/role/gettask/' + module;
		
		$.ajax({
			type : 'GET',
			url : url,
			dataType : 'HTML',
			success : function(data) {
				$('#task').html(data);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert('Transport error');
			}
		});
	});
}