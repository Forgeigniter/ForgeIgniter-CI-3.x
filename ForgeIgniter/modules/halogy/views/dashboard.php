<script type="text/javascript">
	var days = <?php echo $days; ?>;
</script>
<script type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.flot.js"></script>
<!--[if IE]>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/excanvas.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.flot.init.js"></script>
<script type="text/javascript">
function refresh(){
	$('div.loader').load('<?php echo site_url('/admin/activity_ajax'); ?>');
	timeoutID = setTimeout(refresh, 5000);
}
$(function(){
	timeoutID = setTimeout(refresh, 5000);
});
</script>

<div id="tpl-2col">
	
	<div class="col1">

		<h1><strong><?php echo ($this->session->userdata('firstName')) ? ucfirst($this->session->userdata('firstName')) : $this->session->userdata('username'); ?>'s</strong> Dashboard</h1>
		
		<?php if ($errors = validation_errors()): ?>
			<div class="error">
				<?php echo $errors; ?>
			</div>
		<?php endif; ?>

		<?php if ($message): ?>
			<div class="message">
				<?php echo $message; ?>
			</div>
		<?php endif; ?>

		<ul class="dashboardnav">
			<li class="<?php echo ($days == 30) ? 'active' : ''; ?>"><a href="<?php echo site_url('/admin'); ?>">Last 30 Days</a></li>
			<li class="<?php echo ($days == 60) ? 'active' : ''; ?>"><a href="<?php echo site_url('/admin/dashboard/60'); ?>">Last 60 Days</a></li>
			<li class="<?php echo ($days == 90) ? 'active' : ''; ?>"><a href="<?php echo site_url('/admin/dashboard/90'); ?>">3 Months</a></li>
			<li><a href="<?php echo site_url('/admin/tracking'); ?>">Most Recent Visits</a></li>
		</ul>

		<div id="placeholder"></div>
		
		<div id="activity" class="loader">
			<?php echo $activity; ?>
		</div>

		<?php if (@in_array('pages', $this->permission->permissions)): ?>

			<div class="module">
			
				<h2><strong>Manage Your Pages</strong></h2>
			
				<p>You can set up a new page or edit other pages on your website easily.</p>
			
				<p><a href="<?php echo site_url('/admin/pages'); ?>" class="button">Manage Pages</a></p>
				
			</div>

		<?php endif; ?>

		
		<?php if (@in_array('pages_templates', $this->permission->permissions)): ?>

			<div class="module last">
			
				<h2><strong>Build Templates</strong></h2>
			
				<p>Gain full control over templates for pages and modules (such as the Blog).</p>
	
				<p><a href="<?php echo site_url('/admin/pages/templates'); ?>" class="button">Manage Templates</a></p>
				
			</div>
			
		<?php endif; ?>
		
		<?php if (@in_array('images', $this->permission->permissions)): ?>

			<div class="module">
			
				<h2><strong>Upload Images</strong></h2>
			
				<p>Upload images to your website, either individually or with a ZIP file.</p>
	
				<p><a href="<?php echo site_url('/admin/images'); ?>" class="button">Manage Images</a></p>
				
			</div>
			
		<?php endif; ?>
		
		<?php if (@in_array('users', $this->permission->permissions)): ?>
		
			<div class="module last">
			
				<h2><strong>Manage Your Users</strong></h2>
			
				<p>See who's using your site or add administrators to help you run it.</p>
	
				<p><a href="<?php echo site_url('/admin/users'); ?>" class="button">Manage Users</a></p>
				
			</div>

		<?php endif; ?>

		<?php if (@in_array('blog', $this->permission->permissions)): ?>

			<div class="module">
			
				<h2><strong>Get Using the Blog</strong></h2>
			
				<p>Add posts to your blog and view comments made by others.</p>
	
				<p><a href="<?php echo site_url('/admin/blog'); ?>" class="button">Manage Blog</a></p>
				
			</div>
			
		<?php endif; ?>

		<?php if (@in_array('shop', $this->permission->permissions)): ?>
			<div class="module last">
			
				<h2><strong>Build Your Shop</strong></h2>
			
				<p>Set up categories, add products and sell online through the shop.</p>
			
				<p><a href="<?php echo site_url('/admin/shop'); ?>" class="button">Manage Shop</a></p>
				
			</div>
		<?php endif; ?>

		<br class="clear" /><br />

		<?php if ($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6): ?>		

			<div class="quota">
				<div class="<?php echo ($quota > $this->site->plans['storage']) ? 'over' : 'used'; ?>" style="width: <?php echo ($quota > 0) ? (floor($quota / $this->site->plans['storage'] * 100)) : 0; ?>%"><?php echo floor($quota / $this->site->plans['storage'] * 100); ?>%</div>
			</div>
			
			<p><small>You have used <strong><?php echo number_format($quota); ?>kb</strong> out of your <strong><?php echo number_format($this->site->plans['storage']); ?> KB</strong> quota.</small></p>

		<?php endif; ?>

		<br />
	
	</div>
	
	<div class="col2">

		<h3>Site Info</h3>
		
		<table class="default">
			<tr>
				<th class="narrow">Site name:</th>
				<td><?php echo $this->site->config['siteName']; ?></td>
			</tr>
			<tr>
				<th class="narrow">Site URL:</th>
				<td><small><a href="<?php echo $this->site->config['siteURL']; ?>"><?php echo $this->site->config['siteURL']; ?></a></small></td>
			</tr>
			<tr>
				<th class="narrow">Site email:</th>
				<td><small>
					<?php
					$site_email = $this->site->config['siteEmail'];
					if (empty($site_email)){
						echo "No E-mail Set";
					} else {
						echo $site_email;
					}
					?>
				</small></td>
			</tr>
		</table>

		<h3>Site Stats</h3>
		
		<table class="default">
			<tr>
				<th class="narrow">Disk space used:</th>
				<td><?php echo number_format($quota); ?> <small>KB</small></td>
			</tr>
			<tr>
				<th class="narrow">Total page views:</th>
				<td><?php echo number_format($numPageViews); ?> <small>views</small></td>
			</tr>
			<tr>
				<th class="narrow">Pages:</th>
				<td><?php echo $numPages; ?> <small>page<?php echo ($numPages != 1) ? 's' : ''; ?></small></td>
			</tr>
			<?php if (@in_array('blog', $this->permission->permissions)): ?>
				<tr>
					<th class="narrow">Blog posts:</th>
					<td><?php echo $numBlogPosts ?> <small>post<?php echo ($numBlogPosts != 1) ? 's' : ''; ?></small></td>
				</tr>
			<?php endif; ?>
		</table>

		<h3>User Stats</h3>
		
		<table class="default">
			<tr>
				<th class="narrow">Total users:</th>
				<td colspan="2"><?php echo number_format($numUsers); ?> <small>user<?php echo ($numUsers != 1) ? 's' : ''; ?></small></td>
			</tr>
			<tr>
				<th class="narrow">New today:</th>
				<td>			
					<?php echo number_format($numUsersToday); ?> <small>user<?php echo ($numUsersToday != 1) ? 's' : ''; ?></small>
				</td>
				<td>
					<?php
						$difference = @round(100 / $numUsersYesterday * ($numUsersToday - $numUsersYesterday), 2);
						$polarity = ($difference < 0) ? '' : '+';
					?>						
					<?php if ($difference != 0): ?>
						<small>(<span style="color:<?php echo ($polarity == '+') ? 'green' : 'red'; ?>"><?php echo $polarity.$difference; ?>%</span>)</small>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th class="narrow">New yesterday:</th>
				<td colspan="2"><?php echo number_format($numUsersYesterday); ?> <small>user<?php echo ($numUsersYesterday != 1) ? 's' : ''; ?></small></td>
			</tr>
			<tr>
				<th class="narrow">New this week:</th>
				<td>
					<?php echo number_format($numUsersWeek); ?> <small>user<?php echo ($numUsersWeek != 1) ? 's' : ''; ?></small>
				</td>
				<td>
					<?php
						$difference = @round(100 / $numUsersLastWeek * ($numUsersWeek - $numUsersLastWeek), 2);
						$polarity = ($difference < 0) ? '' : '+';
					?>				
					<?php if ($difference != 0): ?>
						<small>(<span style="color:<?php echo ($polarity == '+') ? 'green' : 'red'; ?>"><?php echo $polarity.$difference; ?>%</span>)</small>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th class="narrow">New last week:</th>
				<td colspan="2"><?php echo number_format($numUsersLastWeek); ?> <small>user<?php echo ($numUsersLastWeek != 1) ? 's' : ''; ?></small></td>
			</tr>
		</table>	

		<h3>Most popular pages</h3>

		<?php if ($popularPages): ?>
			<ol>		
				<?php foreach ($popularPages as $page): ?>
					<li><?php echo anchor('/admin/pages/edit/'.$page['pageID'], $page['pageName']); ?></li>
				<?php endforeach; ?>
			</ol>
		<?php else: ?>
			<p><small>We don't have this information yet.</small></p>
		<?php endif; ?>

		<br />
		
<?php if (@in_array('blog', $this->permission->sitePermissions)): ?>

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
		
<?php endif; ?>

<?php if (@in_array('shop', $this->permission->sitePermissions)): ?>		

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
		
	</div>
	
	<br class="clear" />

</div>
