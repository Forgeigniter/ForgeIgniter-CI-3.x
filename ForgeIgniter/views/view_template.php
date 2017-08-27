<?php echo preg_replace('/(<\/html>)+|(<\/body>)+/i', '', $body); ?>
<link rel="stylesheet" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/cms.css" type="text/css" />
<script type="text/javascript">
(function(){
	var jqscript=document.createElement('script');
	jqscript.setAttribute("type","text/javascript");
	jqscript.setAttribute("src","<?php echo base_url().$this->config->item('staticPath'); ?>/js/loader.js");
	document.getElementsByTagName("head")[0].appendChild(jqscript);
})();
</script>
<?php if (in_array('images', $this->permission->permissions)): ?>
	<a href="<?php echo site_url('/admin/images/popup'); ?>" id="halogycms_editpic" rel="<?php echo site_url('/admin/images/popup'); ?>"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_edit_pic.png" alt="Edit Pic" /></a>
<?php endif; ?>
<div id="halogycms_browser"><p style="text-align:center;"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/loading.gif" alt="Loading" /></p></div>
<div id="halogycms_popup" class="loading"></div>
</body>
</html>