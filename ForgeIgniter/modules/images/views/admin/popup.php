<?php if ($errors = validation_errors()): ?>
	<div class="error clear">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<?php if ($this->core->is_ajax()): ?>

	<a class="halogycms_close" href="#"><img title="Close" src="<?php echo base_url().$this->config->item('staticPath'); ?>/images/btn_close.png"/></a>
	<a href="<?php echo site_url('/admin/images'); ?>" class="halogycms_button halogycms_confirm" target="_top">Manage Images</a>

<?php endif; ?>		

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="halogycms_form">

<?php
	$image = $this->uploads->load_image($data['imageRef']);
	$thumb = $this->uploads->load_image($data['imageRef'], true);
	$imagePath = $image['src'];
	$imageThumbPath = $thumb['src'];
?>	
<?php echo ($thumb = display_image(base_url(), $imageThumbPath, $data['imageName'], 100, 'class="pic" ')) ? $thumb : display_image(base_url(), $imagePath, $data['imageName'], 100, 'class="pic"'); ?>

	<label for="image">Image:</label>
	<div class="uploadfile">
		<?php echo @form_upload('image', '', 'size="16" id="image"'); ?>
	</div>
	<br class="clear" />

	<label for="folderID">Folder:</label>
	<?php
		$options[0] = 'No Folder';
		if ($folders):
			foreach ($folders as $folderID):
				$options[$folderID['folderID']] = $folderID['folderName'];
			endforeach;
		endif;
			
		echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="folderID" class="formelement"');
	?>	
	<br class="clear" />
	
	<label for="imageName">Description:</label>
	<?php echo @form_input('imageName', $data['imageName'], 'class="formelement" id="imageName"'); ?>
	<br class="clear" />

	<label for="imageRef">Reference:</label>
	<?php echo @form_input('imageRef', $data['imageRef'], 'class="formelement" id="imageRef"'); ?>
	<br class="clear" />

	<label for="class">Display:</label>
	<?php
		$values = array(
			'default' => 'Default',	
			'left' => 'Left Align',
			'center' => 'Center Align',			
			'right' => 'Right Align',
			'bordered' => 'Border',
			'bordered left' => 'Border - Left Align',
			'bordered center' => 'Border - Center Align',			
			'bordered right' => 'Border - Right Align',
			'full' => 'Full Width',			
			'' => 'No Style'
		);					
		echo @form_dropdown('class',$values,$data['class'], 'class="formelement"'); 
	?>
	<br class="clear" />

	<label for="maxsize">Max Size (px):</label>
	<?php echo @form_input('maxsize', set_value('maxsize', (($data['maxsize']) ? $data['maxsize'] : '')), 'class="formelement" id="maxsize"'); ?>
	<br class="clear" /><br />	

	<input type="submit" value="Save Changes" class="button nolabel" id="submit" />
	<br class="clear" />
	
</form>

<br class="clear" />