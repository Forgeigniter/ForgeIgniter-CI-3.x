<h1 class="headingleft">Web Forms</h1>

<div class="headingright">

	<a href="<?php echo site_url('/admin/webforms/tickets'); ?>" class="button blue">Tickets</a>
	<a href="<?php echo site_url('/admin/webforms/add_form'); ?>" class="button">Add Form</a>

</div>

<?php if ($web_forms): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th><?php echo order_link('admin/webforms/viewall','formName','Form Name'); ?></th>
		<th><?php echo order_link('admin/webforms/viewall','dateCreated','Date Created'); ?></th>		
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php
	$i=0;
	foreach ($web_forms as $form):
	$class = ($i % 2) ? ' class="alt"' : '';
	$i++;
?>
	<tr<?php echo $class; ?>>
		<td>
			<?php echo anchor('/admin/webforms/edit_form/'.$form['formID'], $form['formName']); ?>
			<small>(<?php echo $form['formRef']; ?>)</small>
		</td>	
		<td><?php echo dateFmt($form['dateCreated'], '', '', TRUE); ?></td>
		<td class="tiny">
			<?php echo anchor('/admin/webforms/edit_form/'.$form['formID'], 'Edit'); ?>
		</td>
		<td class="tiny">
			<?php if (in_array('webforms_delete', $this->permission->permissions)): ?>	
				<?php echo anchor('/admin/webforms/delete_form/'.$form['formID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">You have not yet set up any web forms.</p>

<?php endif; ?>

