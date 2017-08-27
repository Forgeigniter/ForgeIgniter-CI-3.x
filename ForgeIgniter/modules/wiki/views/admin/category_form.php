<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Category</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<label for="catName">Title:</label>
	<?php echo @form_input('catName', $data['catName'], 'class="formelement" id="catName"'); ?>
	<br class="clear" />

	<label for="templateID">Parent:</label>
	<?php
	if ($parents):
		$options = '';		
		$options[0] = 'Top Level';		
		foreach ($parents as $parent):
			if ($parent['catID'] != @$data['catID']) $options[$parent['catID']] = $parent['catName'];
		endforeach;
		
		echo @form_dropdown('parentID',$options,$data['parentID'],'id="parentID" class="formelement"');
	endif;
	?>	
	<br class="clear" />

	<label for="description">Description:</label>
	<?php echo @form_textarea('description', set_value('description', $data['description']), 'class="formelement small"'); ?>
	<br class="clear" /><br />

	<input type="submit" value="Save Changes" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />
	
</form>

<br class="clear" />
