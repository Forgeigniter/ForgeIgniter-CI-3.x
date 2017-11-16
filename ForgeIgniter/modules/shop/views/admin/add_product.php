<script type="text/javascript">
function preview(el){
	$.post('<?php echo site_url('/admin/shop/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.preview').html(data);
	});
}
function previewExcerpt(el){
	$.post('<?php echo site_url('/admin/blog/preview'); ?>', { body: $(el).val() }, function(data){
		$('div.previewExcerpt').html(data);
	});
}
$(function(){
	$('div.category>span, div.category>input').hover(
		function() {
			if (!$(this).prev('input').attr('checked') && !$(this).attr('checked')){
				$(this).parent().addClass('hover');
			}
		},
		function() {
			if (!$(this).prev('input').attr('checked') && !$(this).attr('checked')){
				$(this).parent().removeClass('hover');
			}
		}
	);
	$('div.category>span').click(function(){
		if ($(this).prev('input').attr('checked')){
			$(this).prev('input').attr('checked', false);
			$(this).parent().removeClass('hover');
		} else {
			$(this).prev('input').attr('checked', true);
			$(this).parent().addClass('hover');
		}
	});
	$('a.showtab').click(function(event){
		event.preventDefault();
		var div = $(this).attr('href');
		$('div#details, div#desc, div#variations').hide();
		$(div).show();
	});
	$('ul.innernav a').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('li').removeClass('selected');
		$(this).parent().addClass('selected');
	});
	$('.addvar').click(function(event){
		event.preventDefault();
		$(this).parent().parent().siblings('div').toggle('400');
	});
	$('div#desc, div#variations').hide();

	$('input.save').click(function(){
		var requiredFields = 'input#productName, input#catalogueID';
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
		if (!success){
			$('div.tab').hide();
			$('div.tab:first').show();
		}
		return success;
	});
	$('textarea#body').focus(function(){
		$('.previewbutton').show();
	});
	$('textarea#body').blur(function(){
		preview(this);
	});
	preview($('textarea#body'));

	//Preview Button
	$('textarea#excerpt').focus(function(){
		$('.previewExcerptbutton').show();
	});

	$('textarea#excerpt').blur(function(){
		previewExcerpt(this);
	});
	previewExcerpt($('textarea#excerpt'));
});
</script>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
	Shop :
	<small>Add Product</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
	<li class="active">Add Product</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid extra-padding">

	<section class="content">

		<?php if ($errors = validation_errors()): ?>
		<div class="callout callout-danger">
			<h4>Warning!</h4>
			<?php echo $errors; ?>
		</div>
		<?php endif; ?>

		<?php if (isset($message)): ?>
		<div class="callout callout-info">
			<h4>Notice</h4>
			<?php echo $message; ?>
		</div>
		<?php endif; ?>

		<div class="row">
			<div class="pull-left">
				<a href="<?= site_url('/admin/shop/products');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Products</a>
			</div>
			<div class="col-md-6 pull-right">
				<input type="submit" value="Add Product" name="save" id="save" class="btn btn-green margin-bottom save" />
			</div>
		</div>

		<div class="row">

			<div class="box box-crey nav-tabs-custom">

				<ul class="nav nav-tabs pull-right">
					<li class="pull-left header box-title"><i class="fa fa-shopping-cart"></i> Add Product </li>
					<li class=""><a href="#tab_options" data-toggle="tab" aria-expanded="false">Options &amp; Variations</a></li>
					<li class=""><a href="#tab_description" data-toggle="tab" aria-expanded="false">Description</a></li>
					<li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Details</a></li>
				</ul>

				<!-- /.box-header -->
				<div class="box-body">

					<div class="tab-content">
						<div class="tab-pane active" id="tab_details">
							<div class="row">

								<div class="col col-md-4" style="padding-left:30px;">
									<h3 class="underline">Basic Product Details</h3>

									<div style="padding-left:10px;"> <!-- Indent Content -->

										<label for="productName">Product name:</label>
										<?php echo @form_input('productName',set_value('productName', $data['productName']), 'id="productName" class="form-control input-style"'); ?>
										<br class="clear" />

										<label for="catalogueID">Catalogue ID:</label>
										<?php echo @form_input('catalogueID',set_value('catalogueID', $data['catalogueID']), 'id="catalogueID" class="form-control input-style"'); ?>
										<div class="help-tip">
											<p>This is for your own catalogue reference and stock keeping.</p>
										</div>
										<br class="clear" />

										<label for="subtitle">Sub-title / Author:</label>
										<?php echo @form_input('subtitle',set_value('subtitle', $data['subtitle']), 'id="subtitle" class="form-control input-style"'); ?>
										<br class="clear" />

										<label for="tags">Tags: <br /></label>
										<?php echo @form_input('tags', set_value('tags', $data['tags']), 'id="tags" class="form-control input-style"'); ?>
										<div class="help-tip">
											<p>Separate tags with a comma (e.g. &ldquo;places, hobbies, favourite work&rdquo;)</p>
										</div>
										<br class="clear" />

										<label for="price">Price:</label>
										<span class="price"><strong><?php echo currency_symbol(); ?></strong></span>
										<?php echo @form_input('price',number_format(set_value('price', $data['price']),2,'.',''), 'id="price" class="form-control input-style"'); ?>
										<br class="clear" />

										<label for="image">Image:</label>
										<div class="uploadfile">
											<?php if (isset($imagePath)):?>
												<img src="<?php echo $imagePath; ?>" alt="Product image" />
											<?php endif; ?>
											<?php echo @form_upload('image',$this->validation->image, 'size="16" id="image"'); ?>
										</div>
										<br class="clear" />

										<label for="category">Category: <small>[<a href="<?php echo site_url('/admin/shop/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
										<div class="categories">
											<?php if ($categories): ?>
											<?php foreach($categories as $category): ?>
												<div class="category">
													<?php echo @form_checkbox('catsArray['.$category['catID'].']', $category['catName']); ?><span><?php echo ($category['parentID']) ? '<small>'.$category['parentName'].' &gt;</small> '.$category['catName'] : $category['catName']; ?></span>
												</div>
											<?php endforeach; ?>
											<?php else: ?>
												<div class="category">
													<strong>Warning:</strong> It is strongly recommended that you use categories or this may not appear properly. <a href="<?php echo site_url('/admin/shop/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')"><strong>Please update your categories here</strong></a>.
												</div>
											<?php endif; ?>
										</div>
										<br class="clear" /><br />

									</div> <!-- End indent -->

								</div>

								<div class="col col-md-4" style="padding-left:30px;">

									<h3 class="underline">Availability</h3>

									<div style="padding-left:10px;"> <!-- Indent Content -->

										<label for="status">Status:</label>
										<?php
											$values = array(
												'S' => 'In stock',
												'O' => 'Out of stock',
												'P' => 'Pre-order'
											);
											echo @form_dropdown('status',$values,set_value('status', $data['status']), 'id="status" class="form-control input-style"');
										?>
										<br class="clear" /><br/>

										<?php if ($this->site->config['shopStockControl']): ?>
											<label for="stock">Stock:</label>
											<?php echo @form_input('stock', set_value('stock', $data['stock']), 'id="stock" class="form-control input-style"'); ?>
											<br class="clear" />
										<?php endif; ?>

										<label for="featured">Featured?</label>
										<?php
											$values = array(
												'N' => 'No',
												'Y' => 'Yes',
											);
											echo @form_dropdown('featured',$values,set_value('featured', $data['featured']), 'id="featured" class="form-control input-style"');
										?>
										<br class="clear" /><br/>

										<label for="published">Visible:</label>
										<?php
											$values = array(
												1 => 'Yes',
												0 => 'No (hide product)',
											);
											echo @form_dropdown('published',$values,set_value('published', $data['published']), 'id="published" class="form-control input-style"');
										?>
										<br class="clear" />

									</div> <!-- End indent -->

								</div>

							</div> <!-- END ROW -->
						</div>

						<div class="tab-pane" id="tab_description">

							<div class="col col-md-12" style="padding-left:30px;">

								<h3 class="underline">Product Description &amp; Excerpt</h3>

								<div style="padding-left:10px;"> <!-- Indent Content -->

									<label for="excerpt">Introduction <i>(Excerpt)</i>:</label>
									<?php
										$options = [
											'name'        => 'excerpt',
											'id'          => 'excerpt',
											'value'       => @set_value('excerpt', $data['excerpt']),
											'rows'        => '5',
											'cols'        => '10',
											'style'       => 'width:50%; margin-right:10px;',
											'class'       => 'form-control formelement code short'
										];
										echo form_textarea($options);
									?>
									<div class="previewExcerpt"></div>
									<br class="clear" />
									<span class="tip nolabel">The excerpt is a brief description of your product which is used in some templates.</span>
									<br class="clear" />

									<hr>

									<div class="buttons">
										<a href="#" class="boldbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_bold.png" alt="Bold" title="Bold" /></a>
										<a href="#" class="italicbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_italic.png" alt="Italic" title="Italic" /></a>
										<a href="#" class="h1button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h1.png" alt="Heading 1" title="Heading 1"/></a>
										<a href="#" class="h2button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h2.png" alt="Heading 2" title="Heading 2" /></a>
										<a href="#" class="h3button"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_h3.png" alt="Heading 3" title="Heading 3" /></a>
										<a href="#" class="urlbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_url.png" alt="Insert Link" title="Insert Link" /></a>
										<a href="<?php echo site_url('/admin/images/browser'); ?>" class="halogycms_imagebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_image.png" alt="Insert Image" title="Insert Image" /></a>
										<a href="<?php echo site_url('/admin/files/browser'); ?>" class="halogycms_filebutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_file.png" alt="Insert File" title="Insert File" /></a>
										<a href="#" class="previewbutton"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_save.png" alt="Preview" title="Preview" /></a>
									</div>
									<label for="body">Body <i>(Content)</i>:</label>
									<?php
										$options = [
												'name'        => 'body',
												'id'          => 'body',
												'value'       => @set_value('description', $data['description']),
												'rows'        => '30',
												'cols'        => '10',
												'style'       => 'width:50%; margin-right:10px;',
												'class'       => 'form-control formelement code half'
										];
										echo form_textarea($options); ?>
									<div class="preview"></div>

									<br class="clear" /><br />

								</div> <!-- End indent -->

							</div>

						</div> <!-- Content -->

						<div class="tab-pane" id="tab_options">


							<div class="col col-md-4" style="padding-left:30px;">

								<h3 class="underline">Options</h3>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="freePostage">Free Shipping?</label>
								<?php
									$values = array(
										0 => 'No',
										1 => 'Yes',
									);
									echo @form_dropdown('freePostage',$values,set_value('freePostage', $data['freePostage']), 'id="freePostage" class="form-control input-style"');
								?>
								<br class="clear" /><br/>

								<label for="files">File:</label>
								<?php
									$options = NULL;
									$options[0] = 'This product is not a file';
									if ($files):
										foreach ($files as $file):
											$ext = @explode('.', $file['filename']);
											$options[$file['fileID']] = $file['fileRef'].' ('.strtoupper($ext[1]).')';
										endforeach;
									endif;
									echo @form_dropdown('fileID',$options,set_value('fileID', $data['fileID']),'id="files" class="form-control input-style"');
								?>
								<div class="help-tip">
									<p>You can make this product a downloadable file (e.g. a premium MP3 or document).</p>
								</div>
								<br class="clear" /><br/>

								<label for="bands">Shipping Band:</label>
								<?php
									$options = NULL;
									$options[0] = 'No product is not restricted';
									if ($bands):
										foreach ($bands as $band):
											$options[$band['bandID']] = $band['bandName'];
										endforeach;
									endif;
									echo @form_dropdown('bandID', $options, set_value('bandID', $data['bandID']),'id="bands" class="form-control input-style"');
								?>
								<div class="help-tip">
									<p>You can restrict this product to a shipping band if necessary.</p>
								</div>
								<br class="clear" /><br />

								</div> <!-- END Indent Content -->

							</div>


							<div class="col col-md-4" style="padding-left:30px;">

								<h3 class="underline">Variations</h3>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<div id="variation1">
									<div class="addvars">
										<p><a href="#" class="addvar"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_plus.gif" alt="Delete" class="padded" /> Add <?php echo $this->site->config['shopVariation1']; ?> Variations</a></p>
										<br class="clear" />
									</div>
									<div class="showvars" style="display: none;">

										<?php foreach (range(1,5) as $x): $i = $x-1; ?>

										<label for="variation1-<?php echo $x; ?>"><?php echo base_url() . $this->site->config['shopVariation1']; ?> <?php echo $x; ?>:</label>
										<?php echo @form_input('variation1-'.$x,set_value('variation1-'.$x, $variation1[$i]['variation']), 'id="variation1-'.$x.'" class="form-control input-style"'); ?><span class="price"><strong><?php echo currency_symbol(); ?></strong></span><?php echo @form_input('variation1_price-'.$x,number_format(set_value('variation1_price-'.$x, $variation1[$i]['price']),2), 'class="formelement small"'); ?>
										<br class="clear" />

										<?php endforeach; ?>

									</div>
								</div>

								<div id="variation2">
									<div class="addvars">
										<p><a href="#" class="addvar"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_plus.gif" alt="Delete" class="padded" /> Add <?php echo $this->site->config['shopVariation2']; ?> Variations</a></p>
										<br class="clear" />
									</div>
									<div class="showvars" style="display: none;">

										<?php foreach (range(1,5) as $x): $i = $x-1; ?>

										<label for="variation2-<?php echo $x; ?>"><?php echo base_url() . $this->site->config['shopVariation2']; ?> <?php echo $x; ?>:</label>
										<?php echo @form_input('variation2-'.$x,set_value('variation2-'.$x, $variation2[$i]['variation']), 'id="variation2-'.$x.'" class="form-control input-style"'); ?><span class="price"><strong><?php echo currency_symbol(); ?></strong></span><?php echo @form_input('variation2_price-'.$x,number_format(set_value('variation2_price-'.$x, $variation2[$i]['price']),2), 'class="formelement small"'); ?>
										<br class="clear" />

										<?php endforeach; ?>

									</div>
								</div>

								<div id="variation3">
									<div class="addvars">
										<p><a href="#" class="addvar"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_plus.gif" alt="Delete" class="padded" /> Add <?php echo $this->site->config['shopVariation3']; ?> Variations</a></p>
										<br class="clear" />
									</div>
									<div class="showvars" style="display: none;">

										<?php foreach (range(1,5) as $x): $i = $x-1; ?>

										<label for="variation3-<?php echo $x; ?>"><?php echo base_url() . $this->site->config['shopVariation3']; ?> <?php echo $x; ?>:</label>
										<?php echo @form_input('variation3-'.$x,set_value('variation3-'.$x, $variation3[$i]['variation']), 'id="variation3-'.$x.'" class="formelement"'); ?><span class="price"><strong><?php echo currency_symbol(); ?></strong></span><?php echo @form_input('variation3_price-'.$x,number_format(set_value('variation3_price-'.$x, $variation3[$i]['price']),2), 'class="formelement small"'); ?>
										<br class="clear" />

										<?php endforeach; ?>

									</div>
								</div>

								</div> <!-- END Indent Content -->

							</div>

					<!-- /.tab-pane -->
					</div>
					<!-- /.tab-content -->
				</div>

			</div>
		</div> <!-- End Row -->

	</section>

</form>
