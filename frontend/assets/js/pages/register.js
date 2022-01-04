$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			'authorization': $.cookie("token")
		}
	});

	$("#resetpassword").click(function (e) {
		e.preventDefault();
		if ($("#login_email").val() == "") {
			alert("Please fill in Email");
			return false;
		}
		$.get("/rest_v2/user/resetpassword", { email: $("#login_email").val() })
			.done(function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
			});
	});

	$('#occupation').change(function () {
		if ($(this).val() == "1" || $(this).val() == "2") {
			$.getJSON("/rest_v2/guideapi/getdropdowndata?field=grade&occupation_id=" + this.value)
				.done(function (data) {
					$("#grade").empty();
					for (var i = 0, len = data["data"].length; i < len; i++) {
						var option = "<option value='" + data["data"][i].id + "'>" + data["data"][i].name + "</option>";
						$("#grade").append(option);
					}
					if (data["data"].length == 0) {
						var option = "<option value=''>Not Available</option>";
						$("#grade").empty().append(option);
					}
					$("#gradesection").show();
				});
		}
		else {
			$("#gradesection").hide();
		}
	});

	$('#guideform').ajaxForm(
		{
			beforeSubmit: function () {
				var validator = $("#guideform").validate();
			},
			success: function (data) {
				mixpanel.track("Registration", {
					"Occupation": $("#occupation").val(),
					"Gradename": $("#grade").val(),
					"Network": $("#network").val()
				});
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				if (status == "Success") {
					if (data.redirect != "") {
						window.location.href = data.redirect;
					}
					else {
						document.location.reload();
					}
				}
			},
			error: function (data) {
				status = data.responseJSON.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.responseJSON.response);
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
