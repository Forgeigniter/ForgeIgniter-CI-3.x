<?php /*
    <link rel="stylesheet" type="text/css" href="<?= base_url() . $this->config->item('staticPath'); ?>/css/admin.css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/lightbox.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/datepicker.css" media="screen" />

    <script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.js"></script>

    <script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.lightbox.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/default.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/admin.js"></script>
*/ ?>
<script language="JavaScript">
	$(function(){
		$('ul#menubar li').hover(
			function() { $('ul', this).css('display', 'block').parent().addClass('hover'); },
			function() { $('ul', this).css('display', 'none').parent().removeClass('hover'); }
		);
	});
</script>

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
        <small>Add Post</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Blog</a></li>
        <li class="active">Add Blog Post</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
	<section class="content">

		<div class="row">
				<div class="pull-left">
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

				<?php if ($errors = validation_errors()): ?>
				<div class="callout callout-danger">
					<h4>Warning!</h4>
					<?php echo $errors; ?>
				</div>
				<?php endif; ?>

				<div class="box box-crey nav-tabs-custom-box">
					<ul class="nav nav-tabs pull-right">
						<li class="pull-left header box-title"><i class="fa fa-edit"></i>Content and Classification</li>
						<li class=""><a href="#tab_gallery" data-toggle="tab" aria-expanded="false">Gallery</a></li>
						<li class=""><a href="#tab_images" data-toggle="tab" aria-expanded="false">Images</a></li>
						<li class=""><a href="#tab_seo" data-toggle="tab" aria-expanded="false">SEO</a></li>
						<li class=""><a href="#tab_content" data-toggle="tab" aria-expanded="false">Content</a></li>
						<li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Details</a></li>
					</ul>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="tab-content">

							<div class="tab-pane autosave active" id="tab_details" style="padding:10px 8px">

								<label for="postName">Title:</label>
								<?php echo @form_input('postTitle', set_value('postTitle', $data['postTitle']), 'id="postTitle" class="form-control" style="width:50%;"'); ?>

								<label>Categories: <small>[<a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
								<div class="categories">
									<?php if ($categories): ?>
									<?php foreach ($categories as $category): ?>
										<div class="category">
											<?php echo form_checkbox('catsArray['.$category['catID'].']', $category['catName']); ?><span><?php echo $category['catName']; ?></span>
										</div>
									<?php endforeach; ?>
									<?php else: ?>
										<div class="category">
											<strong>Warning:</strong> It is strongly recommended that you use categories or this may not appear properly. <a href="<?php echo site_url('/admin/blog/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')"><strong>Please update your categories here</strong></a>.
										</div>
									<?php endif; ?>
								</div>
								<br class="clear" />
								<br class="clear" />

								<label for="excerpt">Introduction (Excerpt):</label>
								<?php
								  $options = [
								      'name'        => 'excerpt',
								      'id'          => 'excerpt',
								      'value'       => @set_value('excerpt', $data['excerpt']),
								      'rows'        => '5',
								      'cols'        => '10',
								      'style'       => 'width:50%; margin-right:10px;',
								      'class'       => 'form-control formelement code short'
								  ];
								  echo form_textarea($options);
								?>
								<div class="previewExcerpt"></div>

							</div><!-- /.details Tab -->

							<div class="tab-pane autosave" id="tab_content">

							<label for="body">Content (Body):</label>

								<div class="buttons" style="padding-bottom:5px;">
									<a href="#" class="boldbutton"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_bold.png" alt="Bold" title="Bold" /></a>
									<a href="#" class="italicbutton"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_italic.png" alt="Italic" title="Italic" /></a>
									<a href="#" class="h1button"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>
									<a href="#" class="h2button"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>
									<a href="#" class="h3button"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>
									<a href="#" class="urlbutton"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_url.png" alt="Insert Link" title="Insert Link" /></a>
									<a href="<?php echo site_url('/admin/images/browser'); ?>" class="ficms_imagebutton"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_image.png" alt="Insert Image" title="Insert Image" /></a>
									<a href="<?php echo site_url('/admin/files/browser'); ?>" class="ficms_filebutton"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_file.png" alt="Insert File" title="Insert File" /></a>
									<a href="#" class="previewbutton"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/btn_save.png" alt="Preview" title="Preview" /></a>
								</div>

								<?php
									$options = [
									        'name'        => 'body',
									        'id'          => 'body',
									        'value'       => @set_value('body', $data['body']),
									        'rows'        => '30',
									        'cols'        => '10',
									        'style'       => 'width:50%; margin-right:10px;',
									        'class'       => 'form-control formelement code half'
									];
									echo form_textarea($options);
								?>
								<div class="preview"></div>

							</div><!-- /.Content Tab -->

							<div class="tab-pane" id="tab_seo" style="padding:10px 8px">
								<label for="seo_keywords">keywords:</label>
								<?php echo form_input('seo_keywords', @set_value('seo_keywords', $data['seo_keywords']), 'id="seo_keywords" class="form-control" style="width:50%;"'); ?>

								<label for="seo_description">Description:</label>
								<?php
									$seo_description = [
													'name'        => 'seo_description',
													'id'          => 'seo_description',
													'value'       => @set_value('seo_description', $data['seo_description']),
													'rows'        => '3',
													'cols'        => '10',
													'style'       => 'width:50%; margin-right:10px;',
													'class'       => 'form-control formelement'
									];
									echo form_textarea($seo_description);
								?>
							</div><!-- /.SEO Tab -->

							<div class="tab-pane" id="tab_images" style="padding:10px 8px">
images

							</div><!-- /.images Tab -->

							<div class="tab-pane" id="tab_gallery" style="padding:10px 8px">
gallery

							</div><!-- /.gallery Tab -->

						</div><!-- /.tab-content -->
					</div><!-- /.box-body -->
				</div><!-- Custom Nav Box -->

			</div> <!-- End Col9 -->

			<!-- Right Sidebar -->
			<div class="col-md-3">

				<div class="box box-primary">

					<div class="box-header with-border">
						<i class="fa fa-cogs"></i>
						<h3 class="box-title">Options &amp; Publishing</h3>
					</div>

					<div class="box-body" style="padding-bottom:20px;">

						<div class="form-group">
            			<label for="tags">Tags :</label>
						<?php
                $options = [
                    'id'			=> 'tags',
                    'name'			=> 'tags',
                    'value'			=> @set_value('tags', $data['tags']),
                    'class'			=> 'form-control',
                    'placeholder'	=> 'Separate tags with a comma,'
                ];

                echo form_input($options); ?>
      			</div>

						<div class="form-group">
							<label for="published">Publish:</label>
								<?php
                    $values = [
                        1 => 'Yes',
                        0 => 'No (save as draft)',
                    ];
                    echo @form_dropdown('published', $values, set_value('published', $data['published']), 'id="published" class="form-control"');
                ?>
     				</div>

						<label for="allowComments">Allow Comments?</label>
						<?php
                $values = [
                    1 => 'Yes',
                    0 => 'No',
                ];
                echo @form_dropdown('allowComments', $values, set_value('allowComments', $data['allowComments']), 'id="allowComments" class="form-control"');
            ?>

					</div> <!-- End Box Body -->
				</div>

			</div> <!-- End Sidebar -->

		</div> <!-- End Main Row -->

	</section>

</form>
