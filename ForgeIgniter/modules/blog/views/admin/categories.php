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
/*

$(function () {
        $("a.toggle").click(function () {
			$("#hidecat").removeClass( "hidden" );
			$('div.hidden').slideToggle('400');
        });
    });
*/

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

<h1 class="headingleft">Blog Categories</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/blog/viewall'); ?>" class="button blue">View Posts</a>
	<a href="#" id="#btnaddcat" class="toggle button blue">Add Category</a>

</div>

<div class="clear"></div>

<div id="hidecat" class="hidden">
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">
	
		<label for="categoryName">Category name:</label>
		
		<?php
			$blog_cats = NULL;
			echo form_input('catName',$blog_cats['catName'], 'class="formelement" id="catName"');
		 ?>
			
		<input type="submit" value="Add Category" id="submit" class="button" />

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

