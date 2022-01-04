$(document).ready(function () {
	$.ajaxSetup({
   headers:{
      'authorization': $.cookie("token")
   }
	});

	$( "#selectguidelines" ).change(function(){
		CKEDITOR.instances['guidedescription'].insertHtml("<a class='guidelink' href='guide.php?id="+$(this).val()+"'>"+$(this).children(':selected').text()+"</a>");
		$('#selectguidelines').val('0');
	});
	$( ".dinamicguidelineselector" ).change(function(){
		CKEDITOR.instances['editor'+$(this).data("id")].insertHtml("<a class='guidelink' href='guide.php?id="+$(this).val()+"'>"+$(this).children(':selected').text()+"</a>");
		$('#selectguidelines').val('0');
	});
	$( "#content-holder" ).sortable({
	    stop: function(event, ui) {
				var editor = $('#' + ui.item.children().find("textarea")[0].id).ckeditorGet();
				var content = $('#' + ui.item.children().find("textarea")[0].id).val();
				CKEDITOR.remove(editor);
				$('#cke_'+ui.item.children().find("textarea")[0].id).remove();
				CKEDITOR.replace(ui.item.children().find("textarea")[0].id);
				CKEDITOR.instances[ui.item.children().find("textarea")[0].id].setData(content);
	    },
	    placeholder: "ui-sorting-highlight"
	});

	$("#content-holder").disableSelection();
	$('.expandlink').click(function(e){
		e.preventDefault();
		if($(this).text()=="+"){
			$(this).text("-");
			$("#contents"+$(this).data("id")).slideDown();
		}else{
			$(this).text("+");
			$("#contents"+$(this).data("id")).slideUp();
		}

	});
	$('#myModal').on('hidden', function () {
		$("#iframe").attr("src","");
		location.reload();
	});
	$("#myModal").on('hide.bs.modal', function () {
		$("#iframe").attr("src","");
		location.reload();
  });
	$("#preview").click(function(){
		if($("#id").val()==""){
			$("#guideform").submit();
		}
		$("#iframe").attr("src","guide_preview.php?id="+$('#id').val());
		$("#myModal").modal('show');
	});

	$("#decisions").click(function(){
		if($("#id").val()==""){
			$("#guideform").submit();
		}
		$("#iframe").attr("src","guide_treeview.php?id="+$('#id').val());
		$("#myModal").modal('show');
	});

	$(".decisionbutton").click(function(){
		if($("#id").val()==""){
			$("#guideform").submit();
		}
		$("#iframe").attr("src","guide_treeview.php?id="+$("#id").val()+"&decision_id="+$(this).data("decisionid"));
		$("#myModal").modal('show');
	});

	$('#guideform').ajaxForm(
	{
		beforeSubmit: function() {
			if ( $("#publish").is(":checked") && !$("#terms").is(":checked") ){
				alert("To publish, you must agree to the terms and conditions");
				window.scrollTo(0, 0);
				return false;
			}
			if ($("#logcomment").val()==""){
				alert("Change log note is required");
				return false;
			}
			if ( $("#guidename").val()=="" || $("#guidecategories").val()==""){
				alert("Guideline name, categories must be filled in.");
				return false;
			}
		},
		success: function(data) {
			status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
			if (typeof data.guideline_id !== 'undefined') {
				$('#id').val(data.guideline_id);
			}
			alert(status+": "+data.response);
			window.location.href='guideline_edit.php?id='+$('#id').val();
		}
	});
	$("#repeat-citation-btn").click(function(e){
		e.preventDefault();
		$('<div class="citation-wrapper">'+
			'<div class="input-group">'+
			'<span class="input-group-addon"></span>'+
			'<input type="text" class="form-control" placeholder="Author" name="citationauthor[]" value="">'+
			'</div>'+
			'<br />'+
			'<div class="input-group">'+
			'<span class="input-group-addon"></span>'+
			'<input type="text" class="form-control" placeholder="Citation Reference" name="citationreference[]" value="">'+
			'</div>'+
			'<br />'+
			'</div>').insertAfter('.citation-wrapper :last');

	});
	$("#repeat-content-btn").click(function(e){
		e.preventDefault();
		editorid=parseInt($('.content-wrapper:last').find("textarea").attr("id").substring(6,8))+1;
		$('<div class="content-wrapper">'+
		'<div class="custom-field-container alert alert-success" style="">'+
		'<button class="close" data-dismiss="alert" onclick="var editor = $(\'#editor'+editorid+'\').ckeditorGet();CKEDITOR.remove(editor);$(this).closest(\'.content-wrapper\').remove();"></button>'+
		'<div class="form-group">'+
		'<label class="form-label">Title</label>'+
		'<div class="controls">'+
		'<input type="text" class="form-control"  name="customcontenttitle[]" value="">'+
		'</div>'+
		'</div>'+
		'<div class="form-group">'+
		'<label class="form-label">Content</label>'+
		'<div class="controls">'+
		'<textarea class="text-editor form-control" id="editor'+editorid+'" name="customcontent[]"></textarea>'+
		'</div>'+
		'</div>'+
		'</div>'+
		'<br />'+
		'</div>').insertAfter('.content-wrapper :last');
		$('.dinamicguidelineselector:last').clone(true).attr("data-id",editorid).insertAfter('#editor'+editorid);
		$('#editor'+editorid).ckeditor().on('insertElement', function(event) {
			var element = event.data;
			if (element.getName() == 'img') {
				element.addClass('image-display');
			}
		});
	});
	$("#add-decision-tree").click(function(e){
		e.preventDefault();
	});
});
