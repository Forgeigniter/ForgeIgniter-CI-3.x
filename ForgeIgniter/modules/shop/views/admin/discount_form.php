<script type="text/javascript">
function showObjects(el){
	if ($(el).val() == 'P'){
		$('div#products').show();
		$('div#categories').hide();
		$('select#productID').removeAttr('disabled');
		$('select#catID').attr('disabled', 'disabled');
	} else if ($(el).val() == 'C'){
		$('div#products').hide();
		$('div#categories').show();
		$('select#productID').attr('disabled', 'disabled');
		$('select#catID').removeAttr('disabled');

	} else {
		$('div#products').slideUp(200);
		$('div#categories').slideUp(200);
	}
}

function showModifier(el){
	if ($(el).val() == 'P'){
		$('span#percentage').show();
		$('span#currency').hide();
	} else {
		$('span#percentage').hide();
		$('span#currency').show();
	}
}

$(function(){
	$('input.datebox').datepicker({dateFormat: 'dd M yy'});
	$('select#modifier').change(function(){
		showModifier($(this));
	});
	$('select#type').change(function(){
		showObjects($(this));
	});
	showModifier('select#modifier');
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
		<li class="active"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Discount</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">
	  <section class="content">

		<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<div class="row">
			<div class="pull-left">
				<a href="<?php echo site_url('/admin/shop/discounts');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Discounts</a>
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
					<h3 class="box-title"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Discount</h3>
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

						  <label for="code">Code:</label>
	  					<?php echo @form_input('code', $data['code'], 'class="form-control input-style" id="code"'); ?>
	  					<br class="clear" /><br />

	  					<label for="type">Calculated On:</label>
	  					<?php
	  						$values = array(
	  							'T' => 'Total Value of Cart',
	  							'P' => 'Products',
	  							'C' => 'Category'
	  						);
	  						echo @form_dropdown('type',$values,set_value('type', $data['type']), 'id="type" class="form-control input-style"');
	  					?>
	  					<br class="clear" /><br />

	  					<div style="display: none;" id="categories">
	  						<label for="catID">Category:</label>
	  						<?php
	  							$options = '';
	  							$options[0] = 'Select a Category...';
	  							if ($categories):
	  								foreach ($categories as $category):
	  									$options[$category['catID']] = ($category['parentID']) ? '-- '.$category['catName'] : $category['catName'];
	  								endforeach;
	  							endif;
	  							echo @form_dropdown('catID',$options,set_value('catID', $data['objectID']),'id="catID" class="form-control input-style"');
	  						?>
	  						<br class="clear" /><br />
	  					</div>

	  					<div style="display: none;" id="products">
	  						<label for="productID">Product:</label>
	  						<?php
	  							$options = '';
	  							if ($products):
	  								foreach ($products as $product):
	  									$options[$product['productID']] = $product['productName'];
	  								endforeach;
	  							endif;
	  							$objectIDArray = (isset($data['objectID'])) ? @explode(',',$data['objectID']) : $this->input->post('productID');
	  							echo @form_dropdown('productID[]',$options, $objectIDArray, 'id="productID" class="form-control input-style" multiple="multiple"');
	  						?>
	  						<br class="clear" /><br />
	  					</div>

	  					<label for="base">Taken Off:</label>
	  					<?php
	  						$values = array(
	  							'T' => 'Sub Total of Cart',
	  							'P' => 'Product Price (and quantity)'
	  						);
	  						echo @form_dropdown('base',$values,set_value('base', $data['base']), 'id="base" class="form-control input-style"');
	  					?>
	  					<br class="clear" /><br />

	  					<label for="modifier">Modifier:</label>
	  					<?php
	  						$values = array(
	  							'A' => 'Amount',
	  							'P' => 'Percentage'
	  						);
	  						echo @form_dropdown('modifier',$values,set_value('modifier', $data['modifier']), 'id="modifier" class="form-control input-style"');
	  					?>
	  					<br class="clear" /><br />

	  					<label for="discount">Discount:</label>
	  					<span class="price" id="currency"><?php echo currency_symbol(); ?></span>
	  					<?php echo @form_input('discount', $data['discount'], 'class="form-control input-style" id="discount"'); ?>
	  					<span class="price" id="percentage" style="display: none;">%</span>
	  					<br class="clear" /><br />

	  					<label for="expiryDate">Expiry Date:</label>
	  					<?php echo @form_input('expiryDate', dateFmt($data['expiryDate'], 'd M Y'), 'id="expiryDate" class="form-control input-style datebox" readonly="readonly"'); ?>
	  					<br class="clear" /><br />

					  </div>
					</div>

				</div>

			</div>

		</div> <!-- End Main Row -->
	  </form>
	  </section>
	</section>
