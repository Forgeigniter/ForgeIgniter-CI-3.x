<script type="text/javascript">
$(function(){
	// $.listen('click', 'a.showform', function(event){showForm(this,event);});
	//$.listen('click', 'input#cancel', function(event){hideForm(this,event);});
});
</script>

<script type="text/javascript">
// Lets not mess with models just yet.
/*
	$(document).ready(function(e) {
	$('.bootpopup').click(function(){
	var frametarget = $(this).attr('href');
	targetmodal = '#myModal';
		$('#modeliframe').attr("src", frametarget );
	});
});
*/
/*
$(function(){
	$('a.showform').click(function(event){
		event.preventDefault();
		$("/admin/shop/add_discount").show();
		//showForm(this,event);
	});

});
*/

</script>


	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Discount Codes</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Discount Codes</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-shopping-cart"></i>
					<h3 class="box-title">Discount Codes</h3>
					<div class="box-tools">
						<a href="<?= site_url('/admin/shop/add_discount'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Discount</a>
					</div>
				</div>

				<div class="box-body table-responsive no-padding">

					<div class="clear"></div>
					<div class="hidden">
						<p class="hide"><a href="#">x</a></p>
						<div class="inner"></div>
					</div>

					<?php if ($shop_discounts): ?>

					<?php echo $this->pagination->create_links(); ?>

					<table class="table table-hover">
						<tr>
							<th>Code</th>
							<th>Calculated On</th>
							<th>Discount</th>
							<th>Expiry Date</th>
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
							<td>
								<?php echo anchor('/admin/shop/edit_discount/'.$discount['discountID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
								<?php echo anchor('/admin/shop/delete_discount/'.$discount['discountID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php echo $this->pagination->create_links(); ?>

					<?php else: ?>

					<p>You have not set up any discount codes yet.</p>

					<?php endif; ?>

				</div> <!-- End box body -->
			</div> <!-- End Box -->

		</div> <!-- End row -->
	</section>
