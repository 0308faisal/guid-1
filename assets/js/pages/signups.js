$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});

	$("#membertable tbody").on("click", ".statusupdate", function () {
		if (confirm("Do you wish to update the signup status of this user?")) {
			$.get("/rest_v2/user/updatesignup?id=" + $(this).parent().parent().find('.idfield').html().trim() + "&activation_code=" + $(this).parent().parent().find('.idfield').data("activation_code") +"&status="+$(this).parent().find('.statusselect').val(), function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				document.location.reload();
			});
		}
	});

});
