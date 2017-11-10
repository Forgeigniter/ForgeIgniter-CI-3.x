<script type="text/javascript">
$(function(){
	$('div.permissions input[type="checkbox"]').each(function(){
		if ($(this).attr('checked')) {
			$(this).parent('div').prev('div').children('input[type="checkbox"]').attr('checked', true);
		}
	});
	$('.selectall').click(function(){
		$el = $(this).parent('div').next('div').children('input[type="checkbox"]');
		$flag = $(this).attr('checked');
		if ($flag) {
			$($el).attr('checked', true);
		}
		else {
			$($el).attr('checked', false);
		}
	});
	$('.seemore').click(function(){
		$el = $(this).parent('div').next('div');
		$($el).toggle('400');
	});
	$('a.selectall').click(function(event){
		event.preventDefault();
		$('input[type="checkbox"]').attr('checked', true);
	});
	$('a.deselectall').click(function(event){
		event.preventDefault();
		$('input[type="checkbox"]').attr('checked', false);
	});
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Users :
		<small>Edit Group</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/users'); ?>"><i class="fa fa-users"></i> Users</a></li>
		<li class="active">Edit Group</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<section class="content">
			<div class="row">
					<div class="pull-left">
						<a href="<?= site_url('/admin/users/groups'); ?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to User Groups</a>
					</div>
					<div class="col-md-3 pull-right">
						<input
							type="submit"
							value="Save Changes"
							class="btn btn-green margin-bottom"
							style="right:4%;position: absolute;top: 0px;"
						/>
					</div>
			</div>
			<!-- Main row -->
			<div class="row">

				<?php if ($errors = validation_errors()): ?>
				<div class="callout callout-danger">
					<h4>Warning!</h4>
					<?php echo $errors; ?>
				</div>
				<?php endif; ?>

				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-users"></i>
						<h3 class="box-title">Edit User Group</h3>
					</div>

					<div class="box-body" style="padding:20px">
					  <div class="col-sm-3">
						<label for="groupName">Name this group:</label><br class="clear" />
						<?php echo @form_input('groupName',set_value('groupName', $data['groupName']), 'id="groupName" class="formelement"'); ?>
						<br class="clear" /><br />

						<?php if ($permissions): ?>

						<h3>Administrative Permissions</h3>

						<p><a href="#" class="selectall button small nolabel grey">Select All</a> <a href="#" class="deselectall button small grey">De-Select All</a></p>

						<?php foreach ($permissions as $cat => $perms): ?>
						<div class="perm-well">
							<div class="perm-head">
								<label for="<?php echo strtolower($cat); ?>_all" class="radio"><?php echo $cat; ?></label>
								<input type="checkbox" class="selectall checkbox" id="<?php echo strtolower($cat); ?>_all" />
								<input type="button" value="See more" class="seemore small-button" />
							</div>

							<div class="permissions">

							<?php foreach ($perms as $perm): ?>
								<div class="perm-body">
									<div class="perm-content">
										<label for="<?php echo 'perm_'.$perm['key']; ?>" class="radio"><?php echo $perm['permission']; ?></label>
										<?php echo @form_checkbox('perm'.$perm['permissionID'], 1, set_value('perm'.$perm['permissionID'], $data['perm'.$perm['permissionID']]), 'id="'.'perm_'.$perm['key'].'" class="checkbox"'); ?>
										<br class="clear" />
									</div>
								</div>
							<?php endforeach; ?>

							</div>
						</div><!-- End Well -->
						<?php endforeach; ?>
						<?php endif; ?>
					  </div>
					</div> <!-- End box-body -->
				</div> <!-- End box -->
			</div> <!-- End Row -->
		</section>

		</form>
