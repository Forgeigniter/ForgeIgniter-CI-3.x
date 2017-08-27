<script type="text/javascript">
function refresh(){
	$('div.loader').load('<?php echo site_url('/admin/tracking_ajax'); ?>');
	timeoutID = setTimeout(refresh, 5000);
}

$(function(){
	timeoutID = setTimeout(refresh, 0);
});
</script>

<h1>Most Recent Visits <small>(<a href="<?php echo site_url('/admin'); ?>">Back to Dashboard</a>)</small></h1>

<br />

<div class="loader"></div>