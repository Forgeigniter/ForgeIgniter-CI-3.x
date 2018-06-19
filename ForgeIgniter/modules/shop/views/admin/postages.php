<script type="text/javascript">
$(function(){
	$.listen('click', 'a.showform', function(event){showForm(this,event);});
	$.listen('click', 'input#cancel', function(event){hideForm(this,event);});
});
</script>

<h1 class="headingleft">Shipping Costs</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/shop/add_postage'); ?>" class="showform button blue">Add Shipping Rate</a>
</div>

<div class="clear"></div>
<div class="hidden"></div>

<?php if ($shop_postages): ?>
<table class="default">
	<tr>
		<th>Total</th>
		<th>Cost</th>
		<th class="tiny"></th>
		<th class="tiny"></th>
	</tr>
	<?php foreach($shop_postages as $postage): ?>
		<tr>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['total'], 2); ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['cost'], 2); ?></td>
			<td><?php echo anchor('/admin/shop/edit_postage/'.$postage['postageID'], 'Edit', 'class="showform"'); ?></td>
			<td><?php echo anchor('/admin/shop/delete_postage/'.$postage['postageID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\');"'); ?></td>
		</tr>
	<?php endforeach; ?>
</table>

<?php else: ?>

<p>You have not yet set up your shipping costs yet.</p>

<?php endif; ?>
