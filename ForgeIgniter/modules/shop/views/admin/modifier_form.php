	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Discount Codes</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Modifier</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">
	  <section class="content extra-padding">

	  <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<div class="row">
			<div class="pull-left">
				<a href="<?php echo site_url('/admin/shop/modifiers');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Modifiers</a>
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
					<h3 class="box-title"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Modifier</h3>
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

						  <?php if ($bands): ?>

					  		<label for="modifierName">Name:</label>
					  		<?php echo @form_input('modifierName', $data['modifierName'], 'class="formelement" id="modifierName"'); ?>
					  		<br class="clear" />

					  		<label for="templateID">Band:</label>
					  		<?php
					  			$options = '';
					  			foreach ($bands as $band):
					  				$options[$band['bandID']] = $band['bandName'];
					  			endforeach;

					  			echo @form_dropdown('bandID', $options, $data['bandID'], 'id="bandID" class="formelement"');
					  		?>
					  		<br class="clear" />

					  		<label for="multiplier">Multiplier:</label>
					  		<?php echo @form_input('multiplier', set_value('multiplier', $data['multiplier']), 'class="formelement small" id="multiplier"'); ?>
					  		<span class="price">x</span>
					  		<br class="clear" />

						  <?php else: ?>

						  You need to create shipping bands before you can add postage modifiers.

						  <?php endif; ?>

						  <br class="clear" />

					 </div>
					</div>

				</div> <!-- end box body -->
			</div> <!-- end box -->
		</div> <!-- end main row -->

	  </form>

	  </section>
	</section>
