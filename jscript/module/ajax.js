function checkName()
{
    $(document).ready(function() {
        var module 	= $('#name').val();
		var regex 	= /^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/;

		if (!module.match(regex))
			$('#name_elmError').html("<span style='color:red'>alphanumeric only, use underscore to separate words!</span>");
		else if (module.length < 2)
			$('#name_elmError').html("<span style='color:red'>too short, use at least 2 characters!</span>");
		else {
			$('#name_elmError').html('checking availability ...');

			var url = ROOT_URL + '/module/validate/' + module;

			$.ajax({
				type : 'GET',
				url : url,
				dataType : 'HTML',
				success : function(data) {
					$('#name_elmError').html(data);
				},
				error : function(XMLHttpRequest, textStatus, errorThrown) {
					alert('Transport error');
				}
			});
		}
    });
}