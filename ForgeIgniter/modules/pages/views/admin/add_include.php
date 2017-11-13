
<?php
	// Note: This is naughty typelink will be removed, type is fine.
 	$typeLink = NULL;
?>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Themes / Templates :
		<small>Add Include Type</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-paint-brush"></i> Themes / Templates</a></li>
		<li class="active">Add Include Type</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

				<div class="row">
					<div class="pull-left">
						<a href="<?= site_url('/admin/pages/includes/'.$typeLink); ?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Includes</a>
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

					<?php if ($errors = validation_errors()): ?>
					<div class="callout callout-danger">
						<h4>Warning!</h4>
						<?php echo $errors; ?>
					</div>
					<?php endif; ?>

					<?php if (isset($message)): ?>
					<div class="callout callout-info">
						<h4>Notice</h4>
						<?php echo $message; ?>
					</div>
					<?php endif; ?>

					<div class="box box-crey">
						<div class="box-header with-border">
							<i class="fa fa-paint-brush"></i>
							<h3 class="box-title">Add <?= $type ?>
								<?php echo ($type == 'css' || $type == 'js') ? 'File' : 'Include'; ?>
								<?php
									if ($type == 'C') $typeLink = 'css';
									elseif ($type == 'J') $typeLink = 'js';
									else $typeLink = NULL;
								?>
							</h3>
						</div>

						<div class="box-body">

							<?php if ($type == 'css'): ?>

								<label for="includeRef">Filename:</label>
								<?php echo @form_input('includeRef',set_value('includeRef', $data['includeRef']), 'id="includeRef" class="formelement"'); ?>
								<span class="tip">Your file will be found at &ldquo;/css/filename.css&rdquo; (make sure you use the '.css' extension).</span><br class="clear" />

								<?php echo @form_hidden('type', 'C'); ?>

							<?php elseif ($type == 'js'): ?>

								<label for="includeRef">Filename:</label>
								<?php echo @form_input('includeRef',set_value('includeRef', $data['includeRef']), 'id="includeRef" class="formelement"'); ?>
								<span class="tip">Your file will be found at &ldquo;/js/filename.js&rdquo; (make sure you use the '.js' extension).</span><br class="clear" />

								<?php echo @form_hidden('type', 'J'); ?>

							<?php else: ?>

								<label for="includeRef">Reference:</label>
								<?php echo @form_input('includeRef',set_value('includeRef', $data['includeRef']), 'id="includeRef" class="formelement"'); ?>
								<span class="tip">To access this include just use {include:REFERENCE} in your template.</span><br class="clear" />

								<?php echo @form_hidden('type', 'H'); ?>

							<?php endif; ?>

								<div class="autosave">

									<script src="<?= site_url('static/themes/assets/editors/ckeditor/ckeditor.js'); ?>"></script>

									<textarea name='body' id="body" class="code editor"><?=@set_value('body', $data['body']);?></textarea>

									<script type="text/javascript" >
										<?=$this->config->item('settingsIncludes')?>
									</script>

									<br class="clear" />
								</div>

						</div> <!-- end body -->

					</div> <!-- end box -->

				</div> <!-- end row -->

			</form>

			</div> <!-- end row -->
		</section>
