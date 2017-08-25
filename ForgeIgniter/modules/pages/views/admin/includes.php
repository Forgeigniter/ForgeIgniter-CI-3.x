<h1 class="headingleft">Includes</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/pages/templates'); ?>" class="button blue">Templates</a>
	<a href="<?php echo site_url('/admin/pages/includes/css'); ?>" class="button blue">CSS</a>
	<a href="<?php echo site_url('/admin/pages/includes/js'); ?>" class="button blue">Javascript</a>	
	<a href="<?php echo site_url('/admin/pages/add_include'); ?>" class="button">Add Include</a>
</div>

<div class="hidden">
	<p class="hide"><a href="#">x</a></p>
	<div class="inner"></div>
</div>

<div class="clear"></div>

<?php if ($includes): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th>Reference</th>
		<th>Date Modified</th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php
	$i = 0;
	foreach ($includes as $include):
	$class = ($i % 2) ? ' class="alt"' : ''; $i++;
?>
	<tr<?php echo $class;?>>
		<td><?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], $include['includeRef']); ?></td>	
		<td><?php echo dateFmt($include['dateCreated']); ?></td>
		<td>
			<?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], 'Edit'); ?>
		</td>
		<td>			
			<?php echo anchor('/admin/pages/delete_include/'.$include['includeID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">You haven't made any Include files yet.</p>

<?php endif; ?>

