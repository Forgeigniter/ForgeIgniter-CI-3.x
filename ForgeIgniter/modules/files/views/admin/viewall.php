<style type="text/css">
.ac_results { padding: 0px; border: 1px solid black; background-color: white; overflow: hidden; z-index: 99999; }
.ac_results ul { width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0; }
.ac_results li { margin: 0px; padding: 2px 5px; cursor: default; display: block; font: menu; font-size: 12px; line-height: 16px; overflow: hidden; }
.ac_results li span.email { font-size: 10px; }
.ac_loading { background: white url('<?php echo base_url().$this->config->item('staticPath'); ?>/images/loader.gif') right center no-repeat; }
.ac_odd { background-color: #eee; }
.ac_over { background-color: #0A246A; color: white; }
</style>

<script language="javascript" type="text/javascript" src="<?php echo base_url().$this->config->item('staticPath'); ?>/js/jquery.fieldreplace.js"></script>
<script type="text/javascript">
$(function(){
	$('.toggle-file').click(function(event){
		event.preventDefault();
		$('div#upload-file').slideToggle('400');
		$("#upload-file").removeClass("hidden");
		$('div#upload-zip:visible, div#loader:visible').slideToggle('400');
	});

	$('.cancel-file').click(function(event){
		event.preventDefault();
		$('div#upload-file').slideToggle('400');
		$("#upload-file").addClass("hidden");
	});

	$('.editz').click(function(event){
		event.preventDefault();
		$.scrollTo(0, '200');
		$('div#loader').load(this.href, function(){
			$('div#loader:hidden').toggle('400');
			$('div#upload-zip:visible, div#upload-file:visible').slideToggle('400');
		});
	});

    $('#searchbox').fieldreplace();
	function formatItem(row) {
		if (row[0].length) return row[1]+'<br /><span class="email">(#'+row[0]+')</span>';
		else return 'No results';
	}
	$('#searchbox').autocomplete("<?= site_url('/admin/files/ac_files'); ?>", { delay: "0", selectFirst: false, matchContains: true, formatItem: formatItem, minChars: 2 });
	$('#searchbox').result(function(event, data, formatted){
		$(this).parent('form').submit();
	});

});
$(function(){
	$('select#folderID').change(function(){
		var folderID = ($(this).val());
		window.location.href = '<?= site_url('/admin/files/viewall'); ?>/'+folderID;
	});
});
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
	Images / Files :
	<small>Files</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/files'); ?>"><i class="fa fa-file-o"></i> Images / Files</a></li>
	<li class="active">Files</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid">

	<section class="content">
		<div class="row extra-padding">

			<div class="box box-crey">
				<div class="box-header with-border">
				<i class="fa fa-file-o"></i>
				<h3 class="box-title">Files</h3>
					<div class="box-tools">

						<?php
							//$options = NULL;
							$options['me'] = 'My Files';
							if (in_array('files_all', $this->permission->permissions)):
								$options['all'] = 'View All Files';

								if ($folders):
									foreach ($folders as $folder):
										$options[$folder['folderID']] = $folder['folderName'];
									endforeach;
								endif;
							endif;
							echo form_dropdown('folderID', $options, $folderID, 'id="folderID" class="form-control" style="right: 115px; width: 150px;"');
						?>

						<?php if ($this->site->config['plan'] = 0 || $this->site->config['plan'] = 6 || (($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6) && $quota < $this->site->plans['storage'])): ?>
						<a href="#" class="toggle-file mb-xs mt-xs mr-xs btn btn-green" style="float:none;">Upload Zip</a>
						<?php endif; ?>

					</div>
				</div>

				<div class="box-body">

					<div class="headingright">

						<form method="post" action="<?= site_url('/admin/files/viewall'); ?>" class="default" id="search">
							<input type="text" name="searchbox" id="searchbox" class="formelement inactive" title="Search Files..." />
							<input type="image" src="<?= base_url($this->config->item('staticPath')); ?>/images/btn_search.gif" id="searchbutton" />
						</form>

						<label for="folderID">
							Folder
						</label>

					</div>

					<?php if ($errors = validation_errors()): ?>
					<div class="callout callout-danger">
						<h4>Warning!</h4>
						<?php echo $errors; ?>
					</div>
					<?php endif; ?>

					<div id="upload-file"class="hidden clear">
						<form method="post" action="<?= site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

							<label for="file">File:</label>
							<div class="uploadfile">
								<?php echo @form_upload('file',$this->validation->file, 'size="16" id="file"'); ?>
							</div>
							<br class="clear" />

							<label for="fileFolderID">Folder: <small>[<a href="<?php echo site_url('/admin/files/folders'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
							<?php
								$options = NULL;
								$options[0] = 'No Folder';
								if ($folders):
									foreach ($folders as $folderID):
										$options[$folderID['folderID']] = $folderID['folderName'];
									endforeach;
								endif;

								echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="fileFolderID" class="formelement"');
							?>
							<br class="clear" /><br />

							<input type="submit" value="Upload File" class="button nolabel" id="submit" />
							<a href="<?= site_url('/admin/files'); ?>" class="cancel-file button cancel grey">Cancel</a>

						</form>
					</div>

					<div id="loader" class="hidden clear"></div>

					<?php if ($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6): ?>

						<?php if ($quota > $this->site->plans['storage']): ?>

						<div class="error clear">
							<p>You have gone over your storage capacity, we will be contacting you soon.</p>
						</div>

						<div class="quota">
							<div class="over"><?= floor($quota / $this->site->plans['storage'] * 100); ?>%</div>
						</div>

						<?php else: ?>

						<div class="quota">
							<div class="used" style="width: <?= ($quota > 0) ? (floor($quota / $this->site->plans['storage'] * 100)) : 0; ?>%"><?php echo floor($quota / $this->site->plans['storage'] * 100); ?>%</div>
						</div>

						<?php endif; ?>

						<p><small>You have used <strong><?= number_format($quota); ?>kb</strong> out of your <strong><?= number_format($this->site->plans['storage']); ?> KB</strong> quota.</small></p>

					<?php endif; ?>

					<?php if ($files): ?>

						<?= $this->pagination->create_links(); ?>

						<table class="images files clear">
							<tr>
							<?php
								$numItems = sizeof($files);
								$itemsPerRow = 6;
								$i = 0;

								foreach ($files as $file)
								{
									if (($i % $itemsPerRow) == 0 && $i > 1)
									{
										echo '</tr><tr>'."\n";
										$i = 0;
									}
									echo '<td align="center" valign="top" width="'.floor(( 1 / $itemsPerRow) * 100).'%">';

									$extension = substr($file['filename'], strpos($file['filename'], '.')+1);
									$filePath = base_url().'files/'.$file['fileRef'].'.'.$extension;

							?>

									<div class="buttons">
										<?= anchor('/admin/files/edit/'.$file['fileID'].'/', '<img src="'.base_url($this->config->item('staticPath')).'/images/btn_edit.png" alt="Edit" />', 'class="edit"'); ?>
										<?= anchor('/admin/files/delete/'.$file['fileID'], '<img src="'.base_url($this->config->item('staticPath')).'/images/btn_delete.png" alt="Delete" />', 'onclick="return confirm(\'Are you sure you want to delete this file?\')"'); ?>
									</div>

									<a href="<?= $filePath; ?>" title="<?php echo $file['fileRef']; ?>"><img src="<?php echo base_url($this->config->item('staticPath')); ?>/fileicons/<?php echo $extension; ?>.png" alt="<?php echo $file['fileRef']; ?>" class="file" /></a>

									<p><strong><?= $file['fileRef']; ?></strong></p>

							<?php
									echo '</td>'."\n";
									$i++;
								}

								for($x = 0; $x < ($itemsPerRow - $i); $x++)
								{
									echo '<td width="'.floor((1 / $itemsPerRow) * 100).'%">&nbsp;</td>';
								}
							?>
							</tr>
						</table>

						<?= $this->pagination->create_links(); ?>

					<?php else: ?>

					<p class="clear">You have not yet uploaded any files.</p>

					<?php endif; ?>

				</div> <!-- end box body -->

			</div> <!-- end box -->
		</div> <!-- end row -->
	</section>
</section>
