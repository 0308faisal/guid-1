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
			document.location.reload();
		}
	});

	$('#inviteform').ajaxForm(
	{
		beforeSubmit: function() {

		},
		success: function(data) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			alert(status+": "+data.response);
			document.location.reload();
		}
	});

	$( "#membertable tbody" ).on( "click", ".statusupdate", function() {
		if(confirm("Do you wish to update the status of this user?")){
			$.get( "/rest_v2/guideapi/updatestatus?token="+$('#token').val()+"&networkid="+$('#networkid').val()+"&id="+$(this).parent().parent().find('.idfield').html().trim()+"&status="+$(this).parent().find('.statusselect').val(), function( data ) {
				status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
					return letter.toUpperCase();
				});
				alert(status+": "+data.response);
				document.location.reload();
			});
		}
	});

});
