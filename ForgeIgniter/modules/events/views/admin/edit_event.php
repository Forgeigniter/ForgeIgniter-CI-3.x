<script type="text/javascript">
function preview(el){
	$.post('<?php echo site_url('/admin/events/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.preview').html(data);
	});
}
function previewExcerpt(el){
	$.post('<?php echo site_url('/admin/blog/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.previewExcerpt').html(data);
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

	$('textarea#excerpt').blur(function(){
		previewExcerpt(this);
	});
	previewExcerpt($('textarea#excerpt'));
});
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
	Events :
	<small>Edit</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-calendar"></i> Events</a></li>
	<li class="active">Edit Event</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid">

	<section class="content">

		<form name="form" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

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
					<a href="<?= site_url('/admin/events');;?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Events</a>
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

		<div class="row">
			<div class="col-md-9">
				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Edit Event</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">

						<label for="eventName">Event Title:</label>
						<?php echo @form_input('eventTitle', set_value('eventTitle', $data['eventTitle']), 'id="eventTitle" class="form-control" style="width:50%;"'); ?>
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
						<br class="clear" /><br />

						<div class="buttons">
							<a href="#" class="boldbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>
							<a href="#" class="italicbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>
							<a href="#" class="h1button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>
							<a href="#" class="h2button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>
							<a href="#" class="h3button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>
							<a href="#" class="urlbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_url.png" alt="Insert Link" title="Insert Link" /></a>
							<a href="<?php echo site_url('/admin/images/browser'); ?>" class="ficms_imagebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_image.png" alt="Insert Image" title="Insert Image" /></a>
							<a href="<?php echo site_url('/admin/files/browser'); ?>" class="ficms_filebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_file.png" alt="Insert File" title="Insert File" /></a>
							<a href="#" class="previewbutton"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>
						</div>
						<label for="body">Content (Body):</label>
						<?php
							$options = [
									'name'        => 'description',
									'id'          => 'body',
									'value'       => @set_value('description', $data['description']),
									'rows'        => '15',
									'cols'        => '10',
									'style'       => 'width:50%; margin-right:10px;',
									'class'       => 'form-control formelement code half'
							];
							echo form_textarea($options); ?>
						<div class="preview" style="min-height: 315px;"></div>
						<br class="clear" /><br />

					</div><!-- End Box Body -->
				</div> <!-- End Box -->
			</div>

			<!-- Right Sidebar -->
			<div class="col-md-3">
				<div class="box box-primary">

					<div class="box-header with-border">
						<i class="fa fa-cogs"></i>
						<h3 class="box-title">Place &amp; Time</h3>
					</div>

					<div class="box-body" style="padding-bottom:20px;">

						<label for="eventDate">Start Date:</label>
						<?php echo @form_input('eventDate', date('d M Y', strtotime($data['eventDate'])), 'id="eventDate" class="form-control datebox" readonly="readonly"'); ?>
						<br class="clear" />

						<label for="eventEnd">End Date:</label>
						<?php echo @form_input('eventEnd', (($data['eventEnd'] > 0) ? date('d M Y', strtotime($data['eventEnd'])) : ''), 'id="eventEnd" class="form-control input-style datebox" readonly="readonly"'); ?>
						<div class="help-tip">
							<p>Optional, useful if the event goes on for more than one day.</p>
						</div>
						<br class="clear" />

						<label for="time">Time:</label>
						<?php echo @form_input('time', set_value('time', $data['time']), 'id="time" class="form-control"'); ?>
						<br class="clear" />

						<label for="location">Location:</label>
						<?php echo @form_input('location', set_value('location', $data['location']), 'id="location" class="form-control"'); ?>

					</div>
				</div> <!-- End Box -->

				<div class="box box-primary">

					<div class="box-header with-border">
						<i class="fa fa-cogs"></i>
						<h3 class="box-title">Options &amp; Publishing</h3>
					</div>

					<div class="box-body" style="padding-bottom:20px;">

						<label for="featured">Featured:</label>
						<?php
							$values = array(
								0 => 'No',
								1 => 'Yes',
							);
							echo @form_dropdown('featured',$values,set_value('featured', $data['featured']), 'id="featured" class="form-control"');
						?>
						<br class="clear" />

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

						<label for="published">Publish:</label>
						<?php
							$values = array(
								1 => 'Yes',
								0 => 'No (save as draft)',
							);
							echo @form_dropdown('published',$values,set_value('published', $data['published']), 'id="published" class="form-control"');
						?>

					</div>
				</div> <!-- End Box -->
			</div>
		</div> <!-- End Row -->

		</form>

	</section>
