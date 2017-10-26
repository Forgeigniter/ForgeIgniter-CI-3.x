	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Wiki :
		<small>Add Page</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/wiki'); ?>"><i class="fa fa-file-text-o"></i> Wiki</a></li>
		<li class="active">Add Page</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<?php if ($errors = validation_errors()): ?>
		<div class="callout callout-danger">
			<h4>Warning!</h4>
			<?php echo $errors; ?>
		</div>
		<?php endif; ?>

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-file-text-o"></i>
					<h3 class="box-title">Add Page</h3>
					<div class="box-tools">
						<input
							type="submit"
							value="Add Page"
							class="btn btn-green margin-bottom"
							style="right:4%;position: absolute;top: 0px;"
						/>
					</div>
				</div>

				<div class="box-body">

					<label for="pageName">Title:</label>
					<?php echo @form_input('pageName',set_value('pageName', $data['pageName']), 'id="pageName" class="formelement"'); ?>
					<br class="clear" /><br />

					<label for="uri">URI:</label>
					<?php echo @form_input('uri',set_value('uri', $data['uri']), 'id="uri" class="formelement"'); ?>
					<br class="clear" /><br />

					<label for="category">Category: <small>[<a href="<?php echo site_url('/admin/wiki/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
					<?php
						$options[''] = 'No Category';
					if ($categories):
						foreach ($categories as $category):
							$options[$category['catID']] = $category['catName'];
						endforeach;

						echo @form_dropdown('catID',$options,set_value('catID', $data['catID']),'id="category" class="formelement"');
					endif;
					?>
					<br class="clear" /><br />

				</div>
			</div>
		</div>

		</form>

	</section>
