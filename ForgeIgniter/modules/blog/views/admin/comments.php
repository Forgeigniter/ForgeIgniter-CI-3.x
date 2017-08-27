<h1>Blog comments</h1>

<?php if ($comments): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th>Date Posted</th>
		<th>Post</th>
		<th>Author</th>
		<th>Email</th>
		<th>Comment</th>	
		<th>Status</th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php foreach ($comments as $comment): ?>
	<tr>
		<td><?php echo dateFmt($comment['dateCreated']); ?></td>
		<td><?php echo anchor('/blog/'.dateFmt($comment['uriDate'], 'Y/m/').$comment['uri'], $comment['postTitle']); ?></td>
		<td><?php echo $comment['fullName']; ?></td>
		<td><?php echo $comment['email']; ?></td>
		<td><small><?php echo (strlen($comment['comment'] > 50)) ? htmlentities(substr($comment['comment'], 0, 50)).'...' : htmlentities($comment['comment'],ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?></small></td>						
		<td><?php echo ($comment['active']) ? '<span style="color:green;">Active</span>' : '<span style="color:orange;">Pending</span>'; ?></td>		
		<td><?php echo (!$comment['active']) ? anchor('/admin/blog/approve_comment/'.$comment['commentID'], 'Approve') : ''; ?></td>
		<td>
			<?php echo anchor('/admin/blog/delete_comment/'.$comment['commentID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no comments yet.</p>

<?php endif; ?>

