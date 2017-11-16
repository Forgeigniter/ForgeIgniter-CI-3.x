	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Reviews</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Reviews</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-shopping-cart"></i>
					<h3 class="box-title">Reviews</h3>
				</div>

				<div class="box-body table-responsive no-padding">

					<?php if ($reviews): ?>

					<?php echo $this->pagination->create_links(); ?>

					<table class="table table-hover">
						<tr>
							<th>Date Posted</th>
							<th>Product</th>
							<th>Author</th>
							<th>Email</th>
							<th>Review</th>
							<th class="narrow">Status</th>
							<th class="tiny">&nbsp;</th>
							<th class="tiny">&nbsp;</th>
						</tr>
					<?php foreach ($reviews as $review): ?>
						<tr>
							<td><?php echo dateFmt($review['dateCreated']); ?></td>
							<td><?php echo anchor('/shop/viewproduct/'.$review['productID'], $review['productName']); ?></td>
							<td><?php echo $review['fullName']; ?></td>
							<td><?php echo $review['email']; ?></td>
							<td><?php echo (strlen($review['review'] > 50)) ? substr($review['review'], 0, 50).'...' : $review['review']; ?></td>
							<td><?php echo ($review['active']) ? '<span style="color:green;">Active</span>' : '<span style="color:orange;">Pending</span>'; ?></td>
							<td><?php echo (!$review['active']) ? anchor('/admin/shop/approve_review/'.$review['reviewID'], 'Approve') : ''; ?></td>
							<td>
								<?php echo anchor('/admin/shop/delete_review/'.$review['reviewID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php echo $this->pagination->create_links(); ?>

					<?php endif; ?>

				</div> <!-- end box body -->

			</div> <!-- end box -->
		</section>
