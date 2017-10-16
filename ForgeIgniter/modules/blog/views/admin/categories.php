<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/blog/order/cat'); ?>',$(this).sortable('serialize'),function(data){ });
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

	initOrder('ol.order');
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
		Blog :
        <small>Categories</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Blog</a></li>
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
						<h3 class="box-title">Categories</h3>

						<div class="box-tools">
							<a href="#" id="#btnaddcat" class="toggle mb-xs mt-xs mr-xs btn btn-green">Add Category</a>
							<a href="<?= site_url('/admin/blog/viewall'); ?>" id="#btnaddcat" class="mb-xs mt-xs mr-xs btn btn-blue">View Posts</a>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">

						<div id="hidecat" class="hidden">
							<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

								<div class="col col-md-4">

								<label for="categoryName">Category name:</label>

								<?php
									$blog_cats = NULL;
									echo form_input('catName',$blog_cats['catName'], 'class="formelement" id="catName"');
								?>

								<input type="submit" value="Add Category" id="submit" class="button" />

								</div>

								<br class="clear" />

							</form>
						</div>

						<?php if ($categories): ?>

						<form method="post" action="<?php echo site_url('/admin/blog/edit_cat'); ?>">

							<ol class="order">
							<?php foreach ($categories as $category): ?>
								<li id="blog_cats-<?php echo $category['catID']; ?>">
									<div class="col1">
										<span><strong><?php echo $category['catName']; ?></strong> <small>(<?php echo url_title(strtolower(trim($category['catName']))); ?>)</small></span>
										<?php echo form_input($category['catID'].'[catName]', $category['catName'], 'class="formelement hide" title="Category Name"'); ?><input type="submit" class="button hide" value="Edit" />
									</div>
									<div class="col2">
										&nbsp;
									</div>
									<div class="buttons">
										<a href="#" class="edit"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
										<a href="<?php echo site_url('/admin/blog/delete_cat/'.$category['catID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
									</div>
									<div class="clear"></div>
								</li>
							<?php endforeach; ?>
							</ol>

						</form>

						<?php else: ?>

						<p>No blog categories have been created yet.</p>

						<?php endif; ?>

					</div>
				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>
