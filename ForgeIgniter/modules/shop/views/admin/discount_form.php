<script type="text/javascript">
function showObjects(el){
	if ($(el).val() == 'P'){
		$('div#products').show();
		$('div#categories').hide();
		$('select#productID').removeAttr('disabled');
		$('select#catID').attr('disabled', 'disabled');
	} else if ($(el).val() == 'C'){
		$('div#products').hide();
		$('div#categories').show();
		$('select#productID').attr('disabled', 'disabled');
		$('select#catID').removeAttr('disabled');
		
	} else {
		$('div#products').slideUp(200);
		$('div#categories').slideUp(200);
	}
}

function showModifier(el){
	if ($(el).val() == 'P'){
		$('span#percentage').show();
		$('span#currency').hide();
	} else {
		$('span#percentage').hide();
		$('span#currency').show();
	}
}

$(function(){
	$('input.datebox').datepicker({dateFormat: 'dd M yy'});
	$('select#modifier').change(function(){
		showModifier($(this));
	});
	$('select#type').change(function(){
		showObjects($(this));
	});
	showModifier('select#modifier');
	showObjects('select#type');	
});
</script>

<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Discount</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<label for="code">Code:</label>
	<?php echo @form_input('code', $data['code'], 'class="formelement" id="code"'); ?>
	<br class="clear" />

	<label for="type">Calculated On:</label>
	<?php 
		$values = array(
			'T' => 'Total Value of Cart',
			'P' => 'Products',
			'C' => 'Category'
		);
		echo @form_dropdown('type',$values,set_value('type', $data['type']), 'id="type" class="formelement"'); 
	?>
	<br class="clear" />

	<div style="display: none;" id="categories">
		<label for="catID">Category:</label>
		<?php
			$options = '';
			$options[0] = 'Select a Category...';			
			if ($categories):
				foreach ($categories as $category):
					$options[$category['catID']] = ($category['parentID']) ? '-- '.$category['catName'] : $category['catName'];
				endforeach;
			endif;					
			echo @form_dropdown('catID',$options,set_value('catID', $data['objectID']),'id="catID" class="formelement"');
		?>	
		<br class="clear" />	
	</div>

	<div style="display: none;" id="products">
		<label for="productID">Product:</label>
		<?php
			$options = '';		
			if ($products):
				foreach ($products as $product):
					$options[$product['productID']] = $product['productName'];
				endforeach;
			endif;
			$objectIDArray = (isset($data['objectID'])) ? @explode(',',$data['objectID']) : $this->input->post('productID');
			echo @form_dropdown('productID[]',$options, $objectIDArray, 'id="productID" class="formelement" multiple="multiple"');
		?>	
		<br class="clear" />
	</div>

	<label for="base">Taken Off:</label>
	<?php 
		$values = array(
			'T' => 'Sub Total of Cart',
			'P' => 'Product Price (and quantity)'
		);
		echo @form_dropdown('base',$values,set_value('base', $data['base']), 'id="base" class="formelement"'); 
	?>
	<br class="clear" />

	<label for="modifier">Modifier:</label>
	<?php 
		$values = array(
			'A' => 'Amount',
			'P' => 'Percentage'
		);
		echo @form_dropdown('modifier',$values,set_value('modifier', $data['modifier']), 'id="modifier" class="formelement"'); 
	?>
	<br class="clear" />	
		
	<label for="discount">Discount:</label>
	<span class="price" id="currency"><?php echo currency_symbol(); ?></span>
	<?php echo @form_input('discount', $data['discount'], 'class="formelement small" id="discount"'); ?>
	<span class="price" id="percentage" style="display: none;">%</span>
	<br class="clear" />

	<label for="expiryDate">Expiry Date:</label>
	<?php echo @form_input('expiryDate', dateFmt($data['expiryDate'], 'd M Y'), 'id="expiryDate" class="formelement datebox" readonly="readonly"'); ?>
	<br class="clear" />	
		
	<input type="submit" value="Save Changes" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />
	
</form>

<br class="clear" />
