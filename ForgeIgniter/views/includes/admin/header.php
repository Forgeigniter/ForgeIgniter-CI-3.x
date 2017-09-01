<?php
	// Set Easy Vars

	///Paths
	$siteURL = base_url($this->uri->uri_string());
	$staticPath = base_url($this->config->item('staticPath'));
	$themePath = base_url($this->config->item('themePath'));
	$loginPath = base_url('admin/login');

	///
	$websiteName = $this->site->config['siteName'];

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= (isset($websiteName)) ? $websiteName : 'Login to'; ?> Admin Login - ForgeIgniter</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?=$themePath?>anvil/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=$themePath?>anvil/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?=$themePath?>anvil/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=$themePath?>anvil/css/LTE.css">
  <!-- Skins 
		Anvil (Default)
		Neo ( Dark With Gradient Colours)
		Night ( Dark )
		Dream ( Light With Gradient Colours)
		White Rose ( White Theme)

		Make selectable in backend for 2.1 - 2.2
  -->
  <link rel="stylesheet" href="<?=$themePath?>anvil/css/skins/skin-anvil-light.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
		
  <link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Exo+2:300,400,500">
</head>
<?php
// Check if we're at admin login page
if (strpos($siteURL,'admin/login') !== FALSE) {
	// Login Body
	echo "<body class='hold-transition skin-anvil-light login-page'>";
}
else {
	// Default Body
	echo "<body class='hold-transition skin-anvil-light sidebar-mini sidebar-collapse'>";
}
?>

<?php if ($this->session->userdata('session_admin') && strpos($siteURL,'admin/login') == FALSE): ?>

