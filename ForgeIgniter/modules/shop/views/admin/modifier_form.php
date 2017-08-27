<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Modifier</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<?php if ($bands): ?>
	
	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">
	
		<label for="modifierName">Name:</label>
		<?php echo @form_input('modifierName', $data['modifierName'], 'class="formelement" id="modifierName"'); ?>
		<br class="clear" />

		<label for="templateID">Band:</label>
		<?php
			$options = '';
			foreach ($bands as $band):
				$options[$band['bandID']] = $band['bandName'];
			endforeach;
			
			echo @form_dropdown('bandID', $options, $data['bandID'], 'id="bandID" class="formelement"');
		?>	
		<br class="clear" />
			
		<label for="multiplier">Multiplier:</label>
		<?php echo @form_input('multiplier', set_value('multiplier', $data['multiplier']), 'class="formelement small" id="multiplier"'); ?>
		<span class="price">x</span>
		<br class="clear" />
			
		<input type="submit" value="Save Changes" class="button nolabel" />
		<input type="button" value="Cancel" id="cancel" class="button grey" />
		
	</form>

<?php else: ?>

You need to create shipping bands before you can add postage modifiers.

<?php endif; ?>

<br class="clear" />
