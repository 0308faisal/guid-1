$(document).ready(function () {   
	//$(".editorfield").ckeditor(); 
    $("#savechanges").click(function(){	
		serialized = $('.sortable').nestedSortable('serialize', {startDepthCount: 0});
		$('#fields').val(serialized);
		$("#guideform").submit();
	});
	$("#deletedecision").click(function(){
		if(confirm("Delete this decision?")==true){
		$.get("/rest_v2/decisiondelete/"+$("#decision_id").val()+"?token="+$("#token").val(), function(data){
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			if(status=="Success"){
				window.top.location.href="/guideline_edit.php?id="+$('#guideline_id').val();		
			}
			else{
				alert(status+": "+data.response);
			}
			
		});
		
		}
	});
	
	$('#guideform').ajaxForm( 
	{
		beforeSubmit: function() { 
			if ( $("#decision_title").val()==""){
				alert("Decision title must be filled in.");
				return false;
			}
			serialized = $('.sortable').nestedSortable('serialize', {startDepthCount: 0});
			arr =  serialized.split('&');
			nullcounter=0;
			for(i=0;i<arr.length;i++){
				tmp=arr[i].split('=');
				if(tmp[1]=="null"){
					nullcounter++;
				}
			}
			if(nullcounter>1){
				alert("Decision cannot have more than one top level node.");
				return false;
			}
		},
		success: function(data) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			alert(status+": "+data.response);
			window.top.location.href="/guideline_edit.php?id="+$('#guideline_id').val();	
		} 
	}); 
	//$('.dd').nestable();
	 $('.sortable').nestedSortable({
            handle: 'div',
            items: 'li',
            toleranceElement: '> div'
        });

    $("#addoption").click(function(){
		var optionid = 0;
		$('.dd-list li').each(function(i,n){
		  var check = $(this).data("id");
		  if(check>=optionid) optionid = check +1;
		});
		$('<li class="dd-item dd3-item" data-id="'+optionid+'" id="item_'+optionid+'">' +
			'<div class="dd-handle"><!--</div><div class="dd3-content">-->' +
				'<span id="headerspan'+optionid+'" onclick="$(\'#fielddiv'+optionid+'\').toggle();">New Field</span><button class="close" onclick="$(\'li[data-id='+optionid+']\').remove();var editor = $(\'#contentfield'+optionid+'\').ckeditorGet();CKEDITOR.remove(editor);return false;" style="padding-right:5px">X</button>' +
			'</div>'+
			'<div id="fielddiv'+optionid+'" style="display:none;">' +
				'<input type="hidden"id="typefield'+optionid+'" name="typefield['+optionid+']" value="answer">' +
				'<!--<select id="typefield'+optionid+'" name="typefield['+optionid+']">' +
							'<option value="task">Clinical Activity</option>' +
							'<option value="question">Clinical Issue</option>' +
							'<option value="answer">Differential Diagnosis</option>' +
							'<option value="recommendation">Recommended Action</option>'+
						'</select>-->' +
				'<input style="width:100%" type="text" id="headerfield'+optionid+'" name="headerfield['+optionid+']" onkeyup="$(\'#headerspan'+optionid+'\').html($(\'#headerfield'+optionid+'\').val());" placeholder="Title">' +
				'<textarea id="contentfield'+optionid+'" style="width:100%" name="contentfield['+optionid+']" placeholder="Content"></textarea>' +
			'</div>' +
			'</li>').appendTo('.dd-list :first');
		//$("#contentfield"+optionid).ckeditor(); 
		//$('.dd').nestable();
		 $('.sortable').nestedSortable({
            handle: 'div',
            items: 'li',
            toleranceElement: '> div'
        });
	});
	
	function dump(arr,level) {
			var dumped_text = "";
			if(!level) level = 0;
	
			//The padding given at the beginning of the line.
			var level_padding = "";
			for(var j=0;j<level+1;j++) level_padding += "    ";
	
			if(typeof(arr) == 'object') { //Array/Hashes/Objects
				for(var item in arr) {
					var value = arr[item];
	
					if(typeof(value) == 'object') { //If it is an array,
						dumped_text += level_padding + "'" + item + "' ...\n";
						dumped_text += dump(value,level+1);
					} else {
						dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
					}
				}
			} else { //Strings/Chars/Numbers etc.
				dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
			}
			return dumped_text;
		}

});
