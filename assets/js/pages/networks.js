
	function joinnetwork(networkid,n2n,denyaccess){
		$.ajaxSetup({
	   headers:{
	      'authorization': $.cookie("token")
	   }
		});
		$.getJSON( "/rest_v2/network/networkjoin?token="+$("#token").val()+"&n2n="+n2n+"&nid="+networkid)
		  .done(function( data ) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to join has been received.</p>");
			$("#resultmsg").show();
			window.setTimeout(function() {
			  $("#resultmsg").slideUp("slow");
			}, 3000);
			$("#netbutton_"+networkid).val("Leave");
			$("#netbutton_"+networkid).attr("onclick","leavenetwork("+networkid+",'"+n2n+"','"+denyaccess+"')");
			$("html, body").animate({ scrollTop: 0 }, "slow");
		  });
	}

	function leavenetwork(networkid,n2n,denyaccess){
		$.ajaxSetup({
	   headers:{
	      'authorization': $.cookie("token")
	   }
		});
		$.getJSON( "/rest_v2/network/networkleave?token="+$("#token").val()+"&n2n="+n2n+"&nid="+networkid)
		  .done(function( data ) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			alert(status + ": " + data.response);
			if(status!="error"){
				$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to join "+$(this).data("networkname")+" has been received.</p>");
				$("#resultmsg").show();
				window.setTimeout(function() {
					$("#resultmsg").slideUp("slow");
				}, 3000);
				if(denyaccess=="1"){
					buttontxt="Request Access";
					onclicktxt="requestnetwork("+networkid+",'"+n2n+"','"+denyaccess+"')";
				}else{
					buttontxt="Access Available";
					onclicktxt="joinnetwork("+networkid+",'"+n2n+"','"+denyaccess+"')";
					}
					$("#netbutton_"+networkid).val(buttontxt);
					$("#netbutton_"+networkid).attr("onclick",onclicktxt);
					$("html, body").animate({ scrollTop: 0 }, "slow");
			}


			$("#netbutton_"+networkid).val(buttontxt);
			$("#netbutton_"+networkid).attr("onclick",onclicktxt);
			$("html, body").animate({ scrollTop: 0 }, "slow");
			})
			.fail(function(data) {
	     alert(status + ": " + data.response);
		 });
	}

	function requestnetwork(networkid,n2n,denyaccess){
		$.ajaxSetup({
	   headers:{
	      'authorization': $.cookie("token")
	   }
		});
		$.getJSON( "/rest_v2/network/networkjoin?n2n="+n2n+"&nid="+networkid)
		  .done(function( data ) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			alert(status + ": " + data.response);
			if(status!="error"){
				$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to join "+$(this).data("networkname")+" has been received.</p>");
				$("#resultmsg").show();
				window.setTimeout(function() {
					$("#resultmsg").slideUp("slow");
				}, 3000);
				$("#netbutton_"+networkid).val("Access Requested");
				$("#netbutton_"+networkid).attr("onclick","leavenetwork("+networkid+",'"+n2n+"','"+denyaccess+"')");
				$("html, body").animate({ scrollTop: 0 }, "slow");
			}

		})
			.fail(function(data) {
				status=data.responseJSON.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
					return letter.toUpperCase();
				});
	     alert(status + ": " + data.responseJSON.response);
   		});
	}
