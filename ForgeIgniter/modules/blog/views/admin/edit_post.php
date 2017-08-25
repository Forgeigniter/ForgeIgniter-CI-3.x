<script type="text/javascript">
function preview(el){
	$.post('<?php echo site_url('/admin/blog/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.preview').html(data);
	});
}
function previewExcerpt(el){
	$.post('<?php echo site_url('/admin/blog/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.previewExcerpt').html(data);
	});
}
$(function(){
	$('div.category>span, div.category>input').hover(
		function() {
			if (!$(this).prev('input').attr('checked') && !$(this).attr('checked')){
				$(this).parent().addClass('hover');
			}
		},
		function() {
			if (!$(this).prev('input').attr('checked') && !$(this).attr('checked')){
				$(this).parent().removeClass('hover');
			}
		}
	);	
	$('div.category>span').click(function(){
		if ($(this).prev('input').attr('checked')){
			$(this).prev('input').attr('checked', false);
			$(this).parent().removeClass('hover');
		} else {
			$(this).prev('input').attr('checked', true);
			$(this).parent().addClass('hover');
		}
	});
	$('textarea#body').focus(function(){
		$('.previewbutton').show();
	});
	$('textarea#body').blur(function(){
		preview(this);
	});
	$('input.datebox').datepicker({dateFormat: 'dd M yy'});
	preview($('textarea#body'));

	//Preview Button
	$('textarea#excerpt').focus(function(){
		$('.previewExcerptbutton').show();
	});
	
	$('textarea#excerpt').blur(function(){
		previewExcerpt(this);
	});
	$('input.datebox').datepicker({dateFormat: 'dd M yy'});
	previewExcerpt($('textarea#excerpt'));
	

});
</script>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

<h1 class="headingleft">Edit Blog Post <small>(<a href="<?php echo site_url('/admin/blog'); ?>">Back to Blog Posts</a>)</small></h1>

<div class="headingright">
	<input type="submit" name="view" value="View Post" class="button blue" />
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

<h2 class="underline">Content and Classification</h2>

<label for="postName">Title:</label>
<?php echo @form_input('postTitle', set_value('postTitle', $data['postTitle']), 'id="postTitle" class="formelement"'); ?>
<br class="clear" />

<label>Categories: <small>[<a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
<div class="categories">
	<?php if ($categories): ?>
	<?php foreach($categories as $category): ?>
		<div class="category<?php echo (isset($data['categories'][$category['catID']])) ? ' hover' : ''; ?>">
			<?php echo @form_checkbox('catsArray['.$category['catID'].']', $category['catName'], (isset($data['categories'][$category['catID']])) ? 1 : ''); ?><span><?php echo $category['catName']; ?></span>
		</div>
	<?php endforeach; ?>
	<?php else: ?>
		<div class="category">
			<strong>Warning:</strong> It is strongly recommended that you use categories or this may not appear properly. <a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')"><strong>Please update your categories here</strong></a>.
		</div>
	<?php endif; ?>
</div>
<br class="clear" />

<div class="buttons">
	<a href="#" class="boldbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>
	<a href="#" class="italicbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>
	<a href="#" class="h1button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>
	<a href="#" class="h2button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>
	<a href="#" class="h3button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>	
	<a href="#" class="urlbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_url.png" alt="Insert Link" title="Insert Link" /></a>
	<a href="<?php echo site_url('/admin/images/browser'); ?>" class="halogycms_imagebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_image.png" alt="Insert Image" title="Insert Image" /></a>
	<a href="<?php echo site_url('/admin/files/browser'); ?>" class="halogycms_filebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_file.png" alt="Insert File" title="Insert File" /></a>
	<a href="#" class="previewbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>	
</div>

<div class="autosave">
	<label for="body">Content (Body):</label>
	<?php echo @form_textarea('body', set_value('body', $data['body']), 'id="body" class="formelement code half"'); ?>
	<div class="preview"></div>
</div>
<br class="clear" /><br />

<div class="autosave">
	<label for="excerpt">Introduction (Excerpt):</label>
	<?php echo @form_textarea('excerpt', set_value('excerpt', $data['excerpt']), 'id="excerpt" class="formelement code short"'); ?>
	<div class="previewExcerpt"></div>
</div>

<br class="clear" /><br />

<h2 class="underline">Publishing and Options</h2>

<label for="tags">Tags: <br /></label>
<?php echo @form_input('tags', set_value('tags', $data['tags']), 'id="tags" class="formelement"'); ?>
<span class="tip">Separate tags with a comma (e.g. &ldquo;places, hobbies, favourite work&rdquo;)</span>
<br class="clear" />

<label for="published">Publish:</label>
<?php 
	$values = array(
		1 => 'Yes',
		0 => 'No (save as draft)',
	);
	echo @form_dropdown('published',$values,set_value('published', $data['published']), 'id="published"'); 
?>
<br class="clear" />	

<label for="allowComments">Allow Comments?</label>
<?php 
	$values = array(
		1 => 'Yes',
		0 => 'No',
	);
	echo @form_dropdown('allowComments',$values,set_value('allowComments', $data['allowComments']), 'id="allowComments"'); 
?>
<br class="clear" />

<label for="publishDate">Publish Date:</label>
<?php echo @form_input('publishDate', date('d M Y', strtotime($data['dateCreated'])), 'id="publishDate" class="formelement datebox" readonly="readonly"'); ?>
<br class="clear" />

<br />
	
<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
