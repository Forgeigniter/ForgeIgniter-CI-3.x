<h1 class="headingleft">CSS Files</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/pages/templates'); ?>" class="button blue">Templates</a>
	<a href="<?php echo site_url('/admin/pages/includes'); ?>" class="button blue">Includes</a>	
	<a href="<?php echo site_url('/admin/pages/includes/js'); ?>" class="button blue">Javascript</a>	
	<a href="<?php echo site_url('/admin/pages/add_include/css'); ?>" class="button">Add CSS</a>
</div>

<div class="hidden">
	<p class="hide"><a href="#">x</a></p>
	<div class="inner"></div>
</div>

<?php if ($includes): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th>Filename</th>
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
		<td>
			<?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], 'Edit'); ?>
		</td>
		<td>			
			<?php echo anchor('/admin/pages/delete_include/'.$include['includeID'].'/css', 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">You haven't made any CSS files yet.</p>

<?php endif; ?>

