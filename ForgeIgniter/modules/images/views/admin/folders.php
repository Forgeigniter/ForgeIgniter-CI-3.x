<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/images/order/folder'); ?>',$(this).sortable('serialize'),function(data){ });
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
	});

	$('a.edit').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('.col1').children('input').show();
		$(this).parent().siblings('.col1').children('span').hide();
	});

	initOrder('ol.order');
});
</script>

<h1 class="headingleft">Image Folders</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/images/viewall'); ?>" class="button blue">View Images</a>
	<a href="#" class="toggle button blue">Add Folder</a>
</div>

<div class="clear"></div>

<div class="hidden">
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">
	
		<label for="folderName">Folder Name:</label>
		
		<?php echo @form_input('folderName',$images_folders['folderName'], 'class="formelement" id="folderName"'); ?>
			
		<input type="submit" value="Add Folder" id="submit" class="button" />

		<br class="clear" />
		
	</form>
</div>

<?php if ($folders): ?>

<form method="post" action="<?php echo site_url('/admin/images/edit_folder'); ?>">

	<ol class="order">
	<?php foreach ($folders as $folder): ?>
		<li id="image_folders-<?php echo $folder['folderID']; ?>">
			<div class="col1">
				<span><strong><?php echo $folder['folderName']; ?></strong> <small>(<?php echo url_title(strtolower($folder['folderName'])); ?>)</small></span>
				<?php echo @form_input($folder['folderID'].'[folderName]', $folder['folderName'], 'class="formelement hide" title="folder Name"'); ?><input type="submit" class="button hide" value="Edit" />
			</div>
			<div class="col2">
				&nbsp;
			</div>
			<div class="buttons">
				<a href="#" class="edit"><img src="<?php echo base_url().$this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
				<a href="<?php echo site_url('/admin/images/delete_folder/'.$folder['folderID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo base_url().$this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
			</div>
			<div class="clear"></div>
		</li>
	<?php endforeach; ?>
	</ol>

</form>

<?php else: ?>

<p>No folders have been created yet.</p>

<?php endif; ?>

