<h1>Most Recent Changes</h1>

<?php if ($changes): ?>

<table class="default">
	<tr>
		<th>Page</th>
		<th>Notes</th>
		<th>Date</th>		
		<th>User</th>		
		<th class="tiny">&nbsp;</th>	
	</tr>
<?php foreach ($changes as $change): ?>
	<tr>
		<td><?php echo (in_array('wiki_edit', $this->permission->permissions)) ? anchor('/admin/wiki/edit_page/'.$change['pageID'], $change['pageName']) : $change['pageName']; ?></td>	
		<td><?php echo $change['notes']; ?></td>
		<td><?php echo dateFmt($change['dateCreated']); ?></td>
		<td><?php echo $this->wiki->lookup_user($change['userID'], TRUE); ?></td>
		<td><?php echo anchor('/wiki/'.$change['uri'], 'View'); ?></td>
	</tr>
<?php endforeach; ?>
</table>


<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no wiki pages yet.</p>

<?php endif; ?>

