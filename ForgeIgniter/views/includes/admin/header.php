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
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/bower_components/Ionicons/css/ionicons.min.css">

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
  <link rel="stylesheet" href="<?=PATH['theme'];?>anvil/css/skins/skin-anvil-light.css">

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

<link rel="stylesheet" type="text/css" href="<?= base_url($this->config->item('staticPath'));?>/css/admin.css" media="all" />

<!-- jQuery 3 -->
<script src="<?=PATH['theme'];?>anvil/bower_components/jquery/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/jquery-migrate-3.0.0.js"></script>

<!-- jQuery UI -->
<script src="<?=PATH['theme'];?>anvil/plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?=PATH['theme'];?>anvil/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

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

</head>
<?php
// Check if we're at admin login page
if (strpos(FULL_URL,'admin/login') !== FALSE) {
	// Login Body
  echo "<body class='hold-transition skin-anvil-light login-page'>";
}
else {
	// Default Body
	echo "<body class='hold-transition skin-anvil-light sidebar-mini sidebar-collapse'>";
}
?>

<?php if ($this->session->userdata('session_admin') && strpos(FULL_URL,'admin/login') == FALSE): ?>

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
            <a href="<?= site_url('/admin/site/'); ?>"><i class="fa fa-sitemap"></i><span class="menu-icon-text"> My Site</span></a>
          </li>
					<li>
            <a href="http://www.forgeigniter.com/support" target="_blank"><i class="fa fa-book"></i><span class="menu-icon-text"> Docs</span></a>
          </li>
				<?php endif; ?>


<?php /*
Not implemented yet.
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 4 messages</li>
              <li>
                <!-- inner menu: contains the messages -->
                <ul class="menu">
                  <li><!-- start message -->
                    <a href="#">
                      <div class="pull-left">
                        <!-- User Image -->
                        <img src="<?=PATH['theme'];?>anvil/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <!-- Message title and timestamp -->
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <!-- The message -->
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <!-- end message -->
                </ul>
                <!-- /.menu -->
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">
                  <li><!-- start notification -->
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>

          <!-- Tasks Menu -->
          <li class="dropdown tasks-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                <!-- Inner menu: contains the tasks -->
                <ul class="menu">
                  <li><!-- Task item -->
                    <a href="#">
                      <!-- Task title and progress text -->
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <!-- The progress bar -->
                      <div class="progress xs">
                        <!-- Change the css width attribute to simulate progress -->
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
*/?>
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
				<li><a href="<?php echo site_url('/admin/pages/templates'); ?>">All Templates</a></li>
				<li><a href="<?php echo site_url('/admin/pages/includes'); ?>">Includes</a></li>
				<li><a href="<?php echo site_url('/admin/pages/includes/css'); ?>">CSS</a></li>
				<li><a href="<?php echo site_url('/admin/pages/includes/js'); ?>">Javascript</a></li>
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
          <li class="treeview">
            <a href="#"><i class="fa fa-edit"></i> <span>Web Forms</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
				<li><a href="<?php echo site_url('/admin/webforms/tickets'); ?>">Tickets</a></li>
				<?php if (in_array('webforms_edit', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/webforms/viewall'); ?>">All Web Forms</a></li>
					<li><a href="<?php echo site_url('/admin/webforms/add_form'); ?>">Add Web Form</a></li>
				<?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php // Node Modules needs to be generated, not static ?>
        <li class="header">MODULES</li>
        <?php if (in_array('pages', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-files-o"></i> <span>Pages</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
				<li><a href="<?php echo site_url('/admin/pages/viewall'); ?>">All Pages</a></li>
				<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/pages/add'); ?>">Add Page</a></li>
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

        <?php if (in_array('forums', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-list-alt"></i> <span>Forum</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
			<?php if (in_array('forums', $this->permission->permissions)): ?>
				<li><a href="<?php echo site_url('/admin/forums/forums'); ?>">Forums</a></li>
			<?php endif; ?>
			<?php if (in_array('forums_cats', $this->permission->permissions)): ?>
				<li><a href="<?php echo site_url('/admin/forums/categories'); ?>">Forum Categories</a></li>
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
        <?php endif; ?>

        <?php if (in_array('events', $this->permission->permissions)): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-calendar"></i> <span>Events</span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
				<li><a href="<?php echo site_url('/admin/events/viewall'); ?>">All Events</a></li>
			<?php if (in_array('events_edit', $this->permission->permissions)): ?>
				<li><a href="<?php echo site_url('/admin/events/add_event'); ?>">Add Event</a></li>
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
					<li><a href="<?php echo site_url('/admin/wiki/viewall'); ?>">All Wiki Pages</a></li>
				<?php endif; ?>
				<?php if (in_array('wiki_cats', $this->permission->permissions)): ?>
					<li><a href="<?php echo site_url('/admin/wiki/categories'); ?>">Wiki Categories</a></li>
				<?php endif; ?>
				<li><a href="<?php echo site_url('/admin/wiki/changes'); ?>">Recent Changes</a></li>
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
				<li><a href="<?php echo site_url('/admin/users/viewall'); ?>">All Users</a></li>
				<li><a href="<?php echo site_url('/admin/users/groups'); ?>">User Groups</a></li>
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


<?php if ($this->session->userdata('session_admin') && (strpos(FULL_URL,'admin/login') == TRUE)) : ?>

	<h1>Logout</h1>

	<p><a href="<?= site_url('admin/logout');?>">Click here to logout.</a></p>

<?php endif; ?>
