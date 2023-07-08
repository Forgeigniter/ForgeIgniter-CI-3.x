<a class="ficms_close" href="#"><img title="Close" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/icons/cms/btn_close.png"/></a>
<a href="<?php echo site_url('/admin/images'); ?>" class="ficms_button ficms_confirm" target="_top">Manage Images</a>
<div style="clear:both;"></div>

<div id="ficms_scroll">

<?php if (@$folders): ?>

	<?php foreach ($folders as $folder): ?>
		<?php if ($folder['images']): ?>
			<ul>
				<li class="ficms_title"><a href="#" class="ficms_togglefolder"><strong><?php echo $folder['folderName']; ?></strong></a></li>
			</ul>

			<ul>
				<?php foreach ($folder['images'] as $image):
					$imageData = $this->uploads->load_image($image['imageRef']);
					$imagePath = $imageData['src'];
					$imageData = $this->uploads->load_image($image['imageRef'], true);
					$imageThumbPath = $imageData['src'];
				?>
					<li class="fixed">
						<div class="ficms_thumb">
							<a href="#" class="ficms_insertimage" title="<?php echo $image['imageRef']; ?>">
								<img src="<?php echo base_url().$imageThumbPath; ?>" width="80" />
							</a>
						</div>
						<div class="ficms_description">
							<a href="#" class="ficms_insertimage" title="<?php echo $image['imageRef']; ?>"><?php echo $image['imageRef']; ?></a>
						</div>
					</li>
				<?php endforeach; ?>

			</ul>
		<?php endif; ?>
	<?php endforeach; ?>

	<ul>
		<li class="ficms_title"><a href="#" class="ficms_togglefolder"><strong>Other Images</strong></a></li>
	</ul>

<?php endif; ?>

<?php if ($images): ?>

	<ul>
		<?php foreach ($images as $image):
			$imageData = $this->uploads->load_image($image['imageRef']);
			$imagePath = $imageData['src'];
			$imageData = $this->uploads->load_image($image['imageRef'], true);
			$imageThumbPath = $imageData['src'];
		?>
			<li class="fixed">
				<div class="ficms_thumb">
					<a href="#" class="ficms_insertimage" title="<?php echo $image['imageRef']; ?>">
						<img src="<?php echo base_url().$imageThumbPath; ?>" width="80" />
					</a>
				</div>
				<div class="ficms_description">
					<a href="#" class="ficms_insertimage" title="<?php echo $image['imageRef']; ?>"><?php echo $image['imageRef']; ?></a>
				</div>
			</li>
		<?php endforeach; ?>

	</ul>

<?php else: ?>

	<p class="clear">You haven't uploaded any images yet.</p>

<?php endif; ?>

</div>
