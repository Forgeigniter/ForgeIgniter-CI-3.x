
<?php /*
Disable Save & Auto Save For now.
<script type="text/javascript">
$(function(){
	$('input#submit').click(function(){
		$('span.autosave-saving').fadeIn('fast');
		$.post('<?php echo site_url($this->uri->uri_string()); ?>', {
				includeRef: $('#includeRef').val(),
				body: $('#body').val()
		}, function(data){
			$('span.autosave-saving').fadeOut('fast');
			$('span.autosave-complete').text(data).fadeIn('fast').delay(3000).fadeOut('fast');
		});
		return false;
	});
});
</script>
*/ ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
	Pages :
			<small>Edit Template</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url('admin/'); ?>"><i class="fa fa-newspaper-o"></i> Pages</a></li>
			<li class="active">Edit Template</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">
		<section class="content">

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

			<div class="row">
				<div class="pull-left">
					<a href="<?= site_url('/admin/pages/includes');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Includes</a>
				</div>
				<div class="col-md-6 pull-right">
					<input type="submit" value="Save Changes" name="save" id="save" class="btn btn-green margin-bottom save" />
				</div>
			</div>

			<div class="row">

				<div class="box box-crey nav-tabs-custom">

					<ul class="nav nav-tabs pull-right">
						<li class="pull-left header box-title"><i class="fa fa-edit"></i> Edit
							<?php echo ($type == 'C' || $type == 'J') ? 'File' : 'Include'; ?>
							<?php
								if ($type == 'C') $typeLink = 'css';
								elseif ($type == 'J') $typeLink = 'js';
								else $typeLink = '';
							?>
						</li>
						<li class=""><a href="#tab_versions" data-toggle="tab" aria-expanded="false">Versions</a></li>
						<li class="active"><a href="#tab_include" data-toggle="tab" aria-expanded="true">Include</a></li>
					</ul>

					<div class="box-body">
						<div class="tab-content">
							<!-- Templates Tab -->
							<div class="tab-pane active" id="tab_include">


							<?php if ($type == 'C'): ?>

								<label for="includeRef">Filename:</label>
								<?php echo @form_input('includeRef',set_value('includeRef', $data['includeRef']), 'id="includeRef" class="formelement"'); ?>
								<span class="tip">Your file will be found at &ldquo;/css/filename.css&rdquo; (make sure you use the '.css' extension).</span><br class="clear" />

								<?php echo @form_hidden('type', 'C'); ?>

							<?php elseif ($type == 'J'): ?>

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

							<br class="clear" />

								<div class="autosave">
								<!-- Disable for now.
									<span class="autosave-saving">Saving...</span>
									<span class="autosave-complete"></span>
									<label for="body">Markup:</label>
								-->

									<?php // Default
									/*
									Need to make options to select editor [system_settings].
									 - Default
									 - CKeditor
									 - tinymce
									 - etc...
									*/?>

									<script src="<?= site_url('static/themes/assets/editors/ckeditor/ckeditor.js'); ?>"></script>

									<textarea name='body' id="body" class="code editor"><?=set_value('body', $data['body']);?></textarea>

									<script type="text/javascript" >

										<?php
											$ckeditor_settingsIncludes = $this->config->item('settingsIncludes', 'ckeditor_config');
										  echo $ckeditor_settingsIncludes;
										?>

									</script>

									<br class="clear" />
								</div>

							</div>

							<!-- Versions Tab -->
							<div class="tab-pane" id="tab_versions">

								<h2>Versions</h2>

								<ul>
								<?php if ($versions): ?>
									<?php foreach($versions as $version): ?>
										<li>
											<?php if ($data['versionID'] == $version['versionID']): ?>
												<strong><?php echo dateFmt($version['dateCreated'], '', '', TRUE).(($user = $this->core->lookup_user($version['userID'], TRUE)) ? ' <em>(by '.$user.')</em>' : ''); ?></strong>
											<?php else: ?>
												<?php echo dateFmt($version['dateCreated'], '', '', TRUE).(($user = $this->core->lookup_user($version['userID'], TRUE)) ? ' <em>(by '.$user.')</em>' : ''); ?> - <?php echo anchor('/admin/pages/revert_include/'.$version['objectID'].'/'.$version['versionID'], 'Revert', 'onclick="return confirm(\'You will lose unsaved changes. Continue?\');"'); ?>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
								</ul>

							</div>

						</div>

		</section>
	</section>

</form>
