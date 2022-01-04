$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});
	$('#profileform').ajaxForm(
	{
		beforeSubmit: function() {
			$("#profileform").validate();
		},
		success: function(data) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			alert(status+": "+data.response);
			document.location.reload();
		}
	});
	$('#passwordform').ajaxForm(
	{
		beforeSubmit: function() {
			$("#passwordform").validate();
		},
		success: function(data) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			alert(status+": "+data.response);
			document.location.reload();
		}
	});
	$('#occupation').change(function(){
		$.getJSON( "/rest_v2/guideapi/getdropdowndata?field=speciality&occupation_id="+this.value)
		  .done(function( data ) {
			$("#speciality").empty();
			for (var i=0, len=data[0].length; i < len; i++) {
				var option = "<option value='" + data[0][i].id + "'>" + data[0][i].name + "</option>";
				$("#speciality").append(option);
			}
			if(data[0].length==0){
			  var option = "<option value=''>Not Available</option>";
				$("#speciality").empty().append(option);
		  }
		  });
		  $.getJSON( "/rest_v2/guideapi/getdropdowndata?field=grade&occupation_id="+this.value)
		  .done(function( data ) {
			$("#grade").empty();
			for (var i=0, len=data[0].length; i < len; i++) {
				var option = "<option value='" + data[0][i].id + "'>" + data[0][i].name + "</option>";
				$("#grade").append(option);
			}
			if(data[0].length==0){
			  var option = "<option value=''>Not Available</option>";
				$("#grade").empty().append(option);
		  }
		  });

	});

	var $inputnew = $("#passwordform #newpassword");
	$("#shownewpass").click(function (e) {
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
        .attr("id", $inputnew.attr("id"))
        .attr("name", $inputnew.attr("name"))
        .attr('class', $inputnew.attr('class'))
        .val($inputnew.val())
        .insertBefore($inputnew);
      $inputnew.remove();
      $inputnew = rep;
    }).insertAfter($inputnew);

		var $inputold = $("#passwordform #oldpassword");
		$("#showoldpass").click(function (e) {
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
	        .attr("id", $inputold.attr("id"))
	        .attr("name", $inputold.attr("name"))
	        .attr('class', $inputold.attr('class'))
	        .val($inputold.val())
	        .insertBefore($inputold);
	      $inputold.remove();
	      $inputold = rep;
	    }).insertAfter($inputold);
});
