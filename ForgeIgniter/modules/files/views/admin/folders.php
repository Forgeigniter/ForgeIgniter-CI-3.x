<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/files/order/folder'); ?>',$(this).sortable('serialize'),function(data){ });
};

function initOrder(el){
	$(el).sortable({
		axis: 'y',
	    revert: false,
	    delay: '80',
	    opacity: '0.5',
	    update: setOrder
	});
};

$(function(){
	$('a.toggle').click(function(event){
		event.preventDefault();
		$('div.hidden').slideToggle('400');
		$("div.hidden").removeClass("hidden");
	});

	$('a.edit').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('.col1').children('input').show();
		$(this).parent().siblings('.col1').children('span').hide();
		$(this).parent().siblings('.col1').find("input").removeClass('hide');
	});

	initOrder('ol.order');
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Images / Files :
		<small>Folders</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/files'); ?>"><i class="fa fa-file-o"></i> Images / Files</a></li>
		<li class="active">Folders</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-file-o"></i>
					<h3 class="box-title">File Folders</h3>
						<div class="box-tools">
							<a href="<?= site_url('/admin/files/viewall'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">View Files</a>
							<a href="#" class="toggle mb-xs mt-xs mr-xs btn btn-green">Add Folder</a>
						</div>
					</div>

					<div class="box-body">

						<div class="hidden">
							<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

								<label for="folderName">Folder Name:</label>

								<?php
								$files_folders = NULL;
								 echo form_input('folderName',$files_folders['folderName'], 'class="formelement" id="folderName"'); ?>

								<input type="submit" value="Add Folder" id="submit" class="button" />

								<br class="clear" />

							</form>
						</div>

						<?php if ($folders): ?>

						<form method="post" action="<?php echo site_url('/admin/files/edit_folder'); ?>">

							<ol class="order">
							<?php foreach ($folders as $folder): ?>
								<li id="file_folders-<?php echo $folder['folderID']; ?>">
									<div class="col1">
										<span><strong><?php echo $folder['folderName']; ?></strong></span>
										<?php echo @form_input($folder['folderID'].'[folderName]', $folder['folderName'], 'class="formelement hide" title="folder Name"'); ?><input type="submit" class="button hide" value="Edit" />
									</div>
									<div class="col2">
										&nbsp;
									</div>
									<div class="buttons">
										<a href="#" class="edit"><img src="<?= base_url($this->config->item('staticPath')); ?>/images/btn_edit.png" alt="Edit" /></a>
										<a href="<?php echo site_url('/admin/files/delete_folder/'.$folder['folderID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url().$this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
									</div>
									<div class="clear"></div>
								</li>
							<?php endforeach; ?>
							</ol>

						</form>

						<?php else: ?>

						<p>No folders have been created yet.</p>

						<?php endif; ?>

					</div> <!-- end box body -->

				</div> <!-- end box -->
			</div> <!-- end row -->
		</section>
	</section>
