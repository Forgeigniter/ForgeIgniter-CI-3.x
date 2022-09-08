<?php
    // Set Easy Vars

    ///Paths
    $siteURL = base_url($this->uri->uri_string());
    $staticPath = base_url($this->config->item('staticPath'));
    $themePath = base_url($this->config->item('themePath'));
    $loginPath = base_url('admin/login');

    ///
    $websiteName = $this->site->config['siteName'];


    /*
      Almost forgot how bad this was.

      - Huge cleanup needed
      - Things like the modules should be auto loaded into the menu
      - The main content area's are still from v1 but made to look pretty

    */

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= (isset($websiteName)) ? $websiteName : 'Login to'; ?> Admin Login - ForgeIgniter</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap -->
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/components/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/components/font-awesome/css/font-awesome.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/css/LTE.css">
  <!-- Skins
		Anvil (Default)
		Neo ( Dark With Gradient Colours)
		Night ( Dark )
		Dream ( Light With Gradient Colours)
		White Rose ( White Theme)

		Make selectable in backend for 2.1 - 2.2
  -->
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/css/skins/skin-anvil.css">

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


	<!-- REQUIRED JS SCRIPTS -->

	<!-- jQuery 3 -->
	<script src="<?=PATH['theme'];?>anvil/components/jquery/jquery.min.js"></script>
	<script src="https://code.jquery.com/jquery-migrate-3.0.0.js"></script>

	<!-- jQuery UI -->
	<script src="<?=PATH['theme'];?>anvil/plugins/jQueryUI/jquery-ui.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?=PATH['theme'];?>anvil/components/bootstrap/js/bootstrap.min.js"></script>

	<!-- App -->
	<script src="<?=PATH['theme'];?>anvil/js/app.js"></script>

		<script language="javascript" type="text/javascript" src="<?=PATH['static'];?>/js/default.js"></script>
		<script language="javascript" type="text/javascript" src="<?=PATH['static'];?>/js/admin.js"></script>

		<script language="JavaScript">
			$(function(){
				$('ul#menubar li').hover(
					function() { $('ul', this).css('display', 'block').parent().addClass('hover'); },
					function() { $('ul', this).css('display', 'none').parent().removeClass('hover'); }
				);
			});
		</script>


<link rel="stylesheet" type="text/css" href="<?= base_url($this->config->item('staticPath'));?>/css/admin.css" media="all" />


</head>
<?php
// Check if we're at admin login page
if (strpos(FULL_URL, 'admin/login') !== false) {
    // Login Body
    echo "<body class='hold-transition skin-anvil login-page'>";
} else {
    // Default Body
    echo "<body class='hold-transition skin-anvil sidebar-mini sidebar-collapse'>";
}
?>

<?php if ($this->session->userdata('session_admin') && strpos(FULL_URL, 'admin/login') == false): ?>

