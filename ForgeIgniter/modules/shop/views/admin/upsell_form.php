<script type="text/javascript">
function showObjects(el){
	if ($(el).val() == 'V'){
		$('div#value').show();
		$('div#numproducts').hide();
		$('div#products').hide();
		$('div#remove').hide();
	} else if ($(el).val() == 'N'){
		$('div#value').hide();
		$('div#numproducts').show();
		$('div#products').hide();
		$('div#remove').hide();
	} else {
		$('div#value').hide();
		$('div#numproducts').hide();
		$('div#products').show();
		$('div#remove').show();
	}
}

$(function(){
	$('select#type').change(function(){
		showObjects($(this));
	});
	showObjects('select#type');
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Discount Codes</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Upsell</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">
	  <section class="content extra-padding">

	  <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<div class="row">
			<div class="pull-left">
				<a href="<?php echo site_url('/admin/shop/upsells');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Upsells</a>
			</div>
			<div class="col-md-3 pull-right">
				<input
					type="submit"
					value="Save Changes"
					class="btn btn-green margin-bottom"
					style="right:4%;position: absolute;top: 0px;"
				/>
			</div>
		</div>

		<!-- Main row -->
		<div class="row">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-edit"></i>
					<?php if (!$this->core->is_ajax()): ?>
					<h3 class="box-title"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Discount</h3>
					<?php endif; ?>
				</div>

				<div class="box-body" style="padding:20px">

					<?php if ($errors = validation_errors()): ?>
					<div class="callout callout-danger">
						<h4>Warning!</h4>
						<?php echo $errors; ?>
					</div>
					<?php endif; ?>

					<div class="row">
					  <div class="col col-md-4" style="padding-left:30px;">

						<label for="type">If the:</label>
						<?php
							$values = array(
								'V' => 'Total value of cart',
								'N' => 'Number of products in the cart',
								'P' => 'Products in cart'
							);
							echo @form_dropdown('type',$values, set_value('type', $data['type']), 'id="type" class="form-control input-style"');
						?>
						<br class="clear" /><br />

						<div id="value">
							<label for="value">Is greater than:</label>
							<span class="price" id="currency"><?php echo currency_symbol(); ?></span>
							<?php echo @form_input('value', $data['value'], 'class="form-control input-style" id="value"'); ?>
							<span class="price" id="percentage" style="display: none;">%</span>
							<br class="clear" /><br />
						</div>

						<div style="display: none;" id="numproducts">
							<label for="discount">Is greater than:</label>
							<?php echo @form_input('numProducts', set_value('numProducts', $data['numProducts']), 'class="form-control input-style" id="discount"'); ?>
							<br class="clear" /><br />
						</div>

						<div style="display: none;" id="products">
							<label for="productIDs">Include:</label>
							<?php
								$options = NULL;
								if ($products):
									foreach ($products as $product):
										$options[$product['productID']] = $product['productName'];
									endforeach;
								endif;
								$objectIDArray = (isset($data['productIDs'])) ? @explode(',',$data['productIDs']) : $this->input->post('productIDs');
								echo @form_dropdown('productIDs[]',$options, $objectIDArray, 'id="productIDs" class="form-control input-style" multiple="multiple"');
							?>
							<br class="clear" /><br />
						</div>

						<label for="productID">Then upsell:</label>
						<?php
							$options = NULL;
							if ($products):
								foreach ($products as $products):
									$options[$products['productID']] = $products['productName'];
								endforeach;
							endif;
							echo @form_dropdown('productID', $options, set_value('productID', $data['productID']), 'id="productID" class="form-control input-style"');
						?>
						<br class="clear" /><br />

						<div style="display: none;" id="remove">
							<label for="remove">Remove original products?</label>
							<?php
								$values = array(
									'0' => 'No',
									'1' => 'Yes',
								);
								echo @form_dropdown('remove',$values, set_value('remove', $data['remove']), 'id="remove" class="form-control input-style"');
							?>
							<br class="clear" />
						</div>

					  </div>
					</div>

				</div> <!-- end box body -->
			</div> <!-- end box -->
		</div> <!-- end main row -->

	  </form>

	  </section>
	</section>
