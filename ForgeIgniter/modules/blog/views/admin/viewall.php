	<script language="JavaScript">
		$(function(){
			$('ul#menubar li').hover(
				function() { $('ul', this).css('display', 'block').parent().addClass('hover'); },
				function() { $('ul', this).css('display', 'none').parent().removeClass('hover'); }
			);
		});
	</script>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Blog :
        <small>Manage All Posts</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Blog</a></li>
        <li class="active">Control Panel</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

<?php if ($blog_posts): ?>
		<!-- /.row -->
	<div class="row extra-padding">
		<div class="col-xs-12">
			<div class="box box-crey">
				<div class="box-header">
					<i class="fa fa-newspaper-o"></i>
					<h3 class="box-title">Blog Posts</h3>
					<div class="box-tools">
					  <a href="<?= site_url('/admin/blog/add_post'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Post</a>
					</div>
				</div>

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

				</div> <!-- End Box body -->
			</div> <!-- End Box -->
		</div>
	</div> <!-- End Row -->

<?php else: ?>
	<table class="table">
		<td> There is no blog posts yet. </td>
	</table>
<?php endif; ?>
