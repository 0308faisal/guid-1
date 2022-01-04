$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});

	$("#networktable tbody").on("click", ".statusupdate", function () {
		if (confirm("Do you wish to update the status of this network?")) {
			$.get("/rest_v2/network/approvenetwork?token=" + $('#token').val() + "&nid=" + $(this).data("nid") + "&status="+$("#statusselect"+$(this).data("nid")).val(), function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				document.location.reload();
			});
		}
	});

});
