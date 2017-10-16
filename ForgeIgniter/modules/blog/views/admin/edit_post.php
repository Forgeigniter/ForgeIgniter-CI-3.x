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

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
 		Blog :
        <small>Edit Post</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Blog</a></li>
        <li class="active">Edit Post</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

	<section class="content">

		<div class="row">
				<div class="pull-left">

					<input type="submit" name="view" value="View Post" class="btn btn-blue margin-bottom" style="margin-left: 15px;" />

					<a href="<?php echo site_url('/admin/blog');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Posts</a>

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
			<div class="col-md-9">
				<!-- If error or message -->
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

				<!-- Posts -->
				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-edit"></i>
						<h3 class="box-title">Edit Blog Post</h3>
					</div>
					<div class="box-body" style="padding:20px">

					<label for="postName">Title:</label>
					<?php echo form_input('postTitle', set_value('postTitle', $data['postTitle']), 'id="postTitle" class="form-control" style="width:50%;"'); ?>
					<br class="clear" />

					<label>Categories: <small>[<a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
					<div class="categories">
						<?php if ($categories): ?>
						<?php foreach($categories as $category): ?>
							<div class="category<?php echo (isset($data['categories'][$category['catID']])) ? ' hover' : ''; ?>">
								<?php echo form_checkbox('catsArray['.$category['catID'].']', $category['catName'], (isset($data['categories'][$category['catID']])) ? 1 : ''); ?><span><?php echo $category['catName']; ?></span>
							</div>
						<?php endforeach; ?>
						<?php else: ?>
							<div class="category">
								<strong>Warning:</strong> It is strongly recommended that you use categories or this may not appear properly. <a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')"><strong>Please update your categories here</strong></a>.
							</div>
						<?php endif; ?>
					</div>
					<br class="clear" />

					<div class="autosave">
						<label for="excerpt">Introduction (Excerpt):</label>
						<?php
							$options = [
								'name'        => 'excerpt',
								'id'          => 'excerpt',
								'value'       => set_value('excerpt', $data['excerpt']),
								'rows'        => '5',
								'cols'        => '10',
								'style'       => 'width:50%; margin-right:10px;',
								'class'       => 'form-control formelement code short'
							];
							echo form_textarea($options);
						?>
						<div class="previewExcerpt"></div>
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
						<?php
							$options = [
									'name'        => 'body',
									'id'          => 'body',
									'value'       => set_value('body', $data['body']),
									'rows'        => '30',
									'cols'        => '10',
									'style'       => 'width:50%; margin-right:10px;',
									'class'       => 'form-control formelement code half'
							];
							echo form_textarea($options); ?>
						<div class="preview"></div>
					</div>
					<br class="clear" /><br />

					</div><!-- /.box-body  -->
				</div>
			</div>

			<!-- Right Sidebar -->
			<div class="col-md-3">

				<div class="box box-primary">
					<div class="box-header with-border">
						<i class="fa fa-cogs"></i>
						<h3 class="box-title">Options &amp; Publishing</h3>
					</div>

					<!-- /.box-header -->
					<div class="box-body">

						<div class="form-group">
            			<label for="tags">Tags :</label>
						<?php
							$options = [
								'id'			=> 'tags',
								'name'			=> 'tags',
								'value'			=> set_value('tags', $data['tags']),
								'class'			=> 'form-control',
								'placeholder'	=> 'Separate tags with a comma,'
							];

							echo form_input($options); ?>
            			</div>

						<div class="form-group">
							<label for="published">Publish:</label>
								<?php
									$values = array(
										1 => 'Yes',
										0 => 'No (save as draft)',
									);
									echo form_dropdown('published',$values,set_value('published', $data['published']), 'id="published" class="form-control"');
								?>
           				</div>

						<label for="allowComments">Allow Comments?</label>
						<?php
							$values = array(
								1 => 'Yes',
								0 => 'No',
							);
							echo form_dropdown('allowComments',$values,set_value('allowComments', $data['allowComments']), 'id="allowComments" class="form-control"');
						?>

						<label for="publishDate">Publish Date:</label>
						<?php echo form_input('publishDate', date('d M Y', strtotime($data['dateCreated'])), 'id="publishDate" class="formelement datebox" readonly="readonly"'); ?>

						<br />

					</div> <!-- End box body -->
				</div>

			</div><!-- End rightsidebar -->

		</div><!-- /.row -->
	</section>

</form>
