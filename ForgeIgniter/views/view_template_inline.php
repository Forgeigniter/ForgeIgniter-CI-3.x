<?php echo preg_replace('/(<\/html>)+|(<\/body>)+/i', '', $body); ?>
<?php if (!$this->core->is_ajax()): ?>
<link rel="stylesheet" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/cms.css" type="text/css" />
<script type="text/javascript">
(function(){
	var jqscript=document.createElement('script');
	jqscript.setAttribute("type","text/javascript");
	jqscript.setAttribute("src","<?php echo base_url() . $this->config->item('staticPath'); ?>/js/loader.js");
	document.getElementsByTagName("head")[0].appendChild(jqscript);
})();
</script>
<?php if (in_array('images', $this->permission->permissions)): ?>
	<a href="<?php echo site_url('/admin/images/popup'); ?>" id="ficms_editpic" rel="<?php echo site_url('/admin/images/popup'); ?>"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/images/icons/cms/edit_pic.png" alt="Edit Pic" /></a>
<?php endif; ?>
	<div id="ficms_admin">
		<div id="ficms_controls">
			<span class="text">
				Logged in<?php if ($username = $this->session->userdata('username')): ?> as <strong><?php echo $username; ?></strong><?php endif; ?>
			</span>
			<a href="#" class="ficms_button ficms_toggle" id="ficms_toggle">Preview</a>
			<a href="<?php echo site_url('/admin'); ?>" class="ficms_button ficms_saveall">Admin</a>
			<a href="<?php echo site_url('/admin/logout/'.$this->core->encode($this->uri->uri_string())); ?>" class="ficms_button">Logout</a>
			<?php echo (isset($postID) && @in_array('blog_edit', $this->permission->permissions)) ? anchor('/admin/blog/edit_post/'.$postID, 'Edit Post', 'class="ficms_button green"') : ''; ?>
			<?php echo (isset($productID) && @in_array('shop_edit', $this->permission->permissions)) ? anchor('/admin/shop/edit_product/'.$productID, 'Edit Product', 'class="ficms_button green"') : ''; ?>
			<?php echo (isset($versionID) && @in_array('pages_edit', $this->permission->permissions)) ? anchor('/admin/pages/edit/'.$pageID, 'Edit Page', 'class="ficms_button ficms_saveall green"') : ''; ?>
			<?php echo (isset($versionID) && @in_array('pages_edit', $this->permission->permissions)) ? anchor('/admin/pages/publish/'.$pageID, 'Publish Page', 'class="ficms_button ficms_saveall orange"') : ''; ?>
		</div>
	</div>
	<div id="ficms_browser" class="loading"></div>
	<div id="ficms_popup" class="loading"></div>
<?php endif; ?>
</body>
</html>
