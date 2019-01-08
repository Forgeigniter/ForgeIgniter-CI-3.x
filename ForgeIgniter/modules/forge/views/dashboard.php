<script type="text/javascript">
	var days = <?php echo $days; ?>;
</script>

<!-- FLOT CHARTS -->
<script type="text/javascript" src="<?=PATH['theme'];?>anvil/plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script type="text/javascript" src="<?=PATH['theme'];?>anvil/plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT TIME PLUGIN -->
<script type="text/javascript" src="<?=PATH['theme'];?>anvil/plugins/flot/jquery.flot.time.min.js"></script>

<script type="text/javascript" src="<?=PATH['theme'];?>anvil/plugins/flot/jquery.flot.init.js"></script>

<!--[if IE]>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/excanvas.js"></script>
<![endif]-->

<script type="text/javascript">
function refresh(){
	$('div.loader').load('<?php echo site_url('/admin/activity_ajax'); ?>');
	timeoutID = setTimeout(refresh, 5000);
}
$(function(){
	timeoutID = setTimeout(refresh, 5000);
});
</script>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		<?php echo ($this->session->userdata('firstName')) ? ucfirst($this->session->userdata('firstName')) : $this->session->userdata('username'); ?>'s Dashboard :

        <small>Main Control Panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Control Panel</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">



