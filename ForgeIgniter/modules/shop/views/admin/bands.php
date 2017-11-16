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
		<small>Shipping Bands</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Shipping Bands</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-shopping-cart"></i>
					<h3 class="box-title">Shipping Bands</h3>
					<div class="box-tools">
						<a href="<?= site_url('/admin/shop/add_band'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Band</a>
					</div>
				</div>

				<div class="box-body table-responsive no-padding">

					<div class="hidden"></div>

					<?php if ($shop_bands): ?>
					<table class="table table-hover">
						<tr>
							<th>Multiplier</th>
							<th>Name</th>
							<th class="tiny"></th>
						</tr>
						<?php foreach($shop_bands as $band): ?>
							<tr>
								<td><?php echo $band['multiplier']; ?> <small>x</small></td>
								<td><?php echo $band['bandName']; ?></td>
								<td>
									<?php echo anchor('/admin/shop/edit_band/'.$band['bandID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
									<?php echo anchor('/admin/shop/delete_band/'.$band['bandID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\');"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>

					<?php else: ?>

					<p>You have not yet set up any shipping bands yet.</p>

					<?php endif; ?>

				</div> <!-- End box body -->
			</div> <!-- End Box -->

		</div> <!-- End row -->
	</section>
