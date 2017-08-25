<script type="text/javascript">
$(function(){
	$.listen('click', 'a.showform', function(event){showForm(this,event);});
	$.listen('click', 'input#cancel', function(event){hideForm(this,event);});
});
</script>

<h1 class="headingleft">Discount Codes</h1>

<div class="headingright">	
	<a href="<?php echo site_url('/admin/shop/add_discount'); ?>" class="showform button blue">Add Discount</a>
</div>

<div class="clear"></div>
<div class="hidden"></div>

<?php if ($shop_discounts): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th>Code</th>
		<th>Calculated On</th>
		<th>Discount</th>
		<th>Expiry Date</th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php foreach ($shop_discounts as $discount): ?>
	<tr>
		<td><?php echo anchor('/admin/shop/edit_discount/'.$discount['discountID'], $discount['code'], 'class="showform"'); ?></td>
		<td><?php
			if ($discount['type'] == 'P') echo 'Product';
			elseif ($discount['type'] == 'C') echo 'Category';
			else echo 'Total';
		?></td>
		<td><?php echo ($discount['modifier'] == 'A') ? currency_symbol().number_format($discount['discount'],2) : $discount['discount'].'%'; ?></td>
		<td><?php echo (strtotime($discount['expiryDate']) < time()) ? 
			'<span style="color:red;">'.dateFmt($discount['expiryDate']).'</span>' : 
			'<span style="color:green;">'.dateFmt($discount['expiryDate']).'</span>'; ?></td>
		<td><?php echo anchor('/admin/shop/edit_discount/'.$discount['discountID'], 'Edit', 'class="showform"'); ?></td>
		<td><?php echo anchor('/admin/shop/delete_discount/'.$discount['discountID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?></td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>You have not set up any discount codes yet.</p>

<?php endif; ?>