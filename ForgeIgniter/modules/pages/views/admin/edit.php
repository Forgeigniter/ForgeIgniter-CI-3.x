<?php if (!$templates): ?>

<h1>Add Page</h1>

<br />

<div class="error">
	<p>You have not yet set up any templates and you will need a template in order to create a page. You can add and import templates <a href="<?php echo site_url('/admin/pages/templates'); ?>">here</a>.</p>
</div>

<?php else: ?>

	<!-- Encoded URI (useful for redirecting in modules)
		<?php echo $this->core->encode($data['uri']); ?>
	-->

	<script type="text/javascript">
	var published = <?php echo $data['active']; ?>;
	var newPage = <?php echo $data['deleted']; ?>;
	var changePath = false;
	var changingPath = false;

	function changeTemplate() {
		var templateID = ($('#templateID').val());
		$('#preview').attr('src', '<?php echo site_url('/admin/pages/view_template'); ?>/'+templateID+'/<?php echo $data['pageID']; ?>');
		window.frames['preview.src'] = '<?php echo site_url('/admin/pages/view_template'); ?>/'+templateID+'/<?php echo $data['pageID']; ?>';
		return true;
	}

	function saveall(el, postform){
		var requiredFields = 'input#pageName, input#uri';
		var success = true;
		$(requiredFields).each(function(){
			if (!$(this).val()) {
				$('div.panes').scrollTo(
					0, { duration: 400, axis: 'x' }
				);
				$(this).addClass('error').prev('label').addClass('error');
				$(this).focus(function(){
					$(this).removeClass('error').prev('label').removeClass('error');
				});
				success = false;
			}
		});
		if (!success) return false;

		$('#target').val($(el).attr('name'));
		var blocks = ($('#preview').contents().find('a.halogycms_savebutton').length);
		var updated = 0;
		$('#preview').contents().find('a.halogycms_savebutton').each(function(){
			var blockElement = $(this).parent().siblings('div.halogycms_blockelement');
			var blockForm = $(blockElement).siblings('div.halogycms_editblock');
			var body = $(blockForm).find('textarea').val();
			$.post(this.href,{body: body}, function(data){
				$(blockElement).html(data);
				updated++;
				if (updated == blocks && postform){
					$('#editpage').submit();
				}
			});
		});
		if (blocks){
			return false;
		} else {
			return true;
		}
	}

	function setUri(){
		if (!changingPath){
			changingPath = true;
			var uri = $('#uri').val();
			var pageName = $('#pageName').val();
			var parentID = $('#parentID option:selected').val();
			if (!newPage && !changePath){
				if (confirm('This page is published, are you sure want to change the path to this page?')){
					changePath = 'yes';
				} else {
					changePath = 'no';
				}
			}
			if (changePath == 'yes' || newPage){
				var newUri = $.post('<?php echo site_url('/admin/pages/generate_uri'); ?>', { uri: pageName, parentID: parentID }, function(data){
					$('#uri').val(data);
					$('#title').val(pageName);
				});
			}
			changingPath = false;
		}
	}

	$(function(){
		$('ul.innernav a').click(function(event){
			event.preventDefault();
			$(this).parent().siblings('li').removeClass('selected');
			$(this).parent().addClass('selected');
			$pos = $(this).attr('href');
			$.scrollTo('form', { duration: 200 });
			$('div.panes').scrollTo(
				$pos, { duration: 400, axis: 'x'}
			);
		});

		$('select#templateID').change(function(){
			saveall(null, false);
			changeTemplate();
		});

		$('input.save').click(function(){
			return saveall(this, true);
		});

		$('#pageName').blur(function(){
			setUri();
		});
		$('#parentID').change(function(){
			setUri();
		});

		changeTemplate();
		$('div.panes').scrollTo(
			0, { duration: 400, axis: 'x'}
		);

	});
	</script>

	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default" id="editpage">

		<input type="hidden" name="target" id="target" value="" />

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Pages :
        <small>Manage Page</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Pages</a></li>
        <li class="active">Manage</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

		<section class="content">

		<?php if ($errors = validation_errors()): ?>
			<div class="error clear">
				<?php echo $errors; ?>
			</div>
		<?php endif; ?>
		<?php if (isset($message)): ?>
			<div class="message clear">
				<?php echo $message; ?>
			</div>
		<?php endif; ?>

			<div class="row">
				<div class="pull-left">
					<a href="<?= site_url('/admin/pages/viewall');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Pages</a>
				</div>
				<div class="col-md-6 pull-right">
					<input type="submit" value="Publish" name="publish" class="btn btn-orange margin-bottom save" />
					<input type="submit" value="Save Changes" name="save" id="save" class="btn btn-green margin-bottom save" />
					<input type="submit" value="View Page" name="view" class="btn btn-blue margin-bottom save" />
				</div>
			</div>

			<div class="row">

				<div class="box box-crey nav-tabs-custom">

					<ul class="nav nav-tabs pull-right">
						<li class="pull-left header box-title"><i class="fa fa-edit"></i> Manage Page </li>
						<li class=""><a href="#tab_versions" data-toggle="tab" aria-expanded="false">Versions</a></li>
						<li class=""><a href="#tab_content" data-toggle="tab" aria-expanded="false">Content</a></li>
						<li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Details</a></li>
					</ul>

					<!-- /.box-header -->
					<div class="box-body">

						<div class="tab-content">
						<div class="tab-pane active" id="tab_details">
							<div class="row">
							<div class="col col-md-4" style="padding-left:30px;">
								<h2 class="underline">Basic Information</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="pageName">Page Name:</label>
								<?php echo @form_input('pageName',$data['pageName'], 'id="pageName" class="form-control input-style"'); ?>
								<div class="help-tip">
								    <p>This is the name of the page,<b>for your information only</b>.</p>
								</div>
								<br class="clear" />

								<label for="parentID">Parent:</label>
								<?php
									$options = array();
									$options[0] = 'Top Level';
									if ($parents):
										foreach ($parents as $parent):
											if ($parent['pageID'] != @$data['pageID']):
												$options[$parent['pageID']] = $parent['pageName'];
												if (isset($children[$parent['pageID']]) && $children[$parent['pageID']]):
													foreach ($children[$parent['pageID']] as $child):
														$options[$child['pageID']] = '-- '.$child['pageName'];
													endforeach;
												endif;
											endif;
										endforeach;
									endif;
									echo @form_dropdown('parentID',$options,$data['parentID'],'id="parentID" class="form-control input-style"');
								?>
								<div class="help-tip">
								    <p>You can optionally nest this page under other pages.</p>
								</div>
								<br class="clear" />

								<label for="uri">Path:</label>
								<?php echo @form_input('uri',$data['uri'], 'id="uri" class="form-control input-style"'); ?>
								<div class="help-tip">
								    <p>Enter the web path this page can be found at, e.g. `about-us` (no spaces)</p>
								</div>
								<br class="clear" />

								<label for="templateID">Template:</label>
								<?php
								if ($templates):
									$options = array();
									foreach ($templates as $template):
										$options[$template['templateID']] = $template['templateName'];
									endforeach;

									echo @form_dropdown('templateID',$options,$data['templateID'],'id="templateID" class="form-control input-style"');
								endif;
								?>
								<div class="help-tip">
								    <p>Templates control the layout of your page.</p>
								</div>
								<br class="clear" />

								<label for="redirect">Redirect Path:</label>
								<?php echo @form_input('redirect',set_value('redirect', $data['redirect']), 'id="redirect" class="form-control input-style"'); ?>
								<div class="help-tip">
								    <p>You can optionally use this page as a redirect to another page.</p>
								</div>
								<br class="clear" /><br />

								</div> <!-- End indent -->

							</div>

							<div class="col col-md-4" style="padding-left:30px;">

								<h2 class="underline">Meta Data</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="title">Page Title:</label>
								<?php echo @form_input('title',set_value('title', $data['title']), 'id="title" class="form-control input-style"'); ?>
								<div class="help-tip">
									<p>This will display in the title bar of browsers.</p>
								</div>
								<br class="clear" />

								<label for="description">Meta Description:</label>
								<?php echo @form_input('description',set_value('description', $data['description']), 'id="description" class="form-control input-style"'); ?>
								<div class="help-tip">
									<p>Description of page for search engines.</p>
								</div>
								<br class="clear" />

								<label for="keywords">Meta Keywords:</label>
								<?php echo @form_input('keywords',set_value('keywords', $data['keywords']), 'id="keywords" class="form-control input-style"'); ?>
								<div class="help-tip">
									<p>Meta tags for search engines.</p>
								</div>
								<br class="clear" /><br />

								</div> <!-- End indent -->

							</div>

							<div class="col col-md-4" style="padding-left:30px;">

								<h2 class="underline">Visibility and Access</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="navigation">Show in Navigation:</label>
								<?php
									$values = array(
										1 => 'Yes',
										0 => 'No (hidden page)',
									);
									echo @form_dropdown('navigation',$values,$data['navigation'], 'id="navigation" class="form-control input-style"');
								?>
								<div class="help-tip">
									<p>By default your page will appear on the navigation menu.</p>
								</div>
								<br class="clear" />

								<label for="active">Publish Status:</label>
								<?php
									$values = array(
										0 => 'Draft (visible only to administrators)',
										1 => 'Publish',
									);
									echo @form_dropdown('active',$values,$data['active'], 'id="active" class="form-control input-style"');
								?>
								<div class="help-tip">
									<p>Remember to set this to 'Publish' if you want to show the page.</p>
								</div>
								<br class="clear" />

								<label for="groupID">Edit Group:</label>
								<?php
									$values = array(
										0 => 'Administrators only',
									);
									if ($groups)
									{
										foreach($groups as $group)
										{
											$values[$group['groupID']] = $group['groupName'];
										}
									}
									echo @form_dropdown('groupID',$values,$data['groupID'], 'id="groupID" class="form-control input-style"');
								?>
								<div class="help-tip">
									<p>Who is able to edit this page?</p>
								</div>
								<br class="clear" /><br />

								</div> <!-- End indent -->

							</div>

							</div> <!-- END ROW -->

						</div>
						<!-- /.tab-pane -->
							<div class="tab-pane" id="tab_content">
								<iframe name="preview" id="preview" src="about:blank" frameborder="0" marginheight="0" marginwidth="0"></iframe>
							</div> <!-- Content -->

						<div class="tab-pane" id="tab_versions">

							<?php if ($versions): ?>
							<div class="col col-md-4" style="padding-left:30px;">
								<h2 class="underline">Published Versions</h2>

								<ul>
								<?php foreach($versions as $version): ?>
									<li>
										<?php if ($data['versionID'] == $version['versionID']): ?>
											<strong><?php echo dateFmt($version['dateCreated'], '', '', TRUE).(($user = $this->core->lookup_user($version['userID'], TRUE)) ? ' <em>(by '.$user.')</em>' : ''); ?></strong>
										<?php else: ?>
											<?php echo dateFmt($version['dateCreated'], '', '', TRUE).(($user = $this->core->lookup_user($version['userID'], TRUE)) ? ' <em>(by '.$user.')</em>' : ''); ?> - <?php echo anchor('/admin/pages/revert_version/'.$data['pageID'].'/'.$version['versionID'], 'Revert', 'onclick="return confirm(\'You will lose unsaved changes. Continue?\');"'); ?>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
								</ul>

								<br />
							</div>
							<?php endif; ?>

							<?php if ($drafts): ?>
							<div class="col col-md-4" style="padding-left:30px;">
								<h2 class="underline">Drafts</h2>

								<ul>
								<?php foreach($drafts as $version): ?>
									<li>
										<?php if ($data['draftID'] == $version['versionID']): ?>
											<strong><?php echo dateFmt($version['dateCreated'], '', '', TRUE).(($user = $this->core->lookup_user($version['userID'], TRUE)) ? ' <em>(by '.$user.')</em>' : ''); ?></strong>
										<?php else: ?>
											<?php echo dateFmt($version['dateCreated'], '', '', TRUE).(($user = $this->core->lookup_user($version['userID'], TRUE)) ? ' <em>(by '.$user.')</em>' : ''); ?> - <?php echo anchor('/admin/pages/revert_draft/'.$data['pageID'].'/'.$version['versionID'], 'Revert', 'onclick="return confirm(\'You will lose unsaved changes. Continue?\');"'); ?>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>

						</div>
						<!-- /.tab-pane -->
						</div>
						<!-- /.tab-content -->
					</div>

				</div>
				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>

	</form>

<?php endif; ?>
