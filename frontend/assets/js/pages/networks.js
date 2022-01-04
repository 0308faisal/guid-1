
	function joinnetwork(networkid,networkname,denyaccess){
		$.ajaxSetup({
	   headers:{
	      'authorization': $.cookie("token")
	   }
		});
		$.getJSON( "/rest_v2/network/networkjoin?nid="+networkid)
		  .done(function( data ) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to join "+networkname+" has been received.</p>");
			$("#resultmsg").show();
			window.setTimeout(function() {
			  $("#resultmsg").slideUp("slow");
			}, 3000);
			$("#netbutton_"+networkid).val("Leave");
			$("#netbutton_"+networkid).attr("onclick","leavenetwork("+networkid+",'"+networkname.replace(/'/g, "\\'")+"','"+denyaccess+"')");
			$("html, body").animate({ scrollTop: 0 }, "slow");
		  });
	}

	function leavenetwork(networkid,networkname,denyaccess){
		$.ajaxSetup({
	   headers:{
	      'authorization': $.cookie("token")
	   }
		});
		$.getJSON( "/rest_v2/network/networkleave?nid="+networkid)
		  .done(function( data ) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to leave "+networkname+" has been received.</p>");
			$("#resultmsg").show();
			window.setTimeout(function() {
			  $("#resultmsg").slideUp("slow");
			}, 3000);
			if(denyaccess=="1"){
				buttontxt="Request Access";
				onclicktxt="requestnetwork("+networkid+",'"+networkname.replace(/'/g, "\\'")+"','"+denyaccess+"')";
			}else{
				buttontxt="Join";
				onclicktxt="joinnetwork("+networkid+",'"+networkname.replace(/'/g, "\\'")+"','"+denyaccess+"')";
				}
			$("#netbutton_"+networkid).val(buttontxt);
			$("#netbutton_"+networkid).attr("onclick",onclicktxt);
			$("html, body").animate({ scrollTop: 0 }, "slow");
		  });
	}

	function requestnetwork(networkid,networkname,denyaccess){
		$.ajaxSetup({
	   headers:{
	      'authorization': $.cookie("token")
	   }
		});
		$.getJSON( "/rest_v2/network/networkjoin?nid="+networkid)
		  .done(function( data ) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			$("#resultmsg").html("<a href='#' class='close' data-dismiss='alert'></a><p>Your request to access "+networkname+" has been received.</p>");
			$("#resultmsg").show();
			window.setTimeout(function() {
			  $("#resultmsg").slideUp("slow");
			}, 3000);
			$("#netbutton_"+networkid).val("Pending");
			$("#netbutton_"+networkid).attr("onclick","leavenetwork("+networkid+",'"+networkname.replace(/'/g, "\\'")+"','"+denyaccess+"')");
			$("html, body").animate({ scrollTop: 0 }, "slow");
		  });
	}
