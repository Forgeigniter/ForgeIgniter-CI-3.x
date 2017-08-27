<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<h1>Add Page</h1>

	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>	

	<label for="pageName">Title:</label>
	<?php echo @form_input('pageName',set_value('pageName', $data['pageName']), 'id="pageName" class="formelement"'); ?>
	<br class="clear" />

	<label for="uri">URI:</label>
	<?php echo @form_input('uri',set_value('uri', $data['uri']), 'id="uri" class="formelement"'); ?>
	<br class="clear" />

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
	
	<input type="submit" value="Save Changes" class="button nolabel" />
	<br class="clear" />
	
</form>
