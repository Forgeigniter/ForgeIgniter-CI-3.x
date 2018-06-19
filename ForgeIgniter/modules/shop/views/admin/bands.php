<script type="text/javascript">
$(function(){
	$.listen('click', 'a.showform', function(event){showForm(this,event);});
	$.listen('click', 'input#cancel', function(event){hideForm(this,event);});
});
</script>

<h1 class="headingleft">Shipping Bands</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/shop/add_band'); ?>" class="showform button blue">Add Band</a>
</div>

<div class="clear"></div>
<div class="hidden"></div>

<?php if ($shop_bands): ?>
<table class="default">
	<tr>
		<th>Multiplier</th>
		<th>Name</th>
		<th class="tiny"></th>
		<th class="tiny"></th>
	</tr>
	<?php foreach($shop_bands as $band): ?>
		<tr>
			<td><?php echo $band['multiplier']; ?> <small>x</small></td>
			<td><?php echo $band['bandName']; ?></td>
			<td><?php echo anchor('/admin/shop/edit_band/'.$band['bandID'], 'Edit', 'class="showform"'); ?></td>
			<td><?php echo anchor('/admin/shop/delete_band/'.$band['bandID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\');"'); ?></td>
		</tr>
	<?php endforeach; ?>
</table>

<?php else: ?>

<p>You have not yet set up any shipping bands yet.</p>

<?php endif; ?>
