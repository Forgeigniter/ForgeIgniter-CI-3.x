<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/forums/order/cat'); ?>',$(this).sortable('serialize'),function(data){ });
};

function initOrder(el){
	$(el).sortable({ 
		axis: 'y',
	    revert: false, 
	    delay: '60',
	    opacity: '0.5',
	    update: setOrder
	});
};

$(function(){
	$('a.toggle').click(function(event){ 
		event.preventDefault();		
		$('div.hidden').slideToggle('400');
	});

	$('a.edit').click(function(event){
		event.preventDefault();
		$(this).siblings('input').show();
		$(this).siblings('span').hide();
	});

	$('input#submit').click(function(){
		var requiredField = 'input#catName';
		if (!$(requiredField).val()) {
			$(requiredField).addClass('error').prev('label').addClass('error');
			$(requiredField).focus(function(){
				$(requiredField).removeClass('error').prev('label').removeClass('error');
			});
			return false;
		}
	});

	initOrder('ol.order');
});
</script>

<h1 class="headingleft">Forum Categories</h1>

<div class="headingright">	
	<a href="<?php echo site_url('/admin/forums/forums'); ?>" class="button blue">View Forums</a>
	<a href="#" class="toggle button blue">Add Category</a>
</div>

<p class="clear">Order your categories below by dragging the items in the order you want them.</p>

<div class="hidden">
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">
	
		<label for="categoryName">Category name:</label>
		
		<?php echo @form_input('catName',$forums_cats['catName'], 'class="formelement" id="catName"'); ?>
			
		<input type="submit" value="Add Category" id="submit" class="button" />

		<br class="clear" />
		
	</form>
</div>

<?php if ($categories): ?>

<form method="post" action="<?php echo site_url('/admin/forums/edit_cat'); ?>">

	<ol class="order">
	<?php foreach ($categories as $category): ?>
		<li id="forums_cats-<?php echo $category['catID']; ?>">
			<a href="<?php echo site_url('/admin/forums/delete_cat/'.$category['catID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
			<a href="#" class="edit"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
			<span><strong><?php echo $category['catName']; ?></strong></span>
			<?php echo @form_input($category['catID'].'[catName]', $category['catName'], 'class="formelement hide" title="Category Name"'); ?><input type="submit" class="button hide" value="Edit" />
		</li>
	<?php endforeach; ?>
	</ol>

</form>

<?php endif; ?>

