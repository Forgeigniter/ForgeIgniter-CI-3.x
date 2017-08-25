<script type="text/javascript">
function preview(el){
	$.post('<?php echo site_url('/admin/events/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.preview').html(data);
	});
}
$(function(){
	$('input.datebox').datepicker({dateFormat: 'dd M yy'});
	$('textarea#body').focus(function(){
		$('.previewbutton').show();
	});
	$('textarea#body').blur(function(){
		preview(this);
	});
	preview($('textarea#body'));	
});
</script>

<form name="form" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<h1 class="headingleft">Edit Event <small>(<a href="<?php echo site_url('/admin/events'); ?>">Back to Events</a>)</small></h1>
	
	<div class="headingright">
		<input type="submit" value="Save Changes" class="button" />
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

	<h2 class="underline">Place and Time</h2>

	<label for="eventName">Event Title:</label>
	<?php echo @form_input('eventTitle', set_value('eventTitle', $data['eventTitle']), 'id="eventTitle" class="formelement"'); ?>
	<br class="clear" />

	<label for="eventDate">Start Date:</label>
	<?php echo @form_input('eventDate', date('d M Y', strtotime($data['eventDate'])), 'id="eventDate" class="formelement datebox" readonly="readonly"'); ?>
	<br class="clear" />

	<label for="eventEnd">End Date:</label>
	<?php echo @form_input('eventEnd', (($data['eventEnd'] > 0) ? date('d M Y', strtotime($data['eventEnd'])) : ''), 'id="eventEnd" class="formelement datebox" readonly="readonly"'); ?>
	<span class="tip">This is optional and useful if the event goes on for more than one day.</span>
	<br class="clear" />

	<label for="time">Time:</label>
	<?php echo @form_input('time', set_value('time', $data['time']), 'id="time" class="formelement"'); ?>
	<br class="clear" />

	<label for="location">Location:</label>
	<?php echo @form_input('location', set_value('location', $data['location']), 'id="location" class="formelement"'); ?>
	<br class="clear" /><br />

	<h2 class="underline">Event Description</h2>	
	
	<div class="buttons">
		<a href="#" class="boldbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>
		<a href="#" class="italicbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>
		<a href="#" class="h1button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>
		<a href="#" class="h2button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>
		<a href="#" class="h3button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>	
		<a href="#" class="urlbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_url.png" alt="Insert Link" title="Insert Link" /></a>
		<a href="<?php echo site_url('/admin/images/browser'); ?>" class="halogycms_imagebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_image.png" alt="Insert Image" title="Insert Image" /></a>
		<a href="<?php echo site_url('/admin/files/browser'); ?>" class="halogycms_filebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_file.png" alt="Insert File" title="Insert File" /></a>
		<a href="#" class="previewbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>	
	</div>
	<label for="body">Body:</label>
	<?php echo @form_textarea('description', set_value('description', $data['description']), 'id="body" class="formelement code half"'); ?>
	<div class="preview"></div>
	<br class="clear" /><br />

	<label for="excerpt">Excerpt:</label>
	<?php echo @form_textarea('excerpt', set_value('excerpt', $data['excerpt']), 'id="excerpt" class="formelement code short"'); ?>
	<br class="clear" /><br />

	<h2 class="underline">Publishing</h2>
	
	<label for="featured">Featured:</label>
	<?php 
		$values = array(
			0 => 'No',
			1 => 'Yes',
		);
		echo @form_dropdown('featured',$values,set_value('featured', $data['featured']), 'id="featured"'); 
	?>
	<br class="clear" />	

	<label for="tags">Tags: <br /></label>
	<?php echo @form_input('tags', set_value('tags', $data['tags']), 'id="tags" class="formelement"'); ?>
	<span class="tip">Separate tags with spaces (e.g. &ldquo;event popular london&rdquo;)</span>
	<br class="clear" />

	<label for="published">Publish:</label>
	<?php 
		$values = array(
			1 => 'Yes',
			0 => 'No (save as draft)',
		);
		echo @form_dropdown('published',$values,set_value('published', $data['published']), 'id="published"'); 
	?>
	<br class="clear" /><br />

	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
