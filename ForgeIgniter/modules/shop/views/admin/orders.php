<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; }
.ac_loading { background: white url('<?php echo $this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript">
function formatItem(row) {
	if (row[0].length) return row[1]+'<br /><span class="email">(#'+row[0]+')</span>';
	else return 'No results';
}
$(function(){
	$('#searchbox').autocomplete("<?php echo site_url('/admin/shop/ac_orders'); ?>", { delay: "0", selectFirst: false, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});
	$('select#filter').change(function(){
		var status = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/shop/orders'); ?>/'+status;
	});
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Shop :
		<small>Orders</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
		<li class="active">Orders <?php if ($trackingStatus) echo '('.$statusArray[$trackingStatus].')'?></li>
	  </ol>
	</section>

	<!-- Main content -->
    <section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-users"></i>
						<h3 class="box-title">Orders</h3>

						<div class="box-tools">

							<?php
								foreach ($statusArray as $key => $status):
									$options[$key] = $status;
								endforeach;

								echo form_dropdown('filter',$options,$trackingStatus,'id="filter" style="right:165px;"');
							?>

							<a href="<?= site_url('/admin/shop/export_orders'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue">Export Orders as CSV</a>

						</div>
					</div> <!-- End box-header -->

					<div class="box-body table-responsive no-padding">

						<div class="headingright">

							<form method="post" action="<?= site_url('/admin/shop/orders'); ?>" class="default" id="search">
								<input type="text" name="searchbox" id="searchbox" class="formelement inactive" title="Search Products..." />
								<input type="image" src="<?= base_url($this->config->item('staticPath')); ?>/images/btn_search.gif" id="searchbutton" />
							</form>

						</div>

						<div class="clear"></div>

						<?php if ($orders): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="table table-hover">
							<tr>
								<th>Order ID</th>
								<th>Date Ordered</th>
								<th>Full Name</th>
								<th>Number of Items</th>
								<th class="narrow">Total (<?php echo currency_symbol(); ?>)</th>
								<th>Status</th>
								<th class="tiny">&nbsp;</th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php foreach ($orders as $order):
							if (!$order['viewed']) $class = 'style="font-weight: bold;"'; else $class='';
						?>
							<tr <?php echo $class ?>>
								<td><?php echo anchor('/admin/shop/view_order/'.$order['transactionID'], $order['transactionCode']); ?></td>
								<td><?php echo dateFmt($order['dateCreated'], '', '', TRUE); ?></td>
								<td><?php echo $order['firstName']; ?> <?php echo $order['lastName']; ?></td>
								<td><?php echo $order['numItems']; ?></td>
								<td><?php echo currency_symbol().number_format($order['amount'],2); ?></td>
								<td>
									<?php
										if ($order['trackingStatus'] == 'U' && $order['paid']) echo 'Unprocessed';
										elseif ($order['trackingStatus'] == 'L') echo 'Allocated';
										elseif ($order['trackingStatus'] == 'A') echo 'Awaiting Goods';
										elseif ($order['trackingStatus'] == 'O') echo 'Out of Stock';
										elseif ($order['trackingStatus'] == 'D') echo 'Dispatched';
										else echo 'Unpaid Checkout';
									?>
								</td>
								<td><?php echo anchor('/admin/shop/view_order/'.$order['transactionID'], 'View'); ?></td>
								<td><?php echo anchor('/admin/shop/delete_order/'.$order['transactionID'], 'Delete', 'onclick="return confirm(\'Are you absolutely sure you want to delete this order? There is no undo.\')"'); ?></td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

						<?php else: ?>

						<p class="clear">There were no orders found.</p>

						<?php endif; ?>

					</div> <!-- End Box Body -->
				</div> <!-- End Box -->
			</div> <!-- End Row -->
		</section>
	</section>
