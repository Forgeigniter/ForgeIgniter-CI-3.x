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
		<small>Shipping Modifiers</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Shipping Modifiers</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-shopping-cart"></i>
					<h3 class="box-title">Shipping Modifiers</h3>
					<div class="box-tools">
						<a href="<?= site_url('/admin/shop/add_modifier'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Modifier</a>
					</div>
				</div>

				<div class="box-body table-responsive no-padding">

					<div class="clear"></div>
					<div class="hidden"></div>

					<?php if ($shop_modifiers): ?>
					<table class="table table-hover">
						<tr>
							<th>Multiplier</th>
							<th>Name</th>
							<th>Band</th>
							<th class="tiny"></th>
						</tr>
						<?php foreach($shop_modifiers as $modifier): ?>
							<tr>
								<td><?php echo $modifier['multiplier']; ?> <small>x</small></td>
								<td><?php echo $modifier['modifierName']; ?></td>
								<td><?php echo $modifier['bandName']; ?></td>
								<td>
									<?php echo anchor('/admin/shop/edit_modifier/'.$modifier['modifierID'], 'Edit', 'class="showform"'); ?>
									<?php echo anchor('/admin/shop/delete_modifier/'.$modifier['modifierID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\');"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>

					<?php else: ?>

					<p>You have not yet set up any shipping modifiers yet.</p>

					<?php endif; ?>

				</div> <!-- end box body -->
			</div> <!-- end box -->
		</div> <!-- end row -->

	</section>
