<?php if ($categories): ?>
<?php foreach($categories as $cat): ?>
	<li class="<?php echo ($cat['catID'] == $this->uri->segment(3)) ? 'active"' : ''; ?>"><?php echo anchor('/wiki/pages/'.$cat['catID'], $cat['catName']); ?></li>
<?php endforeach; ?>
<?php endif; ?>