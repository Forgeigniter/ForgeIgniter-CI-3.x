<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/shop/order/upsell'); ?>',$(this).sortable('serialize'),function(data){ });
};

function initOrder(el){
	$(el).sortable({
		axis: 'y',
	    revert: false,
	    delay: '80',
	    opacity: '0.5',
	    update: setOrder
	});
};

$(function(){
	initOrder('ol.order, ol.order ol');
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Upsells</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Upsells</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-shopping-cart"></i>
					<h3 class="box-title">Upsells</h3>
					<div class="box-tools">
					  <a href="<?= site_url('/admin/shop/add_upsell'); ?>" class="showform mb-xs mt-xs mr-xs btn btn-green">Add Upsell</a>
					</div>
				</div>

				<div class="box-body">

					<div class="clear"></div>
					<div class="hidden"></div>

					<?php if ($shop_upsells): ?>

					<hr />

					<form method="post" action="<?php echo site_url('/admin/shop/edit_upsell'); ?>">

						<ol class="order">
						<?php $x=0; foreach ($shop_upsells as $upsell): $x++; ?>
							<li id="shop_upsells-<?php echo $upsell['upsellID']; ?>">
								<div class="col1">
									<span>
										<?php echo $x; ?>.
										If
										<?php if ($upsell['type'] == 'V'): ?>
											the <strong>value of the cart</strong>
											is greater than <strong><?php echo currency_symbol(); ?><?php echo number_format($upsell['value'], 2); ?></strong>:
										<?php elseif ($upsell['type'] == 'N'): ?>
											the <strong>number of products in the cart</strong>
											is greater than <strong><?php echo $upsell['numProducts']; ?></strong>:
										<?php else: ?>
											<?php $products = explode(',', $upsell['productIDs']); ?>
											<?php foreach($products as $product): ?>
												<?php
													$productString = '';
													if ($row = $this->shop->get_product($product)) $productString .= $row['productName'].', ';
												?>
												<?php if ($productString): ?>
													<strong><?php echo substr($productString, 0, -2); ?></strong>
												<?php else: ?>
													<strong>N/A</strong>
												<?php endif; ?>
											<?php endforeach; ?>
											is in the cart:
										<?php endif; ?>
									</span>
								</div>
								<div class="col2">
									Upsell
										<?php if ($upsellProduct = $this->shop->get_product($upsell['productID'])): ?>
											<strong><?php echo $upsellProduct['productName']; ?></strong>
										<?php else: ?>
											<strong>N/A</strong>
										<?php endif; ?>
									<?php if ($upsell['remove']): ?>
										and <strong>remove original products</strong>
									<?php endif; ?>
								</div>
								<div class="buttons">
									<a href="<?php echo site_url('/admin/shop/edit_upsell/'.$upsell['upsellID']); ?>" class="showform"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
									<a href="<?php echo site_url('/admin/shop/delete_upsell/'.$upsell['upsellID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
								</div>
								<div class="clear"></div>
							</li>
						<?php endforeach; ?>
						</ol>

					</form>

					<?php else: ?>

					<p>You haven't set up any Upsells yet.</p>

					<?php endif; ?>

				</div>
			</div> <!-- End Box -->
		</div> <!-- End Row-->

	</section>
