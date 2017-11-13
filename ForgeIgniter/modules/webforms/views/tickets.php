<script language="javascript" type="text/javascript">
$(function(){
	$('select#filter').change(function(){
		var status = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/webforms/tickets'); ?>/'+status;
	});
});
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
	Web Forms :
	<small>Tickets</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/webforms'); ?>"><i class="fa fa-edit"></i> Web Forms</a></li>
	<li class="active">Tickets</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid">

	<section class="content">
		<div class="row extra-padding">

			<div class="box box-crey">
				<div class="box-header with-border">
				<i class="fa fa-edit"></i>
				<h3 class="box-title">Tickets <small><?php if ($status) echo '('.$status.')'?></small></h3>

					<div class="box-tools">
						<select id="collapse" class="form-control" style="right: 110px;">
							<option value="all">Show all</option>
							<option value="hidden">Hide hidden pages</option>
							<option value="collapse">Hide sub-pages</option>
							<option value="drafts">Hide drafts</option>
						</select>

						<?php
							$options[''] = 'View All';
							$options['open'] = 'Open';
							$options['closed'] = 'Closed';

							$options['-'] = '--------------------';

							if ($webforms)
							{
								foreach($webforms as $form)
								{
									$options[$form['formID']] = $form['formName'];
								}
							}

							echo form_dropdown('filter', $options, $this->uri->segment(4), 'id="filter" class="form-control" style="right: 110px;"');
						?>

						<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
							<a href="<?= site_url('/admin/webforms/viewall'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue">Web Forms</a>
						<?php endif; ?>
					</div>

				</div>

				<div class="box-body table-responsive no-padding">

					<?php if ($tickets): ?>

					<?php echo $this->pagination->create_links(); ?>

					<table class="table table-hover">
						<tr>
							<th><?php echo order_link('admin/webforms/tickets','subject','Subject'); ?></th>
							<th><?php echo order_link('admin/webforms/tickets','dateCreated','Date'); ?></th>
							<th><?php echo order_link('admin/webforms/tickets','formName','Web Form'); ?></th>
							<th><?php echo order_link('admin/webforms/tickets','status','Status'); ?></th>
							<th><?php echo order_link('admin/webforms/tickets','fullName','Name'); ?></th>
							<th><?php echo order_link('admin/webforms/tickets','email','Email'); ?></th>
							<th class="tiny">&nbsp;</th>
							<th class="tiny">&nbsp;</th>
						</tr>
					<?php
						$i=0;
						foreach ($tickets as $ticket):
						$class = ($i % 2) ? ' class="alt"' : '';
						$style = (!$ticket['viewed']) ? ' style="font-weight: bold;"' : '';
						$i++;
					?>
						<tr<?php echo $class; ?><?php echo $style; ?>>
							<td><?php echo anchor('/admin/webforms/view_ticket/'.$ticket['ticketID'], '[#'.$ticket['ticketID'].']: '.$ticket['subject']); ?></td>
							<td><?php echo dateFmt($ticket['dateCreated'], '', '', TRUE); ?></td>
							<td><?php echo ($ticket['formName']) ? anchor('/admin/webforms/viewall', $ticket['formName']) : ''; ?></td>
							<td><?php echo ($ticket['closed']) ? 'Closed' : 'Open'; ?></td>
							<td><?php echo $ticket['fullName']; ?></td>
							<td><?php echo $ticket['email']; ?></td>
							<td class="tiny">
								<?php echo anchor('/admin/webforms/view_ticket/'.$ticket['ticketID'], 'Edit'); ?>
							</td>
							<td class="tiny">
								<?php if (in_array('webforms_tickets', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/webforms/delete_ticket/'.$ticket['ticketID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php echo $this->pagination->create_links(); ?>

					<?php else: ?>

					<div class="col col-md-4" style="padding:10px;">
						<p class="clear">There are no tickets here yet.</p>
					</div>
					<?php endif; ?>

				</div> <!-- end box body -->

			</div> <!-- end box -->

		</div> <!-- end row -->
	</section>
