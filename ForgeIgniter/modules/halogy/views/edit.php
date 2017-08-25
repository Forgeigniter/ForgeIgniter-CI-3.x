<script type="text/javascript">
$(function(){
	$('a.showtab').click(function(event){
		event.preventDefault();
		var div = $(this).attr('href'); 
		$('div.tab').hide();
		$(div).show();
	});
	$('ul.innernav a').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('li').removeClass('selected'); 
		$(this).parent().addClass('selected');
	});
	$('div.tab:not(#tab1)').hide();
	$('div.permissions input[type="checkbox"]').each(function(){
		if ($(this).attr('checked')) {
			$(this).parent('div').prev('div').children('input[type="checkbox"]').attr('checked', true);
		}
	});	
	$('input.selectall').click(function(){
		$el = $(this).parent('div').next('div').children('input[type="checkbox"]');
		$flag = $(this).attr('checked');
		if ($flag) {
			$($el).attr('checked', true);
		}
		else {
			$($el).attr('checked', false);
		}
	});
	$('.seemore').click(function(){
		$el = $(this).parent('div').next('div');
		$($el).toggle('400');
	});
	$('#siteDomain').change(function(){
		var domainVal = $(this).val();
		var tld = '';
		domainVal = domainVal.replace(/^(http)s?:\/+((w+)\.)?|^www\.|\/|\/(.+)/ig, '');
		if (tld = domainVal.match(/\.[a-z]{2,3}\.[a-z]{2}$/i)){
			domainVal = domainVal.replace(/\.[a-z]{2,3}\.[a-z]{2}$/i, '');
			domainVal = domainVal.replace(/^(.+)\./ig, '');
			domainVal = domainVal+tld;
		}
		else if (tld = domainVal.match(/\.[a-z]{2,4}$/i)){
			domainVal = domainVal.replace(/\.[a-z]{2,4}$/i, '');
			domainVal = domainVal.replace(/(.+)\./ig, '');
			domainVal = domainVal+tld;
		}
		$(this).val(domainVal);
		$('#siteURL').val('http://www.'+domainVal);
		$('#siteEmail').val('info@'+domainVal);
	});
	$('a.selectall').click(function(event){
		event.preventDefault();
		$('input[type="checkbox"]').attr('checked', true);
	});	
	$('a.deselectall').click(function(event){
		event.preventDefault();
		$('input[type="checkbox"]').attr('checked', false);
	});	

});
</script>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

<h1 class="headingleft">Edit Site: <?php echo $data['siteDomain']; ?> <small>(<a href="<?php echo site_url('/halogy/sites'); ?>">Back to Sites</a>)</small></h1></h1>

<div class="headingright">
	<input type="submit" value="Edit Site" class="button" />
</div>

<?php if ($errors = validation_errors()): ?>
	<div class="error clear">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<div class="clear"></div>

<ul class="innernav clear">
	<li class="selected"><a href="#tab1" class="showtab">Details</a></li>
	<li><a href="#tab2" class="showtab">Permissions</a></li>
</ul>

<br class="clear" />

<div id="tab1" class="tab">

	<h2>Domains</h2>

	<label for="siteDomain">Domain:</label>
	<?php echo @form_input('siteDomain', set_value('siteDomain', $data['siteDomain']), 'id="siteDomain" class="formelement"'); ?>
	<span class="tip">For example 'mysite.com' (no sub-domains, www or trailing slashes)</span><br class="clear" />
	
	<label for="altDomain">Staging Domain:</label>
	<?php echo @form_input('altDomain', set_value('altDomain', $data['altDomain']), 'id="altDomain" class="formelement"'); ?>
	<span class="tip">Optional alternative domain for staging sites.</span><br class="clear" /><br />

	<h2>Site Details</h2>

	<label for="siteName">Name of Site:</label>
	<?php echo @form_input('siteName', set_value('siteName', $data['siteName']), 'id="siteName" class="formelement"'); ?>
	<span class="tip">The name of the site</span><br class="clear" />

	<label for="siteURL">URL:</label>
	<?php echo @form_input('siteURL', set_value('siteURL', $data['siteURL']), 'id="siteURL" class="formelement"'); ?>
	<span class="tip">The full URL to the site</span><br class="clear" />

	<label for="siteEmail">Email:</label>
	<?php echo @form_input('siteEmail', set_value('siteEmail', $data['siteEmail']), 'id="siteEmail" class="formelement"'); ?>
	<span class="tip">The site contact email</span><br class="clear" />

	<label for="siteTel">Telephone:</label>
	<?php echo @form_input('siteTel', set_value('siteTel', $data['siteTel']), 'id="siteTel" class="formelement"'); ?>
	<span class="tip">The site contact telephone number</span><br class="clear" />
	<br class="clear" />

	<label for="active">Status:</label>
	<?php
		$actives = array(
			1 => 'Active',
			0 => 'Suspended',			
		);	
		echo @form_dropdown('active', $actives, $data['active'], 'id="active" class="formelement"');
	?>
	<span class="tip">You cannot delete sites, but you can suspend them and take them offline here.</span>
	<br class="clear" />

</div>

<div id="tab2" class="tab">

	<h2>Permissions</h2>

	<p><a href="#" class="selectall button small nolabel grey">Select All</a> <a href="#" class="deselectall button small nolabel grey">De-Select All</a></p>

	<?php if ($permissions): ?>
	<?php foreach ($permissions as $cat => $perms): ?>

		<div class="perm-heading">
			<label for="<?php echo strtolower($cat); ?>_all" class="radio"><?php echo $cat; ?></label>
			<input type="checkbox" class="selectall checkbox" id="<?php echo strtolower($cat); ?>_all" />
			<input type="button" value="See more" class="seemore small-button" />
		</div>

		<div class="permissions">

		<?php foreach ($perms as $perm): ?>

			<label for="<?php echo 'perm_'.$perm['key']; ?>" class="radio"><?php echo $perm['permission']; ?></label>
			<?php echo @form_checkbox('perm'.$perm['permissionID'], 1, set_value('perm'.$perm['permissionID'], $data['perm'.$perm['permissionID']]), 'id="'.'perm_'.$perm['key'].'" class="checkbox"'); ?>
			<br class="clear" />

		<?php endforeach; ?>

		</div>

	<?php endforeach; ?>
	<?php endif; ?>
	
</div>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
