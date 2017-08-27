<script type="text/javascript">
$(function(){
	$('div.hidden').hide();
	$('a.showform').click(function(event){ 
		event.preventDefault();
		$('div.hidden div.inner').load('/templates/add/');		
		$('div.hidden').fadeIn();
	});
	$('p.hide a').click(function(event){ 
		event.preventDefault();		
		$(this).parent().parent().fadeOut();
	});
	$('.toggle-zip').click(function(event){ 
		event.preventDefault();		
		$('div#upload-zip').toggle('400');
		$('div#upload-image:visible, div#loader:visible').toggle('400');
	});
	$('select#filter').change(function(){
		var status = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/pages/templates'); ?>/'+status;
	});
});
</script>

<h1 class="headingleft">Page Templates</h1>

<div class="headingright">
	<label for="filter">
		Filter
	</label> 

	<?php
		$options = array(
			'' => 'View All',
			'page' => 'Page Templates',
			'module' => 'Module Templates'
		);
		
		echo form_dropdown('filter', $options, $type, 'id="filter"');
	?>
	<a href="<?php echo site_url('/admin/pages/includes'); ?>" class="button blue">Includes</a>
	<a href="#" class="button blue toggle-zip">Import Theme</a>
	<a href="<?php echo site_url('/admin/pages/add_template'); ?>" class="button">Add Template</a>
</div>

<div class="hidden">
	<p class="hide"><a href="#">x</a></p>
	<div class="inner"></div>
</div>

<div class="clear"></div>

<?php if ($errors = validation_errors()): ?>
	<div class="error clear">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<div id="upload-zip" class="hidden clear">
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">
	
		<label for="image">ZIP File:</label>
		<div class="uploadfile">
			<?php echo @form_upload('zip', '', 'size="16" id="image"'); ?>
		</div>
		<br class="clear" /><br />	

		<input type="submit" value="Import Theme" name="upload_zip" class="button nolabel" id="submit" />
		<a href="<?php echo site_url('/admin/images'); ?>" class="button cancel grey">Cancel</a>
			
	</form>
</div>

<?php if ($templates): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th>Templates</th>
		<th>Date Modified</th>		
		<th>Usage</th>	
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>		
	</tr>
<?php
	$i = 0;
	foreach ($templates as $template): 
	$class = ($i % 2) ? ' class="alt"' : ''; $i++;
?>
	<tr<?php echo $class;?>>
		<td><?php echo anchor('/admin/pages/edit_template/'.$template['templateID'], ($template['modulePath'] != '') ? '<small>Module</small>: '.$template['modulePath'].' <em>('.ucfirst(preg_replace('/^(.+)_/i', '', $template['modulePath'])).')</em>' : $template['templateName']); ?></td>
		<td><?php echo dateFmt($template['dateCreated']); ?></td>		
		<td><?php if ($this->pages->get_template_count($template['templateID']) > 0): ?>
				<?php echo $this->pages->get_template_count($template['templateID']); ?> <small>page(s)</small>
			<?php endif; ?></td>
		<td>
			<?php echo anchor('/admin/pages/edit_template/'.$template['templateID'], 'Edit'); ?>
		</td>
		<td>
			<?php echo anchor('/admin/pages/delete_template/'.$template['templateID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>There are no templates here yet.</p>


<?php endif; ?>

