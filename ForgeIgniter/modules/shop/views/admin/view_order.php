<script type="text/javascript">
$(function(){
	var originalTrackingStatus = $('#trackingStatus').val();
	$('input.confirm').click(function(){
		if ($('#trackingStatus').val() == 'D'){
			return confirm('You are updating this order as Shipped so a shipping confirmation email will be sent to the customer.\n\nProceed?');
		} else if (originalTrackingStatus == 'N' && $('#trackingStatus').val() != 'N'){
			return confirm('You are forcing an unpaid checkout to paid so an order confirmation email will be sent to the customer.\n\nProceed?');
		}
		else return true;
	});
});
</script>

<style type="text/css" media="print">
body, div.content p, div.content table td { font-size: 18px; }
div#header, div#navigation, div#footer, .printhide{ display: none; }
#tpl-3col .col1 { width: 100%; clear: both; margin-bottom: 50px; }
#tpl-3col .col2, #tpl-3col .col3 { width: 410px; padding: 30px; border: 1px dashed #ccc; margin-bottom: 50px; min-height: 300px; }
div.content h2.underline, div.content h3.underline { border: none; }
</style>

<form action="<?php echo site_url($this->uri->uri_string()); ?>" method="post" class="default">

	<h1 class="headingleft">View Order <small class="printhide">(<a href="<?php echo site_url('/admin/shop/orders'); ?>">Back to Orders</a>)</small></h1>

	<div class="headingright">
		<a href="#" class="button blue" onclick="window.print();">Print Shipping Label</a>
		<input type="submit" value="Update Order" class="button printhide confirm" />
	</div>

	<div class="clear"></div>

	<?php if (isset($message)): ?>
		<div class="message">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>

	<div class="message">
		<p>
			<strong>Order ID #:</strong> <?php echo $order['transactionCode']; ?><br />
			<strong>Date:</strong> <?php echo dateFmt($order['dateCreated'], '', '', TRUE); ?><br />
			<?php if ($order['discountCode']): ?>
				<strong>Discount Code:</strong> <?php echo $order['discountCode']; ?>
			<?php endif; ?>
		</p>
	</div>

	<div id="tpl-3col">

		<div class="col1">

			<h2 class="underline">Products Ordered</h2>

			<?php if ($item_orders): ?>
			<table class="default">
				<tr>
					<th>Product</th>
					<th>Quantity</th>
					<th width="80">Price (<?php echo currency_symbol(); ?>)</th>
				</tr>
				<?php foreach ($item_orders as $item):
					$variationHTML = '';
					$downloadHTML = '';

					// get variation 1
					if ($item['variation1']) $variationHTML .= ' ('.$this->site->config['shopVariation1'].': <strong>'.$item['variation1'].'</strong>)';

					// get variations 2
					if ($item['variation2']) $variationHTML .= ' ('.$this->site->config['shopVariation2'].': <strong>'.$item['variation2'].'</strong>)';

					// get variations 3
					if ($item['variation3']) $variationHTML .= ' ('.$this->site->config['shopVariation3'].': <strong>'.$item['variation3'].'</strong>)';

					// check if its a file
					if ($item['fileID'])
					{
						$file = $this->shop->get_file($item['fileID']);
						$downloadHTML .= ' ['.anchor('/files/'.$this->core->encode($file['fileRef'].'|'.$transactionID), 'Download').']';
						$downloadHTML .= ' ['.anchor('/admin/shop/renew_downloads/'.$transactionID, 'Renew Expiry').']';
					}

				?>
				<tr>
					<td>
						<a href="/shop/viewproduct/<?php echo $item['productID']; ?>"><?php echo $item['productName']; ?></a>
						<small><?php echo $variationHTML; ?><?php echo $downloadHTML; ?></small>
					</td>
					<td><?php echo $item['quantity']; ?></td>
					<td><?php echo currency_symbol(); ?><?php echo number_format(($item['price'] * $item['quantity']), 2); ?></td>
				</tr>

			<?php endforeach; ?>

				<?php
					// find out if there is a donation (adding it after the postage)
					if ($order['donation'] > 0):
				?>
					<tr>
						<td>Donation</td>
						<td>1</td>
						<td><?php echo currency_symbol(); ?><?php echo number_format($order['donation'], 2); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ($order['discounts'] > 0): ?>
					<tr class="shade">
						<td colspan="2">Discounts applied:</td>
						<td>(<?php echo currency_symbol(); ?><?php echo number_format($order['discounts'], 2); ?>)</td>
					</tr>
				<?php endif; ?>
				<tr class="shade">
					<td colspan="2">Sub total:</td>
					<td><?php echo currency_symbol(); ?><?php echo number_format(($order['amount'] - $order['postage'] - $order['tax']), 2); ?></td>
				</tr>
				<tr class="shade">
					<td colspan="2">Postage &amp; packing:</td>
					<td><?php echo currency_symbol(); ?><?php echo number_format($order['postage'], 2); ?></td>
				</tr>
				<?php if ($order['tax'] > 0): ?>
					<tr class="shade">
						<td colspan="2">Tax:</td>
						<td><?php echo currency_symbol(); ?><?php echo number_format($order['tax'], 2); ?></td>
					</tr>
				<?php endif; ?>
				<tr class="shade">
					<td colspan="2"><strong>Total amount:</strong></td>
					<td><strong><?php echo currency_symbol(); ?><?php echo number_format($order['amount'], 2); ?></strong></td>
				</tr>

			</table>
			<?php endif; ?>

		</div>

		<div class="col2">

			<h3 class="underline">Shipping Address</h3>

			<p>
				<?php if ($order['firstName'] && $order['lastName']): ?>
					<?php echo $order['firstName'] ?> <?php echo $order['lastName']; ?><br />
				<?php else: ?>
					<em>No name set</em>
				<?php endif; ?>
				<?php echo ($order['address1']) ? $order['address1'].'<br />' : ''; ?>
				<?php echo ($order['address2']) ? $order['address2'].'<br />' : ''; ?>
				<?php echo ($order['address3']) ? $order['address3'].'<br />' : ''; ?>
				<?php echo ($order['city']) ? $order['city'].'<br />' : ''; ?>
				<?php echo ($order['country']) ? lookup_country($order['country']).'<br />' : ''; ?>
				<?php echo ($order['postcode']) ? $order['postcode'].'<br />' : ''; ?>
				<?php echo ($order['phone']) ? $order['phone'] : ''; ?>
				<?php echo ($order['email']) ? mailto($order['email']) : ''; ?>
			</p>

		</div>

		<div class="col3">

			<h3 class="underline">Billing Address</h3>

			<p>
				<?php if ($order['billingAddress1'] || $order['billingAddress2'] || $order['billingCity'] || $order['billingPostcode']): ?>
					<?php echo ($order['firstName']) ? $order['firstName'] : '(no firstname)'; ?> <?php echo ($order['lastName']) ? $order['lastName'] : '(no surname)'; ?><br />
					<?php echo ($order['billingAddress1']) ? $order['billingAddress1'].'<br />' : ''; ?>
					<?php echo ($order['billingAddress2']) ? $order['billingAddress2'].'<br />' : ''; ?>
					<?php echo ($order['billingAddress3']) ? $order['billingAddress3'].'<br />' : ''; ?>
					<?php echo ($order['billingCity']) ? $order['billingCity'].'<br />' : ''; ?>
					<?php echo ($order['billingCountry']) ? lookup_country($order['billingCountry']).'<br />' : ''; ?>
					<?php echo ($order['billingPostcode']) ? $order['billingPostcode'].'<br />' : ''; ?>
				<?php else: ?>
					<small><em>Same as Shipping Address</em></small>
				<?php endif; ?>
			</p>

		</div>
	</div>
	<div class="clear"></div>

	<br />

	<div class="printhide">
		<h2 class="underline">Process Order</h2>

		<label for="trackingStatus">Tracking status:</label>
		<?php
			foreach ($statusArray as $key => $status):
				$options[$key] = $status;
			endforeach;

			if (!$data['paid'])
			{
				$data['trackingStatus'] = 'N';
			}

			echo form_dropdown('trackingStatus',$options,set_value('trackingStatus', $data['trackingStatus']),'id="trackingStatus"');
		?>
		<br class="clear" />

		<label for="notes">Order notes:</label>
		<?php echo form_textarea('notes',set_value('notes', $data['notes']), 'id="notes" class="formelement"'); ?>
		<br class="clear" /><br />

		<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	</div>

</form>
