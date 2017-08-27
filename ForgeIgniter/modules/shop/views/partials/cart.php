<?php 
if ($cart):

	foreach ($cart as $key => $item): 

		$variationHTML = '';
		
		// get variation 1
		if ($item['variation1']) $variationHTML .= ' ('.$this->site->config['shopVariation1'].': '.$item['variation1'].')';
		
		// get variations 2
		if ($item['variation2']) $variationHTML .= ' ('.$this->site->config['shopVariation2'].': '.$item['variation2'].')';
	
		// get variations 3
		if ($item['variation3']) $variationHTML .= ' ('.$this->site->config['shopVariation3'].': '.$item['variation3'].')';
	
		$key = $this->core->encode($key);
?>

<tr>
	<td><a href="<?php site_url('/shop/')?><?php echo $item['productID']; ?>/<?php echo strtolower(url_title($item['productName'])); ?>"><?php echo $item['productName']; ?><?php echo $variationHTML; ?></a></td>
	<?php if ($this->uri->segment(2) == 'checkout'): ?>
		<td><?php echo $item['quantity']; ?> <a href="/shop/cart/remove/<?php echo $key; ?>">[remove]</a></td>
	<?php else: ?>
		<td><input name="quantity[<?php echo $key; ?>]" type="text" size="2" maxlength="2" value="<?php echo $item['quantity']; ?>" /> <a href="/shop/cart/remove/<?php echo $key; ?>">[remove]</a></td>
	<?php endif; ?>
	<td><?php echo currency_symbol(); ?><?php echo number_format(($item['price'] * $item['quantity']), 2); ?></td>
</tr>

<?php endforeach; ?>
<?php
	// find out if there is a donation (adding it after the postage)
	if ($this->session->userdata('cart_donation') > 0):
?>
<tr>
	<td>Donation</td>
	<td>1 <a href="/shop/cart/remove_donation/">[remove]</a></td>
	<td><?php echo currency_symbol(); ?><?php echo number_format($this->session->userdata('cart_donation'), 2); ?></td>
</tr>
<?php endif; ?>
<?php endif; ?>