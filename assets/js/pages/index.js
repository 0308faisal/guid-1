$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});
	$('.guidelink').click(function (e) {
		e.preventDefault();
        var addressValue = $(this).attr("href");
        window.location=addressValue;
	});

	$(".copy").click(function(e){
		e.preventDefault();
		if (confirm("Do you wish to clone this guideline?")) {
			$.get("/rest_v2/guideline/cloneguide?token=" + $('#token').val() + "&activenetwork=" + $("#activenetwork").val() + "&id=" + $(this).data("guideline_id"), function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				window.location = "/guideline_edit.php?id=" + data.id;
			});
		}
	});

	$(".link").click(function(e){
		e.preventDefault();
		if (confirm("Do you wish to link this guideline? It will be published immediately.")) {
			$.get("/rest_v2/guideline/linkguide?activenetwork=" + $("#activenetwork").val() + "&id=" + $(this).data("guideline_id"), function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				window.location = "/guide.php?id=" + data.id;
			});
		}
	});

	$(".unlink").click(function(e){
		e.preventDefault();
		if (confirm("You are about to unlink this guideline, please click okay to confirm") == true) {
			var addressValue = $(this).attr("href");
			$.get( addressValue, function( data ) {
			  status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
					return letter.toUpperCase();
				});
				alert(status+": " + data.response);
				document.location.reload();
			});
		}
	});

	$(".copyall").click(function(e){
		e.preventDefault();
		if (confirm("Do you wish to clone this guideline?")) {
			$.get("/rest_v2/guideline/cloneall?token=" + $('#token').val() + "&activenetwork=" + $("#activenetwork").val() + "&network_id=" + $(this).data("networkid"), function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				window.location = "/guidelines.php";
			});
		}
	});

	$(".delete").click(function(e){
		e.preventDefault();
		if (confirm("You are about to delete this guideline, please click okay to confirm") == true) {
			var addressValue = $(this).attr("href");
			$.get( addressValue, function( data ) {
			  status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
					return letter.toUpperCase();
				});
				alert(status+": " + data.response);
				document.location.reload();
			});
		}
	});
	$('#search').keypress(function(e) {
		if(e.which == 13) {
			window.location="index.php?searchterm="+$('#search').val();
		}
	});
	$('#searchbutton').click(function(e) {
			window.location="index.php?searchterm="+$('#search').val();
	});
});

$(document).on('click', '.joinnetwork', function(e){
	e.preventDefault();
	thisid=this.id;
	$.get("/rest_v2/network/networkjoin?nid="+$(this).data("networkid")+"&n2n="+$("#activenetwork").val(), function (data) {
		status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
			return letter.toUpperCase();
		});
		alert(status + ": " + data.response);
		if(status!="error"){
			$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to join "+$(this).data("networkname")+" has been received.</p>");
			$("#resultmsg").show();
			window.setTimeout(function() {
				$("#resultmsg").slideUp("slow");
			}, 3000);
			$("#"+thisid).val("Access Requested");
			$("#"+thisid).removeClass("joinnetwork");
			$("#"+thisid).addClass("leavenetwork");
		}

	});
});

$(document).on('click', '.leavenetwork', function(e){
	e.preventDefault();
	thisid=this.id;
	$.get("/rest_v2/network/networkleave?token="+$("#token").val()+"&nid="+$(this).data("networkid")+"&n2n="+$("#activenetwork").val(), function (data) {
		status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
			return letter.toUpperCase();
		});
		alert(status + ": " + data.response);
		$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to leave "+$(this).data("networkname")+" has been received.</p>");
		$("#resultmsg").show();
		window.setTimeout(function() {
			$("#resultmsg").slideUp("slow");
		}, 3000);
		$("#"+thisid).val("Request Access");
		$("#"+thisid).removeClass("leavenetwork");
		$("#"+thisid).addClass("joinnetwork");

	});
});