<div class="wrapper">



  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="<?=base_url('/admin');?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="<?=PATH['theme'];?>anvil/img/logo/fi-logo.jpg" height="32px"></span>
      <!-- logo for regular state and mobile devices
      <span class="logo-lg"><b>F</b>orge<b>I</b>gniter</span>
			-->
	  	<span class="logo-lg"><img src="<?=PATH['theme'];?>anvil/img/logo/forgeigniter-logo.jpg" height="36px"></span>
    </a>


    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <li>
            <a href="<?=site_url();?>"><i class="fa fa-desktop"></i><span class="menu-icon-text">View Site</span></a>
          </li>
        <?php if ($this->session->userdata('groupID') == $this->site->config['groupID'] || $this->session->userdata('groupID') < 0): ?>
					<li>
            <a href="<?= site_url('/admin/site/'); ?>"><i class="fa fa-globe"></i><span class="menu-icon-text"> My Site</span></a>
          </li>
          <li>
            <a href="<?= site_url('/forge/sites/'); ?>"><i class="fa fa-sitemap"></i><span class="menu-icon-text"> Sites</span></a>
          </li>
					<li>
            <a href="http://www.forgeigniter.com/support" target="_blank"><i class="fa fa-book"></i><span class="menu-icon-text"> Docs</span></a>
          </li>
				<?php endif; ?>

          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="<?=PATH['theme'];?>anvil/img/avatar.png" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?= $this->session->userdata('username'); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="<?=PATH['theme'];?>anvil/img/avatar.png" class="img-circle" alt="User Image">

                <p>
                  <?= $this->session->userdata('username'); ?><!-- - Site Role / Rank -->
                  <!--<small>Member since Nov. 2017</small>-->
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?= site_url('/admin/users/edit/'.$this->session->userdata('userID')); ?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?= site_url('/admin/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>


  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?=PATH['theme'];?>anvil/img/avatar.png" class="img-rounded" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?= $this->session->userdata('username'); ?></p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		  <!-- Set status options to Online,Busy,Away,Offline. -->
        </div>
      </div>

      <!-- search form (Optional) Delete Set a Background img ? -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="active"><a href="<?= site_url('/admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

        <?php if (in_array('pages_templates', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-paint-brush"></i> <span>Templates / Themes</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<li><a href="<?php echo site_url('/admin/pages/templates'); ?>"><i class="fa fa-angle-double-right"></i>All Templates</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes'); ?>"><i class="fa fa-angle-double-right"></i>Includes</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes/css'); ?>"><i class="fa fa-angle-double-right"></i>CSS</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes/js'); ?>"><i class="fa fa-angle-double-right"></i>Javascript</a></li>
              <li><a href="<?php echo site_url('/admin/pages/navigation'); ?>"><i class="fa fa-angle-double-right"></i>Navigation</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (in_array('images', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-file-image-o"></i> <span>Images / Files</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<li><a href="<?php echo site_url('/admin/images/viewall'); ?>"><i class="fa fa-angle-double-right"></i>Images</a></li>
							<?php if (in_array('images_all', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/images/folders'); ?>"><i class="fa fa-angle-double-right"></i>Image Folders</a></li>
							<?php endif; ?>
							<?php if (in_array('files', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/files/viewall'); ?>"><i class="fa fa-angle-double-right"></i>Files</a></li>
								<?php if (in_array('files_all', $this->permission->permissions)): ?>
									<li><a href="<?php echo site_url('/admin/files/folders'); ?>"><i class="fa fa-angle-double-right"></i>File Folders</a></li>
								<?php endif; ?>
							<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (in_array('webforms', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-edit"></i> <span>Web Forms</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<li><a href="<?php echo site_url('/admin/webforms/tickets'); ?>"><i class="fa fa-angle-double-right"></i>Tickets</a></li>
							<?php if (in_array('webforms_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/webforms/viewall'); ?>"><i class="fa fa-angle-double-right"></i>All Web Forms</a></li>
								<li><a href="<?php echo site_url('/admin/webforms/add_form'); ?>"><i class="fa fa-angle-double-right"></i>Add Web Form</a></li>
							<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php // Node Modules needs to be generated, not static?>
        <li class="header">MODULES</li>
        <?php if (in_array('pages', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-files-o"></i> <span>Pages</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<li><a href="<?php echo site_url('/admin/pages/viewall'); ?>"><i class="fa fa-angle-double-right"></i>All Pages</a></li>
							<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/pages/add'); ?>"><i class="fa fa-angle-double-right"></i>Add Page</a></li>
							<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

          <li class="treeview">
            <a href="#"><i class="fa fa-newspaper-o"></i> <span>Blog</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<?php if (in_array('blog', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/blog/viewall'); ?>"><i class="fa fa-angle-double-right"></i>All Posts</a></li>
							<?php endif; ?>
							<?php if (in_array('blog_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/blog/add_post'); ?>"><i class="fa fa-angle-double-right"></i>Add Post</a></li>
							<?php endif; ?>
							<?php if (in_array('blog_cats', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/blog/categories'); ?>"><i class="fa fa-angle-double-right"></i>Categories</a></li>
							<?php endif; ?>
                <li><a href="<?php echo site_url('/admin/blog/comments'); ?>"><i class="fa fa-angle-double-right"></i>Comments</a></li>
            </ul>
          </li>

        <?php if (in_array('forums', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-list-alt"></i> <span>Forum</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
						<?php if (in_array('forums', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/forums/forums'); ?>"><i class="fa fa-angle-double-right"></i>Forums</a></li>
						<?php endif; ?>
						<?php if (in_array('forums_cats', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/forums/categories'); ?>"><i class="fa fa-angle-double-right"></i>Forum Categories</a></li>
						<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (in_array('shop', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-shopping-cart"></i> <span>Shop</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<li><a href="<?php echo site_url('/admin/shop/products'); ?>"><i class="fa fa-angle-double-right"></i>All Products</a></li>
							<?php if (in_array('shop_edit', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/add_product'); ?>"><i class="fa fa-angle-double-right"></i>Add Product</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_cats', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/categories'); ?>"><i class="fa fa-angle-double-right"></i>Categories</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_orders', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/orders'); ?>"><i class="fa fa-angle-double-right"></i>View Orders</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_shipping', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/bands'); ?>"><i class="fa fa-angle-double-right"></i>Shipping Bands</a></li>
								<li><a href="<?php echo site_url('/admin/shop/postages'); ?>"><i class="fa fa-angle-double-right"></i>Shipping Costs</a></li>
								<li><a href="<?php echo site_url('/admin/shop/modifiers'); ?>"><i class="fa fa-angle-double-right"></i>Shipping Modifiers</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_discounts', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/discounts'); ?>"><i class="fa fa-angle-double-right"></i>Discount Codes</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_reviews', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/reviews'); ?>"><i class="fa fa-angle-double-right"></i>Reviews</a></li>
							<?php endif; ?>
							<?php if (in_array('shop_upsells', $this->permission->permissions)): ?>
								<li><a href="<?php echo site_url('/admin/shop/upsells'); ?>"><i class="fa fa-angle-double-right"></i>Upsells</a></li>
							<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (in_array('events', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-calendar"></i> <span>Events</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
							<li><a href="<?php echo site_url('/admin/events/viewall'); ?>"><i class="fa fa-angle-double-right"></i>All Events</a></li>
						<?php if (in_array('events_edit', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/events/add_event'); ?>"><i class="fa fa-angle-double-right"></i>Add Event</a></li>
						<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (in_array('wiki', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-file-text-o"></i> <span>Wiki</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
						<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/wiki/viewall'); ?>"><i class="fa fa-angle-double-right"></i>All Wiki Pages</a></li>
						<?php endif; ?>
						<?php if (in_array('wiki_cats', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/wiki/categories'); ?>"><i class="fa fa-angle-double-right"></i>Wiki Categories</a></li>
						<?php endif; ?>
						<li><a href="<?php echo site_url('/admin/wiki/changes'); ?>"><i class="fa fa-angle-double-right"></i>Recent Changes</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <li class="header">SYSTEM</li>

        <?php if (in_array('users', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-users"></i> <span>Users &amp; Groups</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
            <?php if (in_array('users_groups', $this->permission->permissions)): ?>
							<li><a href="<?php echo site_url('/admin/users/viewall'); ?>"><i class="fa fa-angle-double-right"></i>All Users</a></li>
							<li><a href="<?php echo site_url('/admin/users/groups'); ?>"><i class="fa fa-angle-double-right"></i>User Groups</a></li>
            <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">


<?php endif; ?>


<?php if ($this->session->userdata('session_admin') && (strpos(FULL_URL, 'admin/login') == true)) : ?>

	<h1>Logout</h1>

	<p><a href="<?= site_url('admin/logout');?>">Click here to logout.</a></p>

<?php endif; ?>
