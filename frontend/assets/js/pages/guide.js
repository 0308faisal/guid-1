$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			'authorization': $.cookie("token")
		}
	});
	$('.panel-body a').click(function (e) {
		e.preventDefault();
		var addressValue = $(this).attr("href");
		window.open(addressValue);
	});
	$("table").each(function () {
		$(this).addClass('table table-bordered');
	});

	$('.input').keypress(function (e) {
		e.preventDefault();
		if (e.which == 13) {
			$('#commentform').submit(function () {
				$(this).ajaxSubmit();
			});
		}
	});

	$('#adopt').click(function (e) {
		e.preventDefault();
		var addressValue = $(this).attr("href");
		if ($('#adopt').html() == "Remove Bookmark") {
			addressValue = addressValue.replace(/\/adopt\//i, "/unadopt/");
		}
		else {
			addressValue = addressValue.replace(/\/unadopt\//i, "/adopt/");
		}

		$.get(addressValue, function (data) {
			status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
				return letter.toUpperCase();
			});
			if (status == "Success" && $('#adopt').html() == "Bookmark") {
				$('#adopt').html("Remove Bookmark");
			}
			else if (status == "Success" && $('#adopt').html() == "Remove Bookmark") {
				$('#adopt').html("Bookmark");
			}
			alert(status + ": " + data.response);
		});

	});

	$('#commentform').ajaxForm(
		{
			beforeSubmit: function () {
				if ($("#comment").val() == "") {
					alert("Comment must be filled in.");
					return false;
				}
			},
			success: function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
				alert(status + ": " + data.response);
				document.location.reload();
			}
		});



	(function (i, s, o, g, r, a, m) {
		i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
			(i[r].q = i[r].q || []).push(arguments)
		}, i[r].l = 1 * new Date(); a = s.createElement(o),
			m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
	})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
	ga('create', $('#network_ga').val(), 'auto');
	ga('send', 'pageview');


});
