
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

	<h1 class="headingleft">Edit
		<?php echo ($type == 'C' || $type == 'J') ? 'File' : 'Include'; ?>
		<?php
			if ($type == 'C') $typeLink = 'css';
			elseif ($type == 'J') $typeLink = 'js';
			else $typeLink = '';
		?>
		<small>(<a href="<?php echo site_url('/admin/pages/includes'); ?>/<?php echo $typeLink; ?>">Back to Includes</a>)</small>
	</h1>

	<div class="headingright">
		<input type="submit" value="Save Changes" id="submit" class="button" />
	</div>

	<div class="clear"></div>

	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>
	<?php if (isset($message)): ?>
		<div class="message">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>

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

		Also Load settings maybe from config.
		 ckeditor_config.php

		<textarea name='body' id="body" class="code editor"><?=set_value('body', $data['body']);?></textarea>
		*/?>
		<?php //CKeditor

		$content = set_value('body', $data['body']);
		?>
		<script src="<?= site_url('static/themes/assets/editors/ckeditor/ckeditor.js'); ?>"></script>

		<textarea name='body' id="body" class=""><?=$content?></textarea>

		<script type="text/javascript" >

			<?=$this->config->item('settingsIncludes')?>

		</script>

		<br class="clear" />
	</div>

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

	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

</form>
