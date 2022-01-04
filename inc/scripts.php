<!-- BEGIN CORE JS FRAMEWORK-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>

-->
<!-- END CORE JS FRAMEWORK -->

<!-- BEGIN PAGE LEVEL JS -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>



<script src="assets/plugins/breakpoints.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery.cookie.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-slider/jquery.sidr.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-slider/jquery.sidr.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-nestable/jquery.nestable.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/js/jquery.dataTables.min.js" type="text/javascript" ></script>
<script src="assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js" type="text/javascript" ></script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/lodash.min.js"></script>
<script src="assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery.form.js" type="text/javascript"></script>

<!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>-->
<script src="assets/js/ckeditor/ckeditor.js"></script>
<script src="assets/js/ckeditor/adapters/jquery.js"></script>
<script src="assets/js/jquery.mjs.nestedSortable.js"></script>
<script>
	$('textarea.text-editor').ckeditor();

    $('#network-type').change(function() {

      if ($('#network-type').val()=='Private') {
        $('#if_private_network').removeClass('hidden');

      } else {

        $('#if_private_network').addClass('hidden');
      }


    });

   	$( ".allow_duplication" ).change(function() {

  		if ($(this).is(':checked')) {
  			$('#show_allow_duplication').removeClass('hidden');
  		} else {
  			$('#show_allow_duplication').addClass('hidden');
  		}

	   });


    $('#example').tooltip();


</script>

<!-- END PAGE LEVEL PLUGINS -->
<script src="assets/js/datatables.js" type="text/javascript"></script>
<!-- BEGIN CORE TEMPLATE JS -->
<script src="assets/js/tabs_accordian.js" type="text/javascript"></script>
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="assets/js/demo.js" type="text/javascript"></script>
<!--<script src="assets/js/group_list.js" type="text/javascript"></script>-->
<script>
$(document).ready(function () {
	$("#logout").click(function(e){
		e.preventDefault();
		location.href="frontend/register.php?logout=true";
	});
});
</script>
<!-- END CORE TEMPLATE JS -->

<!-- END JAVASCRIPTS -->
