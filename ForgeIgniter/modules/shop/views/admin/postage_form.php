	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Shipping Postage</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Postage</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">
	  <section class="content extra-padding">

	  <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<div class="row">
  			<div class="pull-left">
  				<a href="<?php echo site_url('/admin/shop/postages');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Postages</a>
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
					<h3 class="box-title"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Postage</h3>
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

						<label for="total">Total:</label>
					  	<span class="price"><?php echo currency_symbol(); ?></span><?php echo @form_input('total', $data['total'], 'class="form-control input-style" id="total"'); ?>
					  	<span class="tip">When the shopping cart total reaches the given amount, then this rate will be applied.</span>
					  	<br class="clear" /><br />

					  	<label for="cost">Cost:</label>
					  	<span class="price"><?php echo currency_symbol(); ?></span><?php echo @form_input('cost', $data['cost'], 'class="form-control input-style" id="cost"'); ?>
					  	<span class="tip">What do you want to charge for this rate?</span>
					  	<br class="clear" /><br />

					  </div>
					</div>

				</div> <!-- end box body -->
		 	</div> <!-- end box -->
		</div> <!-- end main row -->

	  </form>

	  </section>
	</section>
