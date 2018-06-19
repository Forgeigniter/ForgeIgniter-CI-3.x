<?php if ($modifiers): ?>
	<?php foreach($modifiers as $modifier): ?>
		<option value="<?php echo $modifier['multiplier']; ?>" <?php echo ($modifier['multiplier'] == $shippingModifier) ? 'selected="selected"' : ''; ?>><?php echo $modifier['modifierName']; ?></option>
	<?php endforeach; ?>
<?php endif; ?>
