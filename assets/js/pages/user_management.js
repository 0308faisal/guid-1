$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	}); 

  $( "#membertable tbody" ).on( "click", ".statusupdate", function() {
		if(confirm("Do you wish to update the status of this user?")){
			$.get( "/rest_v2/user/updatestatus?id="+$(this).parent().parent().find('.idfield').html().trim()+"&status="+$(this).parent().find('.statusselect').val(), function( data ) {
				status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
					return letter.toUpperCase();
				});
				alert(status+": "+data.response);
				document.location.reload();
			});
		}
	});

});
