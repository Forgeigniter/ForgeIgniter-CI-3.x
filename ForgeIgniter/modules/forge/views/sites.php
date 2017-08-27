<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; }
.ac_loading { background: white url('<?php echo $this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
$(function(){
    $('#searchbox').fieldreplace();
	function formatItem(row) {
		if (row[0].length) return row[1]+'<br /><span class="email">('+row[0]+')</span>';
		else return 'No results';
	}
	$('#searchbox').autocomplete("<?php echo site_url('/forge/ac_sites'); ?>", { delay: "0", selectFirst: false, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});
});
</script>

<h1 class="headingleft">Your Sites</h1>

<div class="headingright">

	<form method="post" action="<?php echo site_url('/forge/sites'); ?>" class="default" id="search">
		<input type="text" name="searchbox" id="searchbox" class="formelement inactive" title="Search Sites..." />
		<input type="image" src="<?php echo $this->config->item('staticPath'); ?>/images/btn_search.gif" id="searchbutton" />
	</form>

	<a href="<?php echo site_url('/forge/add_site'); ?>" class="button">Add Site</a>
</div>

<div class="clear"></div>

<?php if ($sites): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th><?php echo order_link('forge/sites/viewall','siteName','Site Name'); ?></th>
		<th><?php echo order_link('forge/sites/viewall','dateCreated','Date Created'); ?></th>
		<th><?php echo order_link('forge/sites/viewall','siteDomain','Domain'); ?></th>
		<th><?php echo order_link('forge/sites/viewall','altDomain','Staging Domain'); ?></th>
		<th class="narrow"><?php echo order_link('forge/sites/viewall','active','Status'); ?></th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php
	$i=0;
	foreach ($sites as $site):
	$class = ($i % 2) ? ' class="alt"' : ''; $i++;
?>
	<tr<?php echo $class; ?>>
		<td><?php echo anchor('/forge/edit_site/'.$site['siteID'], $site['siteName']); ?></td>
		<td><?php echo dateFmt($site['dateCreated']); ?></td>
		<td><?php echo $site['siteDomain']; ?></td>
		<td><?php echo $site['altDomain']; ?></td>
		<td>
			<?php
				if ($site['active']) echo '<span style="color:green"><strong>Active</strong></span>';
				if (!$site['active']) echo '<span style="color:red">Suspended</span>';
			?>
		</td>
		<td class="tiny">
			<?php echo anchor('/forge/edit_site/'.$site['siteID'], 'Edit'); ?>
		</td>
		<td class="tiny">
			<?php echo anchor('/forge/delete_site/'.$site['siteID'], 'Delete', 'onclick="return confirm(\'Are you absolutely SURE you want to delete this site?\n\nThere is no undo!\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

	<?php if (count($_POST)): ?>

		<p>No sites found.</p>

	<?php else: ?>

		<p>You haven't created any sites yet. Create one using the &ldquo;Add Site&rdquo; link above.</p>

	<?php endif; ?>

<?php endif; ?>
