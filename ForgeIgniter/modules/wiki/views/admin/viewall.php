<h1 class="headingleft">Wiki</h1>

<div class="headingright">
	<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/wiki/add_page'); ?>" class="button">Add Page</a>
	<?php endif; ?>
</div>

<?php if ($wiki): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default">
	<tr>
		<th><?php echo order_link('/admin/wiki/viewall','pageName','Page'); ?></th>
		<th><?php echo order_link('/admin/wiki/viewall','uri','URI'); ?></th>
		<th><?php echo order_link('/admin/wiki/viewall','datecreated','Date'); ?></th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>		
	</tr>
<?php foreach ($wiki as $page): ?>
	<tr>
		<td><?php echo (in_array('wiki_edit', $this->permission->permissions)) ? anchor('/admin/wiki/edit_page/'.$page['pageID'], $page['pageName']) : $page['pageName']; ?></td>	
		<td><?php echo $page['uri']; ?></td>
		<td><?php echo dateFmt($page['dateCreated']); ?></td>
		<td><?php echo anchor('/wiki/'.$page['uri'], 'View'); ?></td>		
		<td class="tiny">
			<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
				<?php echo anchor('/admin/wiki/edit_page/'.$page['pageID'], 'Edit'); ?>
			<?php endif; ?>
		</td>
		<td class="tiny">
			<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
				<?php echo anchor('/admin/wiki/delete_page/'.$page['pageID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no wiki pages yet.</p>

<?php endif; ?>

