<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; }
.ac_loading { background: white url('<?php echo base_url() . $this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
$(function(){
	$('.toggle-image').click(function(event){
		event.preventDefault();
		$('div#upload-image').slideToggle('400');
		$('div#upload-zip:visible, div#loader:visible').slideToggle('400');
	});

	$('.toggle-zip').click(function(event){ 
		event.preventDefault();
		$('div#upload-zip').slideToggle('400');
		$('div#upload-image:visible, div#loader:visible').slideToggle('400');
	});

	$('.edit').click(function(event){
		event.preventDefault();
		$.scrollTo(0, '200');
		$('div#loader').load(this.href, function(){
			$('div#loader:hidden').toggle('400');
			$('div#upload-zip:visible, div#upload-image:visible').slideToggle('400');
		});
	});

    $('#searchbox').fieldreplace();
	function formatItem(row) {
		if (row[0].length) return row[1]+'<br /><span class="email">(#'+row[0]+')</span>';
		else return 'No results';
	}
	$('#searchbox').autocomplete("<?php echo site_url('/admin/images/ac_images'); ?>", { delay: "0", selectFirst: false, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});

	$('select#folderID').change(function(){
		var folderID = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/images/viewall'); ?>/'+folderID;
	});

	$('a.lightbox').lightBox({imageLoading:'<?php echo base_url() . $this->config->item('staticPath'); ?>/images/loading.gif',imageBtnClose: '<?php echo base_url().$this->config->item('staticPath'); ?>/images/lightbox_close.gif',imageBtnNext:'<?php echo base_url().$this->config->item('staticPath'); ?>/image/lightbox_btn_next.gif',imageBtnPrev:'<?php echo base_url().$this->config->item('staticPath'); ?>/image/lightbox_btn_prev.gif'});

});
</script>

<h1 class="headingleft">Images</h1>

<div class="headingright">

	<form method="post" action="<?php echo site_url('/admin/images/viewall'); ?>" class="default" id="search">
		<input type="text" name="searchbox" id="searchbox" class="formelement inactive" title="Search Images..." />
		<input type="image" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_search.gif" id="searchbutton" />
	</form>

	<label for="folderID">
		Folder
	</label>

	<?php
		$options = '';
		$options['me'] = 'My Images';
		if (@in_array('images_all', $this->permission->permissions)):
			$options['all'] = 'View All Images';

			if ($folders):
				foreach ($folders as $folder):
					$options[$folder['folderID']] = $folder['folderName'];
				endforeach;
			endif;
		endif;
		echo form_dropdown('folderID', $options, $folderID, 'id="folderID"');
	?>

	<?php if ($this->site->config['plan'] = 0 || $this->site->config['plan'] = 6 || (($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6) && $quota < $this->site->plans['storage'])): ?>

		<a href="#" class="button blue toggle-zip">Upload Zip</a>
		<a href="#" class="button toggle-image blue">Upload Image</a>

	<?php endif; ?>

</div>

<?php if ($errors = validation_errors()): ?>
	<div class="error clear">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<?php if ($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6): ?>

	<?php if ($quota > $this->site->plans['storage']): ?>

	<div class="error clear">
		<p>You have gone over your storage capacity, we will be contacting you soon.</p>
	</div>

	<div class="quota">
		<div class="over"><?php echo floor($quota / $this->site->plans['storage'] * 100); ?>%</div>
	</div>

	<?php else: ?>

	<div class="quota">
		<div class="used" style="width: <?php echo ($quota > 0) ? (floor($quota / $this->site->plans['storage'] * 100)) : 0; ?>%"><?php echo floor($quota / $this->site->plans['storage'] * 100); ?>%</div>
	</div>

	<?php endif; ?>

	<p><small>You have used <strong><?php echo number_format($quota); ?>kb</strong> out of your <strong><?php echo number_format($this->site->plans['storage']); ?> KB</strong> quota.</small></p>

<?php endif; ?>

<div id="upload-image" class="hidden clear">
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

		<label for="image">Image:</label>
		<div class="uploadfile">
			<?php echo @form_upload('image', '', 'size="16" id="image"'); ?>
		</div>
		<br class="clear" />

		<label for="imageName">Description (alt tag):</label>
		<?php echo @form_input('imageName', $images['imageName'], 'class="formelement" id="imageName"'); ?>
		<br class="clear" />

		<label for="imageFolderID">Folder: <small>[<a href="<?php echo site_url('/admin/images/folders'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
		<?php
			$options = '';
			$options[0] = 'No Folder';
			if ($folders):
				foreach ($folders as $folderID):
					$options[$folderID['folderID']] = $folderID['folderName'];
				endforeach;
			endif;

			echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="imageFolderID" class="formelement"');
		?>
		<br class="clear" /><br />

		<input type="submit" value="Save Changes" class="button nolabel" id="submit" />
		<a href="<?php echo site_url('/admin/images'); ?>" class="button cancel grey">Cancel</a>

	</form>
</div>

<div id="upload-zip" class="hidden clear">
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

		<label for="image">ZIP File:</label>
		<div class="uploadfile">
			<?php echo @form_upload('zip', '', 'size="16" id="image"'); ?>
		</div>
		<br class="clear" />

		<label for="zipFolderID">Folder: <small>[<a href="<?php echo site_url('/admin/images/folders'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
		<?php
			$options[0] = 'No Folder';
			if ($folders):
				foreach ($folders as $folderID):
					$options[$folderID['folderID']] = $folderID['folderName'];
				endforeach;
			endif;

			echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="zipFolderID" class="formelement"');
		?>
		<br class="clear" /><br />

		<input type="submit" value="Upload Zip" name="upload_zip" class="button nolabel" />
		<a href="<?php echo site_url('/admin/images'); ?>" class="button cancel grey">Cancel</a>

	</form>
</div>

<div id="loader" class="hidden clear"></div>

<?php if ($images): ?>

	<?php echo $this->pagination->create_links(); ?>

	<table class="images clear">
		<tr>
		<?php
			$numItems = sizeof($images);
			$itemsPerRow = 5;
			$i = 0;

			foreach ($images as $image)
			{
				if (($i % $itemsPerRow) == 0 && $i > 1)
				{
					echo '</tr><tr>'."\n";
					$i = 0;
				}
				echo '<td valign="top" align="center" width="'.floor(( 1 / $itemsPerRow) * 100).'%">';

				$imageData = $this->uploads->load_image($image['imageRef']);
				$imagePath = $imageData['src'];
				$imageData = $this->uploads->load_image($image['imageRef'], true);
				$imageThumbPath = $imageData['src'];
		?>
				<div class="buttons">
					<?php echo anchor('/admin/images/edit/'.$image['imageID'].'/'.$this->core->encode($this->uri->uri_string()), '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_edit.png" alt="Edit" />', 'class="edit"'); ?>
					<?php echo anchor('/admin/images/delete/'.$image['imageID'].'/'.$this->core->encode($this->uri->uri_string()), '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_delete.png" alt="Delete" />', 'onclick="return confirm(\'Are you sure you want to delete this image?\')"'); ?>
				</div>

				<a href="<?php echo base_url().$imagePath; ?>" title="<?php echo $image['imageName']; ?>" class="lightbox">
					<img src="<?php echo base_url().$imageThumbPath; ?>" class="pic" />
				</a>

				<p><strong><?php echo $image['imageRef']; ?></strong></p>

		<?php
				echo '</td>'."\n";
				$i++;
			}

			for($x = 0; $x < ($itemsPerRow - $i); $x++)
			{
				echo '<td width="'.floor((1 / $itemsPerRow) * 100).'%">&nbsp;</td>';
			}
		?>
		</tr>
	</table>

	<?php echo $this->pagination->create_links(); ?>

	<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">You have not yet uploaded any images.</p>

<?php endif; ?>
