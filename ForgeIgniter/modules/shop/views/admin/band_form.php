<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Band</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<label for="bandName">Name:</label>
	<?php echo @form_input('bandName', $data['bandName'], 'class="formelement" id="bandName"'); ?>
	<br class="clear" />

	<label for="multiplier">Multiplier:</label>
	<?php echo @form_input('multiplier', $data['multiplier'], 'class="formelement small" id="multiplier"'); ?>
	<span class="price">x</span>
	<br class="clear" />

	<input type="submit" value="Save Changes" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />

</form>

<br class="clear" />
