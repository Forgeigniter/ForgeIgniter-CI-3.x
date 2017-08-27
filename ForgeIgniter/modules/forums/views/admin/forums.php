<h1 class="headingleft">Forums</h1>

<div class="headingright">
	<?php if (in_array('forums_edit', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/forums/add_forum'); ?>" class="button">Add Forum</a>
	<?php endif; ?>
</div>

<?php if ($forums): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th><?php echo order_link('/admin/forums/forums','forumName','Forum'); ?></th>
		<th><?php echo order_link('/admin/forums/forums','datecreated','Description'); ?></th>
		<th><?php echo order_link('/admin/forums/forums','active','Active'); ?></th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php foreach ($forums as $forum): ?>
	<tr>
		<td><?php echo (in_array('forums_edit', $this->permission->permissions)) ? anchor('/admin/forums/edit_forum/'.$forum['forumID'], $forum['forumName']) : $forum['forumName']; ?></td>
		<td><?php echo $forum['description']; ?></td>
		<td>
			<?php
				if ($forum['active']) echo 'Yes';
				if (!$forum['active']) echo 'No';
			?>
		</td>
		<td><?php echo anchor('/forums/viewforum/'.$forum['forumID'], 'View'); ?></td>	
		<td class="tiny">
			<?php if (in_array('forums_edit', $this->permission->permissions)): ?>
				<?php echo anchor('/admin/forums/edit_forum/'.$forum['forumID'], 'Edit'); ?>
			<?php endif; ?>
		</td>
		<td class="tiny">			
			<?php if (in_array('forums_delete', $this->permission->permissions)): ?>
				<?php echo anchor('/admin/forums/delete_forum/'.$forum['forumID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no forums yet.</p>

<?php endif; ?>

