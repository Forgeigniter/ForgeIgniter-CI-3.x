<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; }
.ac_loading { background: white url('<?php echo base_url() . $this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
$(function(){
    $('#searchbox').fieldreplace();
	function formatItem(row) {
		if (row[0].length) return row[1]+'<br /><span class="email">('+row[0]+')</span>';
		else return 'No results';
	}
	$('#searchbox').autocomplete("<?php echo site_url('/admin/users/ac_users'); ?>", { delay: "0", selectFirst: false, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Users :
		<small>Manage All</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/users'); ?>"><i class="fa fa-users"></i> Users</a></li>
		<li class="active">Manage All</li>
	  </ol>
	</section>

	<!-- Main content -->
    <section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-users"></i>
						<h3 class="box-title">Users</h3>

						<div class="box-tools">

							<?php if (in_array('users_edit', $this->permission->permissions)): ?>
								<a href="<?= site_url('/admin/users/add'); ?>" class="mb-xs mt-xs mr-xs btn btn-green" style="margin-left: 15px;">Add User</a>
							<?php endif; ?>

							<?php if (in_array('users_groups', $this->permission->permissions)): ?>
								<a href="<?php echo site_url('/admin/users/groups'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="margin-left: 15px;">Groups</a>
							<?php endif; ?>

							<?php if (in_array('users_import', $this->permission->permissions)): ?>
								<a href="<?php echo site_url('/admin/users/export'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="margin-left: 15px;">Export Users</a>
								<a href="<?php echo site_url('/admin/users/import'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue">Import Users</a>
							<?php endif; ?>

						</div>
					</div> <!-- End box-header -->

					<div class="box-body">

						<div class="headingright">

							<form method="post" action="<?php echo site_url('/admin/users/viewall'); ?>" class="default" id="search">
								<input type="text" name="searchbox" id="searchbox" class="formelement inactive" title="Search Users..." />
								<input type="image" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_search.gif" id="searchbutton" />
							</form>

						</div>

						<?php if ($users): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="default clear">
							<tr>
								<th><?php echo order_link('/admin/users/viewall','username','Username'); ?></th>
								<th><?php echo order_link('/admin/users/viewall','datecreated','Date Created'); ?></th>
								<th><?php echo order_link('/admin/users/viewall','lastname','Name'); ?></th>
								<th><?php echo order_link('/admin/users/viewall','email','Email'); ?></th>
								<th><?php echo order_link('/admin/users/viewall','groupid','Group'); ?></th>
								<th class="tiny">&nbsp;</th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php foreach ($users as $user): ?>
						<?php
							$class = '';
							if ($user['groupID'] == $this->site->config['groupID'] || $user['groupID'] < 0) $class = 'class="blue"';
							elseif (@in_array($user['groupID'], $adminGroups)) $class = 'class="orange"';

							$username = ($user['username']) ? $user['username'] : '(not set)';
							$userlink = (in_array('users_edit', $this->permission->permissions)) ? anchor('/admin/users/edit/'.$user['userID'], $username) : $username;

						?>
							<tr <?php echo $class; ?>>
								<td><?php echo $userlink; ?></td>
								<td><?php echo dateFmt($user['dateCreated'], '', '', TRUE); ?></td>
								<td><?php echo trim($user['firstName'].' '.$user['lastName']); ?></td>
								<td><?php echo $user['email']; ?></td>
								<td>
									<?php
										if ($user['groupID'] == $this->site->config['groupID'] || $user['groupID'] < 0) echo 'Administrator';
										elseif (@in_array($user['groupID'], $adminGroups)) echo $userGroups[$user['groupID']];
										elseif (@in_array($user['groupID'], $normalGroups)) echo $userGroups[$user['groupID']];
									?>
								</td>
								<td class="tiny">
									<?php if (in_array('users_edit', $this->permission->permissions)): ?>
										<?php echo anchor('/admin/users/edit/'.$user['userID'], 'Edit'); ?>
									<?php endif; ?>
								</td>
								<td class="tiny">
									<?php if (in_array('users_delete', $this->permission->permissions)): ?>
										<?php echo anchor('/admin/users/delete/'.$user['userID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
									<?php endif; ?>

								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<?php else: ?>

						<p class="clear">No users found.</p>

						<?php endif; ?>

					</div> <!-- End Box Body -->
				</div> <!-- End Box -->
			</div> <!-- End Row -->
		</section>
