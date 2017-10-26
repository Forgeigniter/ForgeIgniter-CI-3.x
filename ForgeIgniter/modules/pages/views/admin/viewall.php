<!-- Pages - View All -->
<script type="text/javascript">
function setOrder(){
	$.post('<?php echo site_url('/admin/pages/order/page'); ?>',$(this).sortable('serialize'),function(data){ });
};
function initOrder(el){
	$('ol.order').height($('ol.order').height());
	$(el).sortable({
		axis: 'y',
	    revert: false,
	    delay: '80',
	    opacity: '0.5',
	    update: setOrder
	});
};
$(function(){
	$('#collapse').change(function(){
		if ($(this).val() == 'collapse'){
			$('.subpage').slideUp();
		} else if ($(this).val() == 'hidden'){
			$('.hiddenpage').slideUp();
		} else if ($(this).val() == 'drafts'){
			$('.draft').slideUp();
		} else {
			$('.hiddenpage, .subpage, .draft').slideDown();
		}
	});
	$('a.showform').on('click', function(event){showForm(this,event);});
	$('input#cancel').on('click', function(event){hideForm(this,event);});
	initOrder('ol.order, ol.order ol');
});
</script>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Pages :
        <small>Manage All Pages</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-newspaper-o"></i> Pages</a></li>
        <li class="active">Manage All</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

		<section class="content">
			<div class="row">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-newspaper-o"></i>
					<h3 class="box-title">Pages</h3>

					<div class="box-tools">
						<select id="collapse" class="form-control">
							<option value="all">Show all</option>
							<option value="hidden">Hide hidden pages</option>
							<option value="collapse">Hide sub-pages</option>
							<option value="drafts">Hide drafts</option>
						</select>
						<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
							<a href="<?= site_url('/admin/pages/add'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Page</a>
						<?php endif; ?>
					</div>

					</div>
					<!-- /.box-header -->
					<div class="box-body">

					<?php if ($parents): ?>

						<ol class="order">
							<?php foreach ($parents as $page): ?>
							<li id="pages-<?php echo $page['pageID']; ?>" class="<?php echo (!$page['navigation']) ? 'hiddenpage' : ''; ?><?php echo (!$page['active']) ? ' draft' : ''; ?><?php echo (@$children[$page['pageID']]) ? ' haschildren' : ''; ?><?php echo ($page['active'] && $page['datePublished'] > 0 && ($page['newBlocks'] > 0 || $page['newVersions'] > 0)) ? ' draft' : ''; ?>">

								<div class="col1">
									<strong><?php echo (in_array('pages_edit', $this->permission->permissions)) ? anchor('/admin/pages/edit/'.$page['pageID'], $page['pageName'], 'class="pagelink"') : $page['pageName']; ?></strong><br />
									<small>/<?php echo $page['uri']; ?></small>
								</div>
								<div class="col2">
									<?php if ($page['active']): ?>
										<span style="color:green">
											<?php if ($page['redirect']): ?>
												<strong>Redirect</strong> (<?php echo $page['redirect']; ?>)
											<?php else: ?>
												<?php if ($page['active'] && $page['datePublished'] > 0 && ($page['newBlocks'] > 0 || $page['newVersions'] > 0)): ?>
													<strong>Published (but modified)</strong>
												<?php else: ?>
													<strong>Published</strong>
												<?php endif; ?>
												<?php echo (!$page['navigation']) ? ' (hidden)' : ''; ?>
											<?php endif; ?>
										</span>
									<?php else: ?>
										Draft
										<?php echo (!$page['navigation']) ? ' (hidden)' : ''; ?>
									<?php endif; ?>
									<br />
									<?php if ($page['active'] && (!$page['newBlocks'] && !$page['newVersions'])): ?>
										<small>Published: <strong><?php echo dateFmt($page['datePublished'], '', '', TRUE); ?></strong>
									<?php else: ?>
										<small>Modified: <strong><?php echo dateFmt($page['dateModified'], '', '', TRUE); ?></strong>
									<?php endif; ?>
									<em>by <?php echo $this->core->lookup_user($page['userID'], TRUE); ?></em></small>
								</div>
								<div class="buttons">
									<?php echo anchor($page['uri'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_view.png" alt="View" title="View" />'); ?>
									<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
										<?php echo anchor('/admin/pages/edit/'.$page['pageID'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_edit.png" alt="Edit" title="Edit" />'); ?>
									<?php endif; ?>
									<?php if (in_array('pages_delete', $this->permission->permissions)): ?>
										<?php echo anchor('/admin/pages/delete/'.$page['pageID'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_delete.png" alt="Delete" title="Delete" />', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
									<?php endif; ?>
								</div>
								<div class="clear"></div>

								<?php if (isset($children[$page['pageID']]) && $children[$page['pageID']]): ?>

									<ol class="subpage">
										<?php foreach ($children[$page['pageID']] as $child): ?>
										<li id="pages-<?php echo $child['pageID']; ?>" class="<?php echo (!$child['navigation']) ? 'hiddenpage' : ''; ?><?php echo (!$child['active']) ? ' draft' : ''; ?><?php echo ($child['active'] && $child['datePublished'] > 0 && ($child['newBlocks'] > 0 || $child['newVersions'] > 0)) ? ' draft' : ''; ?>">
											<div class="col1">
												<span class="padded"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/arrow_child.gif" alt="Arrow" /></span> <strong><?php echo (in_array('pages_edit', $this->permission->permissions)) ? anchor('/admin/pages/edit/'.$child['pageID'], $child['pageName'], 'class="pagelink"') : $child['pageName']; ?></strong><br />
												<small>/<?php echo $child['uri']; ?></small>
											</div>
											<div class="col2">
												<?php if ($child['active']): ?>
													<span style="color:green">
														<?php if ($child['redirect']): ?>
															<strong>Redirect</strong> (<?php echo $child['redirect']; ?>)
														<?php else: ?>
														<?php if ($child['active'] && $child['datePublished'] > 0 && ($child['newBlocks'] > 0 || $child['newVersions'] > 0)): ?>
															<strong>Published (but modified)</strong>
														<?php else: ?>
															<strong>Published</strong>
														<?php endif; ?>
															<?php echo (!$child['navigation']) ? ' (hidden)' : ''; ?>
														<?php endif; ?>
													</span>
												<?php else: ?>
													Draft
													<?php echo (!$child['navigation']) ? ' (hidden)' : ''; ?>
												<?php endif; ?>
												<br />
												<?php if ($child['active'] && (!$child['newBlocks'] && !$child['newVersions'])): ?>
													<small>Published: <strong><?php echo dateFmt($child['datePublished'], '', '', TRUE); ?></strong>
												<?php else: ?>
													<small>Modified: <strong><?php echo dateFmt($child['dateModified'], '', '', TRUE); ?></strong>
												<?php endif; ?>
												<em>by <?php echo $this->core->lookup_user($child['userID'], TRUE); ?></em></small>
											</div>
											<div class="buttons">
												<?php echo anchor($child['uri'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_view.png" alt="View" title="View" />'); ?>
												<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
													<?php echo anchor('/admin/pages/edit/'.$child['pageID'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_edit.png" alt="Edit" title="Edit" />'); ?>
												<?php endif; ?>
												<?php if (in_array('pages_delete', $this->permission->permissions)): ?>
													<?php echo anchor('/admin/pages/delete/'.$child['pageID'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_delete.png" alt="Delete" title="Delete" />', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
												<?php endif; ?>
											</div>
											<div class="clear"></div>

											<?php if (isset($subchildren[$child['pageID']]) && $subchildren[$child['pageID']]): ?>

												<ol class="subpage">
													<?php foreach ($subchildren[$child['pageID']] as $subchild): ?>
													<li id="pages-<?php echo $subchild['pageID']; ?>" class="<?php echo (!$subchild['navigation']) ? 'hiddenpage' : ''; ?><?php echo (!$subchild['active']) ? ' draft' : ''; ?><?php echo ($subchild['active'] && $subchild['datePublished'] > 0 && ($subchild['newBlocks'] > 0 || $subchild['newVersions'] > 0)) ? ' draft' : ''; ?>">
														<div class="col1">
															<span class="padded"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/arrow_subchild.gif" alt="Arrow" /></span> <strong><?php echo (in_array('pages_edit', $this->permission->permissions)) ? anchor('/admin/pages/edit/'.$subchild['pageID'], $subchild['pageName'], 'class="pagelink"') : $subchild['pageName']; ?></strong><br />
														<small>/<?php echo $subchild['uri']; ?></small>
														</div>
														<div class="col2">
															<?php if ($subchild['active']): ?>
																<span style="color:green">
																	<?php if ($subchild['redirect']): ?>
																		<strong>Redirect</strong> (<?php echo $subchild['redirect']; ?>)
																	<?php else: ?>
																	<?php if ($subchild['active'] && $subchild['datePublished'] > 0 && ($subchild['newBlocks'] > 0 || $subchild['newVersions'] > 0)): ?>
																		<strong>Published (but modified)</strong>
																	<?php else: ?>
																		<strong>Published</strong>
																	<?php endif; ?>
																		<?php echo (!$subchild['navigation']) ? ' (hidden)' : ''; ?>
																	<?php endif; ?>
																</span>
															<?php else: ?>
																Draft
																<?php echo (!$subchild['navigation']) ? ' (hidden)' : ''; ?>
															<?php endif; ?>
															<br />
															<?php if ($subchild['active'] && (!$subchild['newBlocks'] && !$subchild['newVersions'])): ?>
																<small>Published: <strong><?php echo dateFmt($subchild['datePublished'], '', '', TRUE); ?></strong>
															<?php else: ?>
																<small>Modified: <strong><?php echo dateFmt($subchild['dateModified'], '', '', TRUE); ?></strong>
															<?php endif; ?>
															<em>by <?php echo $this->core->lookup_user($subchild['userID'], TRUE); ?></em></small>
														</div>
														<div class="buttons">
															<?php echo anchor($subchild['uri'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_view.png" alt="View" title="View" />'); ?>
															<?php if (in_array('pages_edit', $this->permission->permissions)): ?>
																<?php echo anchor('/admin/pages/edit/'.$subchild['pageID'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_edit.png" alt="Edit" title="Edit" />'); ?>
															<?php endif; ?>
															<?php if (in_array('pages_delete', $this->permission->permissions)): ?>
																<?php echo anchor('/admin/pages/delete/'.$subchild['pageID'], '<img src="'.base_url().$this->config->item('staticPath').'/images/btn_delete.png" alt="Delete" title="Delete" />', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
															<?php endif; ?>
														</div>
														<div class="clear"></div>
													</li>
													<?php endforeach; ?>
												</ol>

											<?php endif; ?>


										</li>
										<?php endforeach; ?>
									</ol>

								<?php endif; ?>

							</li>
							<?php endforeach; ?>
						</ol>

						<br />

					<?php else: ?>

					<p class="clear">No pages were found.</p>

					<?php endif; ?>

					</div>
				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>

<div class="clear"></div>
