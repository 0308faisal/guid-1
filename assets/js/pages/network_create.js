$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});

	$('#networkform').ajaxForm(
	{
		beforeSubmit: function() {

		},
		success: function(data) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			if (typeof data.network_id !== 'undefined') {
				$('#id').val(data.network_id);
			}
			alert(status+": "+data.response);
			window.location.href='network_management.php';
		}
	});



});
