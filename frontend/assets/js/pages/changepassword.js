$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			'authorization': $.cookie("token")
		}
	});

	$("#resetpassword").click(function (e) {
		e.preventDefault();
		if ($("#email").val() == "") {
			alert("Please fill in Email");
			return false;
		}
		$.get("/rest_v2/user/resetpassword", { email: $("#email").val() })
			.done(function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
			});
	});

	$('#guideform').ajaxForm(
		{
			beforeSubmit: function () {
				$("#guideform").validate();
			},
			success: function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				if (status == "Success") {
					document.location = "/frontend/register.php";
				}


			}
		});
	var $input = $("#guideform #password");
	$("#showpass").click(function (e) {
		e.preventDefault();
		var change = "";
		if ($(this).html() === "Show Password") {
			$(this).html("Hide Password")
			change = "text";
		} else {
			$(this).html("Show Password");
			change = "password";
		}
		var rep = $("<input type='" + change + "' />")
			.attr("id", $input.attr("id"))
			.attr("name", $input.attr("name"))
			.attr('class', $input.attr('class'))
			.val($input.val())
			.insertBefore($input);
		$input.remove();
		$input = rep;
	}).insertAfter($input);
});
