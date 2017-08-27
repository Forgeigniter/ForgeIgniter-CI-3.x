<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/pages/order/nav'); ?>',$(this).sortable('serialize'),function(data){ });
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
	initOrder('ol.order, ol.order ol');
});
</script>

<h1 class="headingleft">Custom Navigation</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/pages/viewall'); ?>" class="button blue">View Pages</a>
	<a href="<?php echo site_url('/admin/pages/add_nav'); ?>" class="showform button blue">Add Navigation</a>	
</div>

<div class="clear"></div>

<div class="tip">
	<p>Custom Navigation allows you to override the default page navigation. To use the custom navigation make sure you use the <strong>{navigation(custom)}</strong> tag.</p>
</div>

<div class="hidden"></div>

<?php if ($parents): ?>

	<hr />

	<ol class="order">
	<?php foreach ($parents as $nav): ?>
		<li id="navigation-<?php echo $nav['navID']; ?>" class="<?php echo (@$children[$nav['navID']]) ? 'haschildren' : ''; ?>">
			<div class="col1">
				<span><strong><?php echo $nav['navName']; ?></strong> <small>(<?php echo $nav['uri']; ?>)</small></span>
			</div>
			<div class="col2">&nbsp;</div>
			<div class="buttons">
				<a href="<?php echo site_url('/admin/pages/edit_nav/'.$nav['navID']); ?>" class="showform"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
				<a href="<?php echo site_url('/admin/pages/delete_nav/'.$nav['navID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
			</div>
			<div class="clear"></div>
			<?php if (@$children[$nav['navID']]): ?>
				<ol class="subnav">
				<?php foreach ($children[$nav['navID']] as $child): ?>
					<li id="navigation-<?php echo $child['navID']; ?>">
						<div class="col1">
							<span class="padded">--</span>
							<span><strong><?php echo $child['navName']; ?></strong> <small>(<?php echo $child['uri']; ?>)</small></span>
						</div>
						<div class="col2">&nbsp;</div>
						<div class="buttons">
							<a href="<?php echo site_url('/admin/pages/edit_nav/'.$child['navID']); ?>" class="showform"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_edit.png" alt="Edit" /></a>
							<a href="<?php echo site_url('/admin/pages/delete_nav/'.$child['navID']); ?>" onclick="return confirm('Are you sure you want to delete this?')"><img src="<?php echo $this->config->item('staticPath'); ?>/images/btn_delete.png" alt="Delete" /></a>
						</div>
						<div class="clear"></div>
					</li>
				<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ol>

<?php endif; ?>

