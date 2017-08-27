<h1>Shop Reviews</h1>

<?php if ($reviews): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th>Date Posted</th>
		<th>Product</th>
		<th>Author</th>
		<th>Email</th>
		<th>Review</th>
		<th class="narrow">Status</th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php foreach ($reviews as $review): ?>
	<tr>
		<td><?php echo dateFmt($review['dateCreated']); ?></td>
		<td><?php echo anchor('/shop/viewproduct/'.$review['productID'], $review['productName']); ?></td>
		<td><?php echo $review['fullName']; ?></td>
		<td><?php echo $review['email']; ?></td>
		<td><?php echo (strlen($review['review'] > 50)) ? substr($review['review'], 0, 50).'...' : $review['review']; ?></td>
		<td><?php echo ($review['active']) ? '<span style="color:green;">Active</span>' : '<span style="color:orange;">Pending</span>'; ?></td>		
		<td><?php echo (!$review['active']) ? anchor('/admin/shop/approve_review/'.$review['reviewID'], 'Approve') : ''; ?></td>
		<td>
			<?php echo anchor('/admin/shop/delete_review/'.$review['reviewID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php endif; ?>