<section class="content">

	<!-- Main row -->
	<div class="row">
	<!-- Left col -->
	<section class="col-lg-9">

		<?php if ($errors = validation_errors()): ?>
		<div class="callout callout-danger">
			<h4>Warning!</h4>
			<?php echo $errors; ?>
     	</div>
		<?php endif; ?>

		<?php if ($message): ?>
		<div class="callout callout-info">
			<h4>Notice</h4>
			<?php echo $message; ?>
     	</div>
		<?php endif; ?>


		<?php if ($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6): ?>

			<div class="quota">
				<div class="<?php echo ($quota > $this->site->plans['storage']) ? 'over' : 'used'; ?>" style="width: <?php echo ($quota > 0) ? (floor($quota / $this->site->plans['storage'] * 100)) : 0; ?>%"><?php echo floor($quota / $this->site->plans['storage'] * 100); ?>%</div>
			</div>

			<p><small>You have used <strong><?php echo number_format($quota); ?>kb</strong> out of your <strong><?php echo number_format($this->site->plans['storage']); ?> KB</strong> quota.</small></p>

		<?php endif; ?>

		<!-- Stats -->
		<div class="box box-grey">
		<div class="box-header with-border">
			<i class="fa fa-line-chart"></i>
			<h3 class="box-title">Website Activity</h3>
		</div>
		<div class="box-body">
			<div class="tab-content no-padding">

		<ul class="dashboardnav">
			<li class="<?php echo ($days == 30) ? 'active' : ''; ?>"><a href="<?php echo site_url('/admin'); ?>">Last 30 Days</a></li>
			<li class="<?php echo ($days == 60) ? 'active' : ''; ?>"><a href="<?php echo site_url('/admin/dashboard/60'); ?>">Last 60 Days</a></li>
			<li class="<?php echo ($days == 90) ? 'active' : ''; ?>"><a href="<?php echo site_url('/admin/dashboard/90'); ?>">3 Months</a></li>
			<li><a href="<?php echo site_url('/admin/tracking'); ?>">Most Recent Visits</a></li>
		</ul>

		<div id="placeholder"></div>


			</div>
		</div><!-- /.box (chat box) -->

		</div>

		<!-- Website Activity -->
		<div class="box box-success">
		<div class="box-header with-border">
			<i class="fa fa-comments-o"></i>
			<h3 class="box-title">Website Activity</h3>
		</div>
		<div class="box-body">
			<div id="activity" class="loader">
				<?php echo $activity; ?>
			</div>
		</div>
		</div><!-- /.box (chat box) -->

		<!-- Nav Big Buttons -->
		<div class="row">
		<div class="col-md-3">
		<?php if (@in_array('pages', $this->permission->permissions)): ?>

			<div class="module">

				<h2><strong>Manage Your Pages</strong></h2>

				<p>You can set up a new page or edit other pages on your website easily.</p>

				<p><a href="<?php echo site_url('/admin/pages'); ?>" class="button">Manage Pages</a></p>

			</div>

		<?php endif; ?>
		</div>

		<div class="col-md-3">
		<?php if (@in_array('pages_templates', $this->permission->permissions)): ?>

			<div class="module last">

				<h2><strong>Build Templates</strong></h2>

				<p>Gain full control over templates for pages and modules (such as the Blog).</p>

				<p><a href="<?php echo site_url('/admin/pages/templates'); ?>" class="button">Manage Templates</a></p>

			</div>

		<?php endif; ?>
		</div>

		<div class="col-md-3">
		<?php if (@in_array('images', $this->permission->permissions)): ?>

			<div class="module">

				<h2><strong>Upload Images</strong></h2>

				<p>Upload images to your website, either individually or with a ZIP file.</p>

				<p><a href="<?php echo site_url('/admin/images'); ?>" class="button">Manage Images</a></p>

			</div>

		<?php endif; ?>
		</div>

		<div class="col-md-3">
		<?php if (@in_array('users', $this->permission->permissions)): ?>

			<div class="module last">

				<h2><strong>Manage Your Users</strong></h2>

				<p>See who's using your site or add administrators to help you run it.</p>

				<p><a href="<?php echo site_url('/admin/users'); ?>" class="button">Manage Users</a></p>

			</div>

		<?php endif; ?>
		</div>

		<div class="col-md-3">
		<?php if (@in_array('blog', $this->permission->permissions)): ?>

			<div class="module">

				<h2><strong>Get Using the Blog</strong></h2>

				<p>Add posts to your blog and view comments made by others.</p>

				<p><a href="<?php echo site_url('/admin/blog'); ?>" class="button">Manage Blog</a></p>

			</div>

		<?php endif; ?>
		</div>

		<div class="col-md-3">
		<?php if (@in_array('shop', $this->permission->permissions)): ?>
			<div class="module last">

				<h2><strong>Build Your Shop</strong></h2>

				<p>Set up categories, add products and sell online through the shop.</p>

				<p><a href="<?php echo site_url('/admin/shop'); ?>" class="button">Manage Shop</a></p>

			</div>
		<?php endif; ?>
		</div>

		</div><!-- /.row -->

		<!-- Most Popular -->
		<div class="row">

		<?php if ($popularPages): ?>
		<div class="col-md-4">
		<h3>Most popular pages</h3>
			<ol>
				<?php foreach ($popularPages as $page): ?>
					<li><?php echo anchor('/admin/pages/edit/'.$page['pageID'], $page['pageName']); ?></li>
				<?php endforeach; ?>
			</ol>
		<?php else: ?>
			<p><small>We don't have this information yet.</small></p>
		<?php endif; ?>

		<br />
		</div>

		<?php if (@in_array('blog', $this->permission->sitePermissions)): ?>
		<div class="col-md-4">
		<h3>Most popular blog posts</h3>

		<?php if ($popularBlogPosts): ?>
			<ol>
				<?php foreach ($popularBlogPosts as $post): ?>
					<li><?php echo anchor('/admin/blog/edit_post/'.$post['postID'], $post['postTitle']); ?></li>
				<?php endforeach; ?>
			</ol>
		<?php else: ?>
			<p><small>We don't have this information yet.</small></p>
		<?php endif; ?>

		<br />
		</div>
		<?php endif; ?>

		<?php if (@in_array('shop', $this->permission->sitePermissions)): ?>
		<div class="col-md-4">
		<h3>Most popular shop products</h3>

		<?php if ($popularShopProducts): ?>
			<ol>
				<?php foreach ($popularShopProducts as $product): ?>
					<li><?php echo anchor('/admin/shop/edit_product/'.$product['productID'], $product['productName']); ?></li>
				<?php endforeach; ?>
			</ol>
			<?php else: ?>
			<p><small>We don't have this information yet.</small></p>

			<?php endif; ?>
		<?php endif; ?>
		</div><!-- /.row -->

	</section><!-- /.Left col -->

	<!-- right Sidebar -->
	<section class="col-lg-3">

		<!-- Site Info -->
		<div class="box box-solid bg-light-blue-gradient">
		<div class="box-header with-border">

			<i class="fa fa-info-circle"></i>
			<h3 class="box-title">
				Site Information
			</h3>
		</div>
		<div class="box-body">
			<div class="small-box">
				<table class="table no-border">
				<tbody><tr>
				</tr>
				<tr>
					<td>Site name :</td>
					<td><?php echo $this->site->config['siteName']; ?></td>
				</tr>
				<tr>
					<td>Site URL :</td>
					<td>
						<small>
							<a href="<?php echo $this->site->config['siteURL']; ?>"><?php echo $this->site->config['siteURL']; ?></a>
						</small>
					</td>
				</tr>
				<tr>
					<td>Site E-mail : </td>
					<td>
						<small>
						<?php
						$site_email = $this->site->config['siteEmail'];
						if (empty($site_email)){
							echo "No E-mail Set";
						} else {
							echo $site_email;
						}
						?>
						</small>
					</td>
				</tr>
				</tbody></table>
			<div class="icon">
				<i class="ion ion-ios-information"></i>
			</div>
			<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
			</div>

		</div><!-- /.box-body-->
		</div><!-- /.box -->

		<!-- Site Statistics -->
		<div class="box box-solid bg-blue-gradient">
		<div class="box-header with-border">

			<i class="fa fa-bar-chart"></i>
			<h3 class="box-title">
				Site Statistics
			</h3>
		</div>
		<div class="box-body">
			<div class="small-box">
				<table class="table no-border">
				<tbody><tr></tr>
				<tr>
					<td>Disk Space Used :</td>
					<td>
						<?php echo number_format($quota); ?> <small>KB</small>
					</td>
				</tr>
				<tr>
					<td>Total Page Views:</td>
					<td>
						<?php echo number_format($numPageViews); ?> <small>views</small>
					</td>
				</tr>
				<tr>
					<td>Pages :</td>
					<td><?php echo $numPages; ?> <small>page<?php echo ($numPages != 1) ? 's' : ''; ?></small></td>
				</tr>

				<?php if (@in_array('blog', $this->permission->permissions)): ?>
				<tr>
					<td>Blog posts :</td>
					<td><?php echo $numBlogPosts ?> <small>post<?php echo ($numBlogPosts != 1) ? 's' : ''; ?></small></td>
				</tr>
				<?php endif; ?>

				</tbody></table>
			<div class="icon">
				<i class="ion ion-stats-bars"></i>
			</div>
			<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div><!-- /.box-body-->
		</div><!-- /.box -->

		<!-- User Statistics -->
		<div class="box box-solid bg-green-gradient">
		<div class="box-header with-border">

			<i class="fa fa-users"></i>
			<h3 class="box-title">
				User Statistics
			</h3>
		</div>

		<div class="box-body">
			<div class="small-box">
				<table class="table no-border">
				<tbody><tr></tr>
				<tr>
					<td>Total users :</td>
					<td>
						<?php echo number_format($numUsers); ?> <small>user<?php echo ($numUsers != 1) ? 's' : ''; ?></small>
					</td>
				</tr>
				<tr>
					<td>New today:</td>
					<td>
						<?php echo number_format($numUsersToday); ?> <small>user<?php echo ($numUsersToday != 1) ? 's' : ''; ?>

						<?php
							$difference = @round(100 / $numUsersYesterday * ($numUsersToday - $numUsersYesterday), 2);
							$polarity = ($difference < 0) ? '' : '+';
						?>
						<?php if ($difference != 0): ?>
							<small style="padding-left:5px;">
								(<span style="color:<?php echo ($polarity == '+') ? '#00ff04' : '#ea604f'; ?>">
									<?php echo $polarity.$difference; ?>%
								</span>)
							</small>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>New yesterday :</td>
					<td>
						<?php echo number_format($numUsersYesterday); ?>
						<small>user<?php echo ($numUsersYesterday != 1) ? 's' : ''; ?></small>
					</td>
				</tr>
				<tr>
					<td>New This Week :</td>
					<td>
						<?php echo number_format($numUsersWeek); ?> <small>user<?php echo ($numUsersWeek != 1) ? 's' : ''; ?></small>

						<?php
							$difference = @round(100 / $numUsersLastWeek * ($numUsersWeek - $numUsersLastWeek), 2);
							$polarity = ($difference < 0) ? '' : '+';
						?>
						<?php if ($difference != 0): ?>
							<small>
								(<span style="color:<?php echo ($polarity == '+') ? '#00ff04' : '#ea604f'; ?>">
									<?php echo $polarity.$difference; ?>%
								</span>)
							</small>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>New Last Week :</td>
					<td>
						<?php echo number_format($numUsersLastWeek); ?>
						<small>user<?php echo ($numUsersLastWeek != 1) ? 's' : ''; ?></small>
					</td>
				</tr>
				</tbody></table>
			<div class="icon">
				<i class="ion ion-ios-people"></i>
			</div>
			<a href="#" class="small-box-footer">Manage Users <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div><!-- /.box-body-->
		</div><!-- /.box -->

	</section><!-- right col -->
	</div><!-- /.row (main row) -->

</section>
