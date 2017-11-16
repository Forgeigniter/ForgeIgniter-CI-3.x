<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/shop/order/cat'); ?>',$(this).sortable('serialize'),function(data){ });
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
	<small>Categories</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
	<li class="active">Categories</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid">
	<section class="content">
		<div class="row extra-padding">

			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-shopping-cart"></i>
					<h3 class="box-title">Shop Categories</h3>

					<div class="box-tools">
						<a href="<?= site_url('/admin/shop/add_cat'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Category</a>
						<a href="<?= site_url('/admin/shop/products'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue">View Products</a>
					</div>
				</div>

				<div class="box-body">

					<div class="hidden"></div>

					<?php if ($parents): ?>

					<form method="post" action="<?php echo site_url('/admin/shop/edit_cat'); ?>">

						<ol class="order">
						<?php foreach ($parents as $cat): ?>
							<li id="shop_cats-<?php echo $cat['catID']; ?>" class="<?php echo (@$children[$cat['catID']]) ? 'haschildren' : ''; ?>">
								<div class="col1">
									<span><strong><?php echo $cat['catName']; ?></strong></span>
									<small>(<?php echo $cat['catSafe']; ?>)</small>
								</div>
								<div class="col2">&nbsp;</div>
								<div class="buttons">
									<a href="<?php echo site_url('/admin/shop/edit_cat/'.$cat['catID']); ?>" class=""><img src="<?= base_url($this->config->item('staticPath')); ?>/images/btn_edit.png" alt="Edit" /></a>
									<a href="<?php echo site_url('/admin/shop/delete_cat/'.$cat['catID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?= base_url($this->config->item('staticPath')); ?>/images/btn_delete.png" alt="Delete" /></a>
								</div>
								<div class="clear"></div>
								<?php if (@$children[$cat['catID']]): ?>
									<ol class="subcat">
									<?php foreach ($children[$cat['catID']] as $child): ?>
										<li id="shop_cats-<?php echo $child['catID']; ?>">
											<div class="col1">
												<span class="padded">--</span>
												<span><strong><?php echo $child['catName']; ?></strong></span>
											</div>
											<div class="col2">&nbsp;</div>
											<div class="buttons">
												<a href="<?php echo site_url('/admin/shop/edit_cat/'.$child['catID']); ?>" class=""><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/btn_edit.png" alt="Edit" /></a>
												<a href="<?php echo site_url('/admin/shop/delete_cat/'.$child['catID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?= base_url($this->config->item('staticPath')); ?>/images/btn_delete.png" alt="Delete" /></a>
											</div>
											<div class="clear"></div>
										</li>
									<?php endforeach; ?>
									</ol>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
						</ol>

					</form>

					<?php else: ?>

					<p>You haven't set up any shop categories yet.</p>

					<?php endif; ?>

				</div>

			</div> <!-- End Box -->

		</div> <!-- End Row -->
	</section>
</section>
