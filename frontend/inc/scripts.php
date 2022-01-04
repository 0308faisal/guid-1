<script src="<?=BASE_URL?>assets/js/jquery.min.js"></script>
<script src="<?=BASE_URL?>assets/js/bootstrap.min.js"></script>
<script src="<?=BASE_URL?>assets/plugins/jquery.cookie.js" type="text/javascript"></script>
<script src="<?=BASE_URL?>assets/plugins/jquery-datatable/js/jquery.dataTables.min.js"></script>
<script src="<?=BASE_URL?>assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js"></script>
<script src="<?=BASE_URL?>assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script src="<?=BASE_URL?>assets/plugins/datatables-responsive/js/lodash.min.js"></script>
<script src="<?=BASE_URL?>assets/plugins/jquery.form.js" type="text/javascript"></script>
<script src="<?=BASE_URL?>assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<!--<script src="../assets/js/datatables.js" type="text/javascript"></script>-->

<script src="<?=BASE_URL?>assets/js/spacetree.js"></script>
<script src="<?=BASE_URL?>frontend/assets/js/tree-view.js"></script>
<script src="<?=BASE_URL?>frontend/assets/js/sugar.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript" src="assets/js/excanvas.js"></script><![endif]-->
<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
var return_visits=0;
mixpanel.init("<?=HOSTS[$_SERVER['SERVER_NAME']]['mixpanel_id']?>");
</script><!-- end Mixpanel -->
<script>
$(document).ready(function () {
	$('#content-accordion').collapse({
		toggle: false
	})
	$("#logout").click(function(e){
		e.preventDefault();
	location.href="register.php?logout=true";
	});
});
</script>
