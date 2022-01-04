$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});

	$("#membertable tbody").on("click", ".statusupdate", function () {
		if (confirm("Do you wish to update the access status of this network?")) {
			$.get("/rest_v2/community/updatecommunityaccess?nid=" + $(this).data("nid") + "&status="+$("#status_"+$(this).data("nid")).val(), function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				document.location.reload();
			});
		}
	});

});
