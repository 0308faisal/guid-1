$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			'authorization': $.cookie("token")
		}
	});
	$('.guidelink').click(function (e) {
		e.preventDefault();
		var addressValue = $(this).attr("href");
		window.location = addressValue;
	});
});
