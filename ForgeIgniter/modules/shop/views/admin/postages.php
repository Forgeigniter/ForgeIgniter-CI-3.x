<script type="text/javascript">
$(function(){
	//$.listen('click', 'a.showform', function(event){showForm(this,event);});
	//$.listen('click', 'input#cancel', function(event){hideForm(this,event);});
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Shipping Costs</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Shipping Costs</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
  			<div class="box box-crey">
  				<div class="box-header with-border">
  					<i class="fa fa-shopping-cart"></i>
  					<h3 class="box-title">Shipping Costs</h3>
  					<div class="box-tools">
  						<a href="<?= site_url('/admin/shop/add_postage'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Shipping Rate</a>
  					</div>
  				</div>

				<div class="box-body table-responsive no-padding">

					<div class="hidden"></div>

					<?php if ($shop_postages): ?>
					<table class="table table-hover">
						<tr>
							<th>Total</th>
							<th>Cost</th>
							<th class="tiny"></th>
						</tr>
						<?php foreach($shop_postages as $postage): ?>
							<tr>
								<td><?php echo currency_symbol(); ?><?php echo number_format($postage['total'], 2); ?></td>
								<td><?php echo currency_symbol(); ?><?php echo number_format($postage['cost'], 2); ?></td>
								<td>
									<?php echo anchor('/admin/shop/edit_postage/'.$postage['postageID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
									<?php echo anchor('/admin/shop/delete_postage/'.$postage['postageID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\');"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>

					<?php else: ?>

					<p>You have not yet set up your shipping costs yet.</p>

					<?php endif; ?>

				</div> <!-- end box body -->

			</div> <!-- box -->
		</div> <!-- end row -->

	</section>
