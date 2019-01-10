	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Blog :
		<small>Comments</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Forum</a></li>
		<li class="active">Comments</li>
	  </ol>
	</section>

	<!-- Main content -->
    <section class="content container-fluid">
		<section class="content">

			<div class="row">
				<div class="pull-left">
					<a href="<?php echo site_url('/admin/blog');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Posts</a>
				</div>
			</div>

			<div class="row">
				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-newspaper-o"></i>
						<h3 class="box-title">Comments</h3>
					</div>

					<div class="box-body table-responsive no-padding">

						<?php if ($comments): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="table table-hover">
							<tr>
								<th>Date Posted</th>
								<th>Post</th>
								<th>Author</th>
								<th>Email</th>
								<th>Comment</th>
								<th>Status</th>
								<th class="tiny">&nbsp;</th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php foreach ($comments as $comment): ?>
							<tr>
								<td><?php echo dateFmt($comment['dateCreated']); ?></td>
								<td><?php echo anchor('/blog/'.dateFmt($comment['uriDate'], 'Y/m/').$comment['uri'], $comment['postTitle']); ?></td>
								<td><?php echo $comment['fullName']; ?></td>
								<td><?php echo $comment['email']; ?></td>
								<td><small><?php echo (strlen($comment['comment'] > 50)) ? htmlentities(substr($comment['comment'], 0, 50)).'...' : htmlentities($comment['comment'],ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?></small></td>
								<td><?php echo ($comment['active']) ? '<span style="color:green;">Active</span>' : '<span style="color:orange;">Pending</span>'; ?></td>
								<td><?php echo (!$comment['active']) ? anchor('/admin/blog/approve_comment/'.$comment['commentID'], 'Approve') : ''; ?></td>
								<td>
									<?php echo anchor('/admin/blog/delete_comment/'.$comment['commentID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<?php else: ?>

						<p class="clear" style="padding:10px;">There are no comments yet.</p>

						<?php endif; ?>

					</div> <!-- End Box Body -->

				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>
	</section>
