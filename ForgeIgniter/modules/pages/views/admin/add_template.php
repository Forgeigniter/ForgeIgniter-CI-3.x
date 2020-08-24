<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Pages :
		<small>Add Template</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?= site_url('admin/pages'); ?>"><i class="fa fa-paint-brush"></i> Pages</a></li>
		<li class="active">Add Template</li>
	</ol>
</section>

<!-- Main content -->
<section class="content container-fluid">

	<section class="content">

		<div class="row">
			<div class="pull-left">
				<a href="<?= site_url('/admin/pages/templates');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Templates</a>
			</div>
			<div class="col-md-6 pull-right">
				<input type="submit" value="Save Changes" name="save" id="save" class="btn btn-green margin-bottom save" />
				<input type="button" value="Reset to Default" id="default" class="btn btn-blue margin-bottom" />
			</div>
		</div>


		<div class="row extra-padding">

			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-paint-brush"></i>
					<h3 class="box-title">Templates / Themes</h3>
				</div><!-- End box header -->

				<div class="box-body">

					<div class="hidden">
						<p class="hide"><a href="#">x</a></p>
						<div class="inner"></div>
					</div>

					<div class="clear"></div>

					<?php if ($errors = validation_errors()): ?>
					<div class="callout callout-danger">
						<h4>Warning!</h4>
						<?php echo $errors; ?>
					</div>
					<?php endif; ?>

					<?php if (isset($message)): ?>
					<div class="callout callout-info">
						<h4>Notice</h4>
						<?php echo $message; ?>
					</div>
					<?php endif; ?>

						<div class="showModuleName">
							<label for="templateName">Name:</label>
							<?php echo @form_input('templateName',set_value('templateName', $data['templateName']), 'id="templateName" class="formelement"'); ?>
							<br class="clear" />
						</div>

						<br />

						<label for="moduleSelect">Module:</label>
						<?php
							// we should really be grabbing avalible module values
							// not this temp mess...
							$values = array();
							$values[''] = 'Not a module template';
							$values['!'] = '---------------------------';
							if (@in_array('blog', $this->permission->permissions)) $values['!blog'] = 'Blog';
							if (@in_array('blog', $this->permission->permissions)) $values['blog'] = '-- View Posts';
							if (@in_array('blog', $this->permission->permissions)) $values['blog_single'] = '-- Single Post';
							if (@in_array('blog', $this->permission->permissions)) $values['blog_search'] = '-- Blog Search Results';
							if (@in_array('community', $this->permission->permissions)) $values['!community'] = 'Community';
							if (@in_array('community', $this->permission->permissions)) $values['community_account'] = '-- Account';
							if (@in_array('community', $this->permission->permissions)) $values['community_create_account'] = '-- Create Account';
							if (@in_array('community', $this->permission->permissions)) $values['community_forgotten'] = '-- Forgotten Password';
							if (@in_array('community', $this->permission->permissions)) $values['community_home'] = '-- Home (My Profile)';
							if (@in_array('community', $this->permission->permissions)) $values['community_login'] = '-- Login';
							if (@in_array('community', $this->permission->permissions)) $values['community_members'] = '-- Members';
							if (@in_array('community', $this->permission->permissions)) $values['community_messages'] = '-- Messages';
							if (@in_array('community', $this->permission->permissions)) $values['community_messages_form'] = '-- Messages Form';
							if (@in_array('community', $this->permission->permissions)) $values['community_messages_popup'] = '-- Messages Popup';
							if (@in_array('community', $this->permission->permissions)) $values['community_messages_read'] = '-- Messages Read';
							if (@in_array('community', $this->permission->permissions)) $values['community_reset'] = '-- Reset Password';
							if (@in_array('community', $this->permission->permissions)) $values['community_view_profile'] = '-- View Profile';
							if (@in_array('community', $this->permission->permissions)) $values['community_view_profile_private'] = '-- View Private Profile';
							if (@in_array('events', $this->permission->permissions)) $values['!events'] = 'Events';
							if (@in_array('events', $this->permission->permissions)) $values['events'] = '-- View Events';
							if (@in_array('events', $this->permission->permissions)) $values['events_single'] = '-- Single Event';
							if (@in_array('events', $this->permission->permissions)) $values['events_featured'] = '-- Featured Events';
							if (@in_array('events', $this->permission->permissions)) $values['events_search'] = '-- Events Search Results';
							if (@in_array('forums', $this->permission->permissions)) $values['!forums'] = 'Forums';
							if (@in_array('forums', $this->permission->permissions)) $values['forums'] = '-- Forums List';
							if (@in_array('forums', $this->permission->permissions)) $values['forums_delete'] = '-- Delete Forum';
							if (@in_array('forums', $this->permission->permissions)) $values['forums_forum'] = '-- View Forum';
							if (@in_array('forums', $this->permission->permissions)) $values['forums_post_reply'] = '-- Post Reply';
							if (@in_array('forums', $this->permission->permissions)) $values['forums_post_topic'] = '-- Post Topic';
							if (@in_array('forums', $this->permission->permissions)) $values['forums_search'] = '-- Forums Search Results';
							if (@in_array('forums', $this->permission->permissions)) $values['forums_topic'] = '-- View Topic';
							if (@in_array('shop', $this->permission->permissions)) $values['!shop'] = 'Shop';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_account'] = '-- Account (Shop)';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_browse'] = '-- Browse Products';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_cancel'] = '-- Cancel Purchase';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_cart'] = '-- Shopping Cart';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_checkout'] = '-- Checkout';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_create_account'] = '-- Create Account (Shop)';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_donation'] = '-- Successful Donation';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_featured'] = '-- Featured Products';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_forgotten'] = '-- Forgotten Password (Shop)';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_login'] = '-- Login (Shop)';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_orders'] = '-- Orders';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_prelogin'] = '-- Pre-login';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_product'] = '-- View Product';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_recommend'] = '-- Recommend Product';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_reset'] = '-- Reset Password (Shop)';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_review'] = '-- Review Product';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_subscriptions'] = '-- Subscriptions';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_success'] = '-- Successful Transaction';
							if (@in_array('shop', $this->permission->permissions)) $values['shop_view_order'] = '-- View Order';
							if (@in_array('wiki', $this->permission->permissions)) $values['!wiki'] = 'Wiki';
							if (@in_array('wiki', $this->permission->permissions)) $values['wiki'] = '-- Browse Pages';
							if (@in_array('wiki', $this->permission->permissions)) $values['wiki_form'] = '-- Edit Page';
							if (@in_array('wiki', $this->permission->permissions)) $values['wiki_page'] = '-- View Page';
							if (@in_array('wiki', $this->permission->permissions)) $values['wiki_search'] = '-- Wiki Search Results';

							$values['custom'] = 'Custom Module';

							echo @form_dropdown('moduleSelect',$values, (($data['templateName'] == 'custom') ? 'custom' : $data['modulePath']), 'id="moduleSelect" class="formelement" rel="'.site_url('/admin/pages/module').'"');
						?>
						<span class="tip">To make a module template (e.g. for the Blog) select the module here.</span>
						<br class="clear" />

						<div class="showModulePath">
							<label for="modulePath">Module Reference:</label>
							<?php echo @form_input('modulePath',set_value('modulePath', $data['modulePath']), 'id="modulePath" class="formelement"'); ?>
							<br class="clear" />
						</div>

						<br />

						<div class="autosave">
							<script src="<?= site_url('static/themes/assets/editors/ckeditor/ckeditor.js'); ?>"></script>
							<?php
								$data = [
									'name'        => 'body',
									'id'          => 'body',
									'class'       => 'code editor',
									'style'       => 'width:70%',
									'rows'        => '20',
									'cols'        => '30',
									'value'       => @set_value('body', $data['body'])
								];
								echo form_textarea($data);
							?>
<!--
	ckeditor setting it's own ID replacing body
	change so we can pull default module tpl's


							<script type="text/javascript" >
								<?php
								//	$ckeditor_settingsTemplate = $this->config->item('settingsTemplates', 'ckeditor_config');
								//	echo $ckeditor_settingsTemplate;
								?>
							</script>
-->

							<br class="clear" />
						</div>
						<br />

					</form>


				</div> <!-- End box body -->
			</div> <!-- End Box -->

		</div> <!-- End row -->
	</section>


	<script type="text/javascript" src="<?=PATH['static'];?>/js/templates.js" /></script>
