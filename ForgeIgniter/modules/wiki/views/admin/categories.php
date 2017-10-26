<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/wiki/order/cat'); ?>',$(this).sortable('serialize'),function(data){ });
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
		$("#hidecat").removeClass( "hidden", 10, "easeInBack");
		$('div.hidden').slideToggle('300');
	});

	$('a.edit').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('.col1').children('input').show();
		$(this).parent().siblings('.col1').children('span').hide();
		$(this).parent().siblings('.col1').find("input").removeClass('hide');
	});

});

$(function(){
	initOrder('ol.order, ol.order ol');
});
</script>
<style>
#hidecat{
    -webkit-transition: all 0.2s ease;
    -moz-transition: all 0.2s ease;
    -o-transition: all 0.2s ease;
    transition: all 0.2s ease;
}
</style>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Wiki :
		<small>Categories</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/wiki'); ?>"><i class="fa fa-file-text-o"></i> Wiki</a></li>
		<li class="active">Categories</li>
	  </ol>
	</section>

	<!-- Main content -->
    <section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-newspaper-o"></i>
						<h3 class="box-title">Wiki Categories</h3>

						<div class="box-tools">
							<a href="#" id="#btnaddcat" class="toggle mb-xs mt-xs mr-xs btn btn-green">Add Category</a>
							<a href="<?= site_url('/admin/wiki/viewall'); ?>" id="#btnaddcat" class="mb-xs mt-xs mr-xs btn btn-blue">View Pages</a>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">

						<div id="hidecat2" class="hidden">
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
								<?php
									$data = NULL;
									echo form_input('catName', $data['catName'], 'class="formelement" id="catName"');
								?>

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

						</div>

						<div id="hidecat" class="hidden">
							<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

								<div class="col col-md-4">

								<label for="categoryName">Category name:</label>

								<?php
									$data = NULL;
									echo form_input('catName',$data['catName'], 'class="formelement" id="catName"');
								?>

								<input type="submit" value="Add Category" id="submit" class="button" />

								</div>

								<br class="clear" />

							</form>
						</div>


						<?php if ($parents): ?>

							<hr />

							<ol class="order">
							<?php foreach ($parents as $cat): ?>
								<li id="wiki_cats-<?php echo $cat['catID']; ?>" class="<?php echo (@$children[$cat['catID']]) ? 'haschildren' : ''; ?>">
									<div class="col1">
										<span><strong><?php echo $cat['catName']; ?></strong></span>
									</div>
									<div class="col2">&nbsp;</div>
									<div class="buttons">
										<a href="<?php echo site_url('/admin/wiki/edit_cat/'.$cat['catID']); ?>" class="showform"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
										<a href="<?php echo site_url('/admin/wiki/delete_cat/'.$cat['catID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
									</div>
									<div class="clear"></div>
									<?php if (@$children[$cat['catID']]): ?>
										<ol class="subcat">
										<?php foreach ($children[$cat['catID']] as $child): ?>
											<li id="wiki_cat-<?php echo $child['catID']; ?>">
												<div class="col1">
													<span class="padded">--</span>
													<span><strong><?php echo $child['catName']; ?></strong></span>
												</div>
												<div class="col2">&nbsp;</div>
												<div class="buttons">
													<a href="<?php echo site_url('/admin/wiki/edit_cat/'.$child['catID']); ?>" class="showform"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
													<a href="<?php echo site_url('/admin/wiki/delete_cat/'.$child['catID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
												</div>
												<div class="clear"></div>
											</li>
										<?php endforeach; ?>
										</ol>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
							</ol>

						<?php else: ?>

						<p>You haven't set up any wiki categories yet.</p>

						<?php endif; ?>

					</div> <!-- End Box Body -->

				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>



<div class="headingright">
	<a href="<?php echo site_url('/admin/wiki/add_cat'); ?>" class="showform button blue">Add Category</a>
</div>
