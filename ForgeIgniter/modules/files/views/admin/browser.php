<a class="halogycms_close" href="#"><img title="Close" src="<?php echo base_url().$this->config->item('staticPath'); ?>/images/btn_close.png"/></a>
<a href="<?php echo site_url('/admin/files'); ?>" class="halogycms_button halogycms_confirm" target="_top">Manage Files</a>
<div style="clear:both;"></div>

<div id="halogycms_scroll">

<?php if (@$folders): ?>

	<?php foreach ($folders as $folder): ?>		
		<?php if ($folder['files']): ?>
			<ul>
				<li class="halogycms_title"><a href="#" class="halogycms_togglefolder"><strong><?php echo $folder['folderName']; ?></strong></a></li>
			</ul>
			
			<ul>
				<?php foreach ($folder['files'] as $file): ?>
					<?php $extension = substr($file['filename'], strpos($file['filename'], '.')+1); ?>
					<li>
						<div class="halogycms_thumb">
							<a href="#" class="halogycms_insertfile" title="<?php echo $file['fileRef']; ?>"><img src="<?php echo base_url().$this->config->item('staticPath'); ?>/fileicons/<?php echo $extension; ?>.png" alt="<?php echo $file['fileRef']; ?>" /></a>
						</div>
						<div class="halogycms_description">
							<a href="#" class="halogycms_insertfile" title="<?php echo $file['fileRef']; ?>"><?php echo $file['fileRef']; ?></a>
						</div>	
					</li>				
				<?php endforeach; ?>
					
			</ul>
		<?php endif; ?>
	<?php endforeach; ?>

	<ul>
		<li class="halogycms_title"><a href="#" class="halogycms_togglefolder"><strong>Other Files</strong></a></li>
	</ul>

<?php endif; ?>

<?php if ($files): ?>

	<ul>
		<?php foreach ($files as $file): ?>
			<?php $extension = substr($file['filename'], strpos($file['filename'], '.')+1); ?>
			<li>
				<div class="halogycms_thumb">
					<a href="#" class="halogycms_insertfile" title="<?php echo $file['fileRef']; ?>"><img src="<?php echo base_url().$this->config->item('staticPath'); ?>/fileicons/<?php echo $extension; ?>.png" alt="<?php echo $file['fileRef']; ?>" /></a>
				</div>
				<div class="halogycms_description">
					<a href="#" class="halogycms_insertfile" title="<?php echo $file['fileRef']; ?>"><?php echo $file['fileRef']; ?></a>
				</div>	
			</li>
		<?php endforeach; ?>
			
	</ul>
	
<?php else: ?>

	<p class="clear">You haven't uploaded any files yet.</p>

<?php endif; ?>

</div>