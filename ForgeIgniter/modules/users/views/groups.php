<h1 class="headingleft">User Groups</h1>

<div class="headingright">

	<?php if (in_array('users', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/users'); ?>" class="button blue">Users</a>
	<?php endif; ?>

	<?php if (in_array('users_groups', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/users/add_group'); ?>" class="button">Add Group</a>
	<?php endif; ?>
</div>

<?php if ($permission_groups): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th><?php echo order_link('/admin/users/viewall','groupName','Group name'); ?></th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>		
	</tr>
<?php foreach ($permission_groups as $group): ?>
	<tr>
		<td><?php echo (in_array('users_groups', $this->permission->permissions)) ? anchor('/admin/users/edit_group/'.$group['groupID'], $group['groupName']) : $group['groupName']; ?></td>
		<td class="tiny">
			<?php echo anchor('/admin/users/edit_group/'.$group['groupID'], 'Edit'); ?>
		</td>
		<td class="tiny">
			<?php echo anchor('/admin/users/delete_group/'.$group['groupID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no permission groups set up yet.</p>

<?php endif; ?>