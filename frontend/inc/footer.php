<?php declare(strict_types=1);

if (isset($_COOKIE['token'])) {?>
	<script>
		$(document).ready(function () {
			// Send user profile to segment
			mixpanel.identify("<?=$user->id; ?>");
					mixpanel.people.set({
			"userId":"<?=$user->id; ?>",
			"Occupation":"<?=$user->occupation; ?>",
			"Speciality":"<?=$user->specialty; ?>",
			"Gradename":"<?=$user->gradename; ?>",
			"Employer":"<?=$user->employer; ?>",
			"Networks":"<?=$user->networks[0]['name']; ?>"
			});

			mixpanel.register({
			"userId":"<?=$user->id; ?>",
			"Occupation":"<?=$user->occupation; ?>",
			"Speciality":"<?=$user->specialty; ?>",
			"Gradename":"<?=$user->gradename; ?>",
			"Employer":"<?=$user->employer; ?>",
			"Networks":"<?=$user->networks[0]['name']; ?>"
			});
			<?php
			if (isset($_GET['login']) && $_GET['login'] == 'true') {?>
			// Track the login
			mixpanel.track('User Logged In', {'distinct_id': '<?=$user->id; ?>'});
					mixpanel.people.increment("Return Visits");
			<?php } ?>
			<?php
			if (isset($_GET['searchterm']) && !empty($_GET['searchterm']) && isset($_GET['st'])) {
				if ($data['available_guide_count'] == 0) {
					?>
						mixpanel.track('Search not found',{'distinct_id': '<?=$user->id; ?>', 'Search Term':'<?=$_GET['searchterm']; ?>'});
						mixpanel.people.increment("No of Searches");
					<?php
				}
			}
			?>
			$('#navguide').click(function (e) {
				mixpanel.track('Guidelines Nav Top', {'distinct_id': '<?=$user->id; ?>'});
			});
			$('#navnetwork').click(function (e) {
				mixpanel.track('Networks Nav Top', {'distinct_id': '<?=$user->id; ?>'});
			});
			$('#navdash').click(function (e) {
				mixpanel.track('Admin Nav Top', {'distinct_id': '<?=$user->id; ?>'});
			});
			$('#my-task-list').click(function (e) {
				mixpanel.track('Viewed Profile', {'distinct_id': '<?=$user->id; ?>'});
			});
			$('.catcollapse').click(function (e) {
				mixpanel.track('Dashboard Expanded Category', { 'Category Name': this.text });
			});
			$('.guidelink').click(function (e) {
				var now = new Date();
				var strguide = this.name.split('|');
				mixpanel.track('View Guideline', { 'distinct_id': '<?=$user->id; ?>', 'Guideline Category': strguide[0], 'Guideline Name': strguide[1], 'Search Term': $('#searchterm').html() });
				mixpanel.people.set('Saw Guide ' + this.id, true);
				mixpanel.people.set('View Guide ' + this.id, now);
				mixpanel.people.increment("Guideline Views");
			});
			$('.contentcollapse').click(function (e) {
				var strguide = this.name.split('|');
				mixpanel.track('Guideline Section Expanded', { 'distinct_id': '<?=$user->id; ?>', 'Guideline Category': strguide[0], 'Guideline Name': strguide[1], 'Section Name': strguide[2] });
			});
			$('#comment').click(function (e) {
				mixpanel.track('Add Comment [' + this.name + ']', {'distinct_id': '<?=$user->id; ?>'});
			});
			$('#reset').click(function (e) {
				mixpanel.track('Reset Password', {'distinct_id': '<?=$user->id; ?>'});
			});
			$('#search').keypress(function(e) {
			if(e.which == 13) {
				mixpanel.track('Searched for \''+$('#search').val()+'\'', {'distinct_id': '<?=$user->id; ?>'});
				mixpanel.people.increment("No of Searches");
				const queryString = window.location.search;
				window.location=window.location.href  + (queryString == "" ? "?" :  "&")  + "st=true&searchterm="+$('#search').val();
			}
			});
			$('#searchbutton').click(function(e) {
					mixpanel.track('Searched for \''+$('#search').val()+'\'', {'distinct_id': '<?=$user->id; ?>'});
					mixpanel.people.increment("No of Searches");
					const queryString = window.location.search;
					window.location=window.location.href + (queryString == "" ? "?" :  "&")  + "st=true&searchterm="+$('#search').val();
			});

		});
</script>
<a href="javascript:;" id="topanchor" onclick="window.scrollTo(0,0);"></a>
<?php
	}
?>

</body>
</html>