<!-- Old - Replace -->
<div class="bg">

	<div class="container">

		<div id="header">

			<div id="logo">

				<?php
					// set logo
					if ($this->config->item('logoPath')) $logo = $this->config->item('logoPath');
					elseif ($image = $this->uploads->load_image('admin-logo')) $logo = $image['src'];
					else $logo = base_url() . $this->config->item('staticPath').'/images/ForgeIgniter-Logo.jpg';
				?>

				<h1><a href="<?php echo site_url('/admin'); ?>"><?php echo (isset($this->site->config['siteName'])) ? $this->site->config['siteName'] : 'Login to'; ?> Admin</a></h1>
				<a href="<?php echo site_url('/admin'); ?>"><img src="<?php echo $logo; ?>" alt="Logo" /></a>

			</div>

			<div id="siteinfo">
				<ul id="toolbar">
					<li><a href="<?php echo site_url('/'); ?>">View Site</a></li>
					<?php if ($this->session->userdata('session_admin')): ?>
						<li><a href="<?php echo site_url('/admin/dashboard'); ?>">Dashboard</a></li>
						<li><a href="<?php echo site_url('/admin/users/edit/'.$this->session->userdata('userID')); ?>">My Account</a></li>
						<?php if ($this->session->userdata('groupID') == $this->site->config['groupID'] || $this->session->userdata('groupID') < 0): ?>
							<li><a href="<?php echo site_url('/admin/site/'); ?>">My Site</a></li>
							<li><a href="<?php echo base_url('/static/docs'); ?>" target="_blank">Docs</a></li>
						<?php endif; ?>
						<?php if ($this->session->userdata('groupID') < 0 && @file_exists(APPPATH.'modules/forge/controllers/forge.php')): ?>
							<li class="noborder"><a href="<?php echo site_url('/admin/logout'); ?>">Logout</a></li>
							<li class="superuser"><a href="<?php echo site_url('/forge/sites'); ?>">Sites</a></li>
						<?php else: ?>
							<li class="last"><a href="<?php echo site_url('/admin/logout'); ?>">Logout</a></li>
						<?php endif; ?>
					<?php else: ?>
						<li class="last"><a href="<?php echo site_url('/admin'); ?>">Login</a></li>
					<?php endif; ?>
				</ul>

				<?php if ($this->session->userdata('session_admin')): ?>
					<h2 class="clear"><strong><?php echo $this->site->config['siteName']; ?> Admin</strong></h2>
					<h3>Logged in as: <strong><?php echo $this->session->userdata('username'); ?></strong></h3>
				<?php endif; ?>
			</div>

		</div>

		<div id="navigation">
			<ul id="menubar">
			<?php if($this->session->userdata('session_admin')): ?>
				<?php if (in_array('pages', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/pages'); ?>">Pages</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/pages/viewall'); ?>">All Pages</a></li>
							<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/pages/add'); ?>">Add Page</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('pages_templates', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/pages/templates'); ?>">Templates</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/pages/templates'); ?>">All Templates</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes'); ?>">Includes</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes/css'); ?>">CSS</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes/js'); ?>">Javascript</a></li>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('images', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/images/viewall'); ?>">Uploads</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/images/viewall'); ?>">Images</a></li>
							<?php if (in_array('images_all', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/images/folders'); ?>">Image Folders</a></li>
							<?php endif; ?>
							<?php if (in_array('files', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/files/viewall'); ?>">Files</a></li>
								<?php if (in_array('files_all', $this->permission->permissions)): ?>
									<li><a href="<?php echo site_url('/admin/files/folders'); ?>">File Folders</a></li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('webforms', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/webforms/tickets'); ?>">Web Forms</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/webforms/tickets'); ?>">Tickets</a></li>
							<?php if (in_array('webforms_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/webforms/viewall'); ?>">All Web Forms</a></li>
								<li><a href="<?php echo site_url('/admin/webforms/add_form'); ?>">Add Web Form</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('blog', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/blog/viewall'); ?>">Blog</a>
						<ul class="subnav">
							<?php if (in_array('blog', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/blog/viewall'); ?>">All Posts</a></li>
							<?php endif; ?>
							<?php if (in_array('blog_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/blog/add_post'); ?>">Add Post</a></li>
							<?php endif; ?>
							<?php if (in_array('blog_cats', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/blog/categories'); ?>">Categories</a></li>
							<?php endif; ?>
							<li><a href="<?php echo site_url('/admin/blog/comments'); ?>">Comments</a></li>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('shop', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/shop/products'); ?>">Shop</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/shop/products'); ?>">All Products</a></li>
							<?php if (in_array('shop_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/add_product'); ?>">Add Product</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_cats', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/categories'); ?>">Categories</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_orders', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/orders'); ?>">View Orders</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_shipping', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/bands'); ?>">Shipping Bands</a></li>
								<li><a href="<?php echo site_url('/admin/shop/postages'); ?>">Shipping Costs</a></li>
								<li><a href="<?php echo site_url('/admin/shop/modifiers'); ?>">Shipping Modifiers</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_discounts', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/discounts'); ?>">Discount Codes</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_reviews', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/reviews'); ?>">Reviews</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_upsells', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/upsells'); ?>">Upsells</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif ?>
				<?php if (in_array('events', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/events/viewall'); ?>">Events</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/events/viewall'); ?>">All Events</a></li>
						<?php if (in_array('events_edit', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/events/add_event'); ?>">Add Event</a></li>
						<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('forums', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/forums/forums'); ?>">Forums</a>
						<ul class="subnav">
							<?php if (in_array('forums', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/forums/forums'); ?>">Forums</a></li>
							<?php endif; ?>
							<?php if (in_array('forums_cats', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/forums/categories'); ?>">Forum Categories</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('wiki', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/wiki'); ?>">Wiki</a>
						<ul class="subnav">
							<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/wiki/viewall'); ?>">All Wiki Pages</a></li>
							<?php endif; ?>
							<?php if (in_array('wiki_cats', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/wiki/categories'); ?>">Wiki Categories</a></li>
							<?php endif; ?>
							<li><a href="<?php echo site_url('/admin/wiki/changes'); ?>">Recent Changes</a></li>
						</ul>
					</li>
				<?php endif; ?>
				<?php if (in_array('users', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/users/viewall'); ?>">Users</a>
					<?php if (in_array('users_groups', $this->permission->permissions)): ?>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/users/viewall'); ?>">All Users</a></li>
							<li><a href="<?php echo site_url('/admin/users/groups'); ?>">User Groups</a></li>
						</ul>
					<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php else: ?>
					<li><a href="<?php echo site_url('/admin'); ?>">Login</a></li>
				<?php endif; ?>
			</ul>

		</div>

		<div id="content" class="content">
 <!-- END Old - Replace -->
<?php endif; ?>


<?php if (($this->session->userdata('session_admin') && (strpos($siteURL,'admin/login') !== FALSE)) == TRUE): ?>

	<h1>Logout</h1>

	<p><a href="<?= site_url('admin/logout');?>">Click here to logout.</a></p>

<?php endif; ?>


