	<link rel="stylesheet" type="text/css" href="<?= base_url() . $this->config->item('staticPath'); ?>/css/admin.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/lightbox.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/datepicker.css" media="screen" />

	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.js"></script>

	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.lightbox.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/default.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/admin.js"></script>

	<script language="JavaScript">
		$(function(){
			$('ul#menubar li').hover(
				function() { $('ul', this).css('display', 'block').parent().addClass('hover'); },
				function() { $('ul', this).css('display', 'none').parent().removeClass('hover'); }
			);
		});
	</script>

<?php if ($blog_posts): ?>
		<!-- /.row -->
	<div class="row extra-padding">
		<div class="col-xs-12">
			<div class="box box-crey">
			<div class="box-header">
				<i class="fa fa-edit"></i>
				<h3 class="box-title">Blog Posts</h3>

				<div class="box-tools">
				  <select class="form-control">
                    <option>Options</option>
                    <option>Some Options</option>
                  </select>
				  <a href=""> </a>
				  <a href="<?= site_url('/admin/blog/add_post'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Post</a>
				</div>
			</div>

			<!-- /.box-header -->
			<div class="box-body table-responsive no-padding">
			
				<table class="table table-hover">

				<tr>
					<th><?php echo order_link('/admin/blog/viewall','posttitle','Post'); ?></th>
					<th><?php echo order_link('/admin/blog/viewall','datecreated','Date'); ?></th>
					<th class="narrow"><?php echo order_link('/admin/blog/viewall','published','Published'); ?></th>
					<th class="tiny">&nbsp;</th>
				</tr>
				<?php foreach ($blog_posts as $post): ?>
				<tr class="<?php echo (!$post['published']) ? 'draft' : ''; ?>">
					<td><?php echo (in_array('blog_edit', $this->permission->permissions)) ? anchor('/admin/blog/edit_post/'.$post['postID'], $post['postTitle']) : $post['postTitle']; ?></td>
					<td><?php echo dateFmt($post['dateCreated'], '', '', TRUE); ?></td>
					<td>
						<?php
							if ($post['published']) echo '<span style="color:green;">Yes</span>';
							else echo 'No';
						?>
					</td>
					<td class="tiny">
						<?php if (in_array('blog_edit', $this->permission->permissions)): ?>
							<?php echo anchor('/admin/blog/edit_post/'.$post['postID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
						<?php endif; ?>
						<?php if (in_array('blog_delete', $this->permission->permissions)): ?>
							<?php echo anchor('/admin/blog/delete_post/'.$post['postID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				</table>
			
			</div>

			<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
	</div>

<?php else: ?>
	<table class="table">
		<td> No content </td>


				<tr>
					<th>ID</th>
					<th>User</th>
					<th>Date</th>
					<th>Status</th>
					<th>Reason</th>
				</tr>
				<tr>
					<td>183</td>
					<td>John Doe</td>
					<td>11-7-2014</td>
					<td><span class="label label-success">Approved</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				<tr>
					<td>219</td>
					<td>Alexander Pierce</td>
					<td>11-7-2014</td>
					<td><span class="label label-warning">Pending</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				<tr>
					<td>657</td>
					<td>Bob Doe</td>
					<td>11-7-2014</td>
					<td><span class="label label-primary">Approved</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>
				<tr>
					<td>175</td>
					<td>Mike Doe</td>
					<td>11-7-2014</td>
					<td><span class="label label-danger">Denied</span></td>
					<td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
				</tr>


	</table>
<?php endif; ?>



<h1 class="headingleft">Blog Posts</h1>

<div class="headingright">
	<?php if (in_array('blog_edit', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/blog/add_post'); ?>" class="button">Add Post</a>
	<?php endif; ?>
</div>

<?php if ($blog_posts): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear">
	<tr>
		<th><?php echo order_link('/admin/blog/viewall','posttitle','Post'); ?></th>
		<th><?php echo order_link('/admin/blog/viewall','datecreated','Date'); ?></th>
		<th class="narrow"><?php echo order_link('/admin/blog/viewall','published','Published'); ?></th>
		<th class="tiny">&nbsp;</th>
		<th class="tiny">&nbsp;</th>
	</tr>
<?php foreach ($blog_posts as $post): ?>
	<tr class="<?php echo (!$post['published']) ? 'draft' : ''; ?>">
		<td><?php echo (in_array('blog_edit', $this->permission->permissions)) ? anchor('/admin/blog/edit_post/'.$post['postID'], $post['postTitle']) : $post['postTitle']; ?></td>
		<td><?php echo dateFmt($post['dateCreated'], '', '', TRUE); ?></td>
		<td>
			<?php
				if ($post['published']) echo '<span style="color:green;">Yes</span>';
				else echo 'No';
			?>
		</td>
		<td class="tiny">
			<?php if (in_array('blog_edit', $this->permission->permissions)): ?>
				<?php echo anchor('/admin/blog/edit_post/'.$post['postID'], 'Edit'); ?>
			<?php endif; ?>
		</td>
		<td class="tiny">			
			<?php if (in_array('blog_delete', $this->permission->permissions)): ?>
				<?php echo anchor('/admin/blog/delete_post/'.$post['postID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no blog posts yet.</p>

<?php endif; ?>