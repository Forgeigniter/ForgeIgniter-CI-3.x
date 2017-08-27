<?php if ($errors = validation_errors()): ?>
	<div class="error clear">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default" style="width: 450px; float: left;">

	<label for="fileRef">Reference:</label>
	<?php echo @form_input('fileRef', $data['fileRef'], 'class="formelement" id="fileRef"'); ?>
	<br class="clear" />

	<label for="folderID">Folder: <small>[<a href="<?php echo site_url('/admin/files/folders'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
	<?php
		$options[0] = 'No Folder';
		if ($folders):
			foreach ($folders as $folderID):
				$options[$folderID['folderID']] = $folderID['folderName'];
			endforeach;
		endif;
			
		echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="folderID" class="formelement"');
	?>	
	<br class="clear" /><br />

	<input type="submit" value="Save Changes" class="button nolabel" id="submit" />
	<a href="<?php echo $this->session->userdata('lastPage'); ?>" class="button cancel grey">Cancel</a>
	<br class="clear" />
	
</form>

<br class="clear" />