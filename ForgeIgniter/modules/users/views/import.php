<h1>Import Users <small>(<a href="<?php echo site_url('/admin/users'); ?>">Back to Users</a>)</small></h1>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<?php if (isset($message)): ?>
	<div class="message">
		<?php echo $message; ?>
	</div>
<?php endif; ?>

<p>To import user in to the database please make sure you create a CSV file with the first column as Email, the second as First name and the third as Second name.</p>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">
		<label for="csv">CSV File:</label>
		<div class="uploadfile">
			<?php echo @form_upload('csv', '', 'size="16" id="csv"'); ?>
		</div>
		<br class="clear" />

		<input type="hidden" name="test" value="" />

		<input type="submit" value="Upload File" class="button nolabel" id="submit" />
		
</form>
