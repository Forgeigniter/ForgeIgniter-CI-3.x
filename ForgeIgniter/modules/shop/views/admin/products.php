<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; } 
.ac_loading { background: white url('<?php echo $this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript" src="<?php echo $this->config->item('staticPath'); ?>/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/shop/order/product'); ?>',$(this).sortable('serialize'),function(data){ });
};
var fixHelper = function (e, a) {
	if ($(this).is('tbody')) {
		a.children().each(function () {
			$('table.order .' + $(this).attr('class')).width($(this).width())
		})
	}
	return a
};
function initOrder(el){
	$(el).sortable({ 
		axis: 'y',
	    revert: false, 
	    delay: '80',
	    opacity: '0.5',
	    update: setOrder,
		helper: fixHelper
	});
};
function formatItem(row){
	if (row[0].length) return row[1]+'<br /><span class="email">(#'+row[0]+')</span>';
	else return 'No results';
}
$(function(){
    $('#searchbox').fieldreplace();
	$('#searchbox').autocomplete("<?php echo site_url('/admin/shop/ac_products'); ?>", { delay: "0", selectFirst: false, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});
	
	$('select#category').change(function(){
		var folderID = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/shop/products'); ?>/'+folderID;
	});	
	
	initOrder('table.order tbody');	
});
</script>

<h1 class="headingleft">Products</h1>

<div class="headingright">

	<form method="post" action="<?php echo base_url() . site_url('/admin/shop/products'); ?>" class="default" id="search">
		<input type="text" name="searchbox" id="searchbox" class="formelement inactive" title="Search Products..." />
		<input type="image" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_search.gif" id="searchbutton" />
	</form>
	
	<label for="category">
		Category
	</label> 

	<?php
		$options = array(
			'' => 'View All Products...',
			'featured' => 'Featured'
		);
		if ($categories):
			foreach ($categories as $category):
				$options[$category['catID']] = ($category['parentID']) ? '-- '.$category['catName'] : $category['catName'];
			endforeach;
		endif;					
		echo @form_dropdown('catID', $options, set_value('catID', $catID), 'id="category" class="formelement"');
	?>	

	<?php if (in_array('shop_edit', $this->permission->permissions)): ?>	
		<a href="<?php echo site_url('/admin/shop/add_product'); ?>" class="button">Add Product</a>
	<?php endif; ?>
</div>

<div class="clear"></div>

<?php if ($products): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear<?php echo ($catID) ? ' order' : ''; ?>">
	<thead>
		<tr>
			<th><?php echo order_link('admin/shop/products'.(($catID) ? '/'.$catID : ''),'productName','Product name', (($catID) ? 5 : 4)); ?></th>
			<th><?php echo order_link('admin/shop/products'.(($catID) ? '/'.$catID : ''),'subtitle','Subtitle', (($catID) ? 5 : 4)); ?></th>
			<th><?php echo order_link('admin/shop/products'.(($catID) ? '/'.$catID : ''),'catalogueID','Catalogue ID', (($catID) ? 5 : 4)); ?></th>
			<th><?php echo order_link('admin/shop/products'.(($catID) ? '/'.$catID : ''),'dateCreated','Date added', (($catID) ? 5 : 4)); ?></th>
			<th class="narrow"><?php echo order_link('admin/shop/products'.(($catID) ? '/'.$catID : ''),'price','Price ('.currency_symbol().')', (($catID) ? 5 : 4)); ?></th>
			<?php if ($this->site->config['shopStockControl']): ?>
				<th><?php echo order_link('/admin/shop/products'.(($catID) ? '/'.$catID : ''),'stock','Stock', (($catID) ? 5 : 4)); ?></th>
			<?php endif; ?>
			<th class="narrow"><?php echo order_link('/admin/shop/products'.(($catID) ? '/'.$catID : ''),'published','Published', (($catID) ? 5 : 4)); ?></th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>		
		</tr>
	</thead>
	<tbody id="shop_products">
	<?php foreach ($products as $product): ?>
		<tr class="<?php echo (!$product['published']) ? 'draft' : ''; ?>" id="shop_products-<?php echo $product['productID']; ?>">
			<td class="col1"><?php echo (in_array('shop_edit', $this->permission->permissions)) ? anchor('/admin/shop/edit_product/'.$product['productID'], $product['productName']) : $product['productName']; ?></td>
			<td class="col2"><?php echo $product['subtitle']; ?></td>		
			<td class="col3"><?php echo $product['catalogueID']; ?></td>
			<td class="col4"><?php echo dateFmt($product['dateCreated'], '', '', TRUE); ?></td>
			<td class="col5"><?php echo currency_symbol(); ?><?php echo number_format($product['price'],2); ?></td>
			<?php if ($this->site->config['shopStockControl']): ?>
				<td class="col6"><?php echo ($product['stock'] > 0) ? '<span style="color:green;">'.$product['stock'].'</span>' : '<span style="color:red;">'.$product['stock'].'</span>'; ?></td>
			<?php endif; ?>
			<td class="col7">
				<?php
					if ($product['published']) echo '<span style="color:green;">Yes</span>';
					else echo 'No';
				?>
			</td>
			<td class="col8 tiny">
				<?php if (in_array('shop_edit', $this->permission->permissions)): ?>	
					<?php echo anchor('/admin/shop/edit_product/'.$product['productID'], 'Edit'); ?>
				<?php endif; ?>
			</td>
			<td class="col9 tiny">
				<?php if (in_array('shop_delete', $this->permission->permissions)): ?>	
					<?php echo anchor('/admin/shop/delete_product/'.$product['productID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>No products were found.</p>


<?php endif; ?>

