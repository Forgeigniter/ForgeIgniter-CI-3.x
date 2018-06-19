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
	if ($('input#variation1-1').val()){
		$('div#variation1').children('div.showvars').show();
	}
	if ($('input#variation2-1').val()){
		$('div#variation2').children('div.showvars').show();
	}
	if ($('input#variation3-1').val()){
		$('div#variation3').children('div.showvars').show();
	}
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
	$('a.lightbox').lightBox({imageLoading:'<?php echo base_url() . $this->config->item('staticPath'); ?>/images/loading.gif',imageBtnClose: '<?php echo base_url() . $this->config->item('staticPath'); ?>/images/lightbox_close.gif',imageBtnNext:'<?php echo base_url() . $this->config->item('staticPath'); ?>/image/lightbox_btn_next.gif',imageBtnPrev:'<?php echo base_url() . $this->config->item('staticPath'); ?>/image/lightbox_btn_prev.gif'});
	preview($('textarea#body'));

	//Excerpt Preview Button
	$('textarea#excerpt').focus(function(){
		$('.previewExcerptbutton').show();
	});

	$('textarea#excerpt').blur(function(){
		previewExcerpt(this);
	});
	previewExcerpt($('textarea#excerpt'));
  $('a.lightbox').lightBox({imageLoading:'<?php echo base_url($this->config->item('staticPath')); ?>/images/loading.gif',imageBtnClose: '<?php echo base_url($this->config->item('staticPath')); ?>/images/lightbox_close.gif',imageBtnNext:'<?php echo base_url($this->config->item('staticPath')); ?>/image/lightbox_btn_next.gif',imageBtnPrev:'<?php echo base_url($this->config->item('staticPath')); ?>/image/lightbox_btn_prev.gif'});
});
</script>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

<h1 class="headingleft">Edit Product <small>(<a href="<?php echo site_url('/admin/shop/products'); ?>">Back to Products</a>)</small></h1>

<div class="headingright">
	<input type="submit" name="view" value="View Product" class="button blue save" />
	<input type="submit" value="Save Changes" class="button save" />
</div>

<div class="clear"></div>

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

<ul class="innernav clear">
	<li class="selected"><a href="#details" class="showtab">Details</a></li>
	<li><a href="#desc" class="showtab">Description</a></li>
	<li><a href="#variations" class="showtab">Options &amp; Variations</a></li>
</ul>

<br class="clear" />

<div id="details" class="tab">

	<h2 class="underline">Product Details</h2>

	<label for="productName">Product name:</label>
	<?php echo @form_input('productName',set_value('productName', $data['productName']), 'id="productName" class="formelement"'); ?>
	<br class="clear" />

	<label for="catalogueID">Catalogue ID:</label>
	<?php echo @form_input('catalogueID',set_value('catalogueID', $data['catalogueID']), 'id="catalogueID" class="formelement"'); ?>
	<span class="tip">This is for your own catalogue reference and stock keeping.</span>
	<br class="clear" />

	<label for="subtitle">Sub-title / Author:</label>
	<?php echo @form_input('subtitle',set_value('subtitle', $data['subtitle']), 'id="subtitle" class="formelement"'); ?>
	<br class="clear" />

	<label for="tags">Tags: <br /></label>
	<?php echo @form_input('tags', set_value('tags', $data['tags']), 'id="tags" class="formelement"'); ?>
	<span class="tip">Separate tags with a comma (e.g. &ldquo;places, hobbies, favourite work&rdquo;)</span>
	<br class="clear" />

	<label for="price">Price:</label>
	<span class="price"><strong><?php echo currency_symbol(); ?></strong></span>
	<?php echo @form_input('price',number_format(set_value('price', $data['price']),2,'.',''), 'id="price" class="formelement small"'); ?>
	<br class="clear" />

	<label for="image">Image:</label>
	<div class="uploadfile">
		<?php if ($imagePath):?>
			<a href="<?php echo $imageThumbPath; ?>" title="<?php echo set_value('productName', $data['productName']); ?>" class="lightbox"><img src="<?php echo site_url($imagePath); ?>" alt="Product image" class="pic" /></a>
		<?php endif; ?>
		<?php echo @form_upload('image',set_value('image', $data['image']), 'size="16" id="image"'); ?>
	</div>
	<br class="clear" />

	<label for="category">Category: <small>[<a href="<?php echo site_url('/admin/shop/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
	<div class="categories">
		<?php if ($categories): ?>
		<?php foreach($categories as $category): ?>
			<div class="category<?php echo (isset($data['categories'][$category['catID']])) ? ' hover' : ''; ?>">
				<?php echo @form_checkbox('catsArray['.$category['catID'].']', $category['catName'], (isset($data['categories'][$category['catID']])) ? 1 : ''); ?><span><?php echo ($category['parentID']) ? '<small>'.$category['parentName'].' &gt;</small> '.$category['catName'] : $category['catName']; ?></span>
			</div>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="category">
				<strong>Warning:</strong> It is strongly recommended that you use categories or this may not appear properly. <a href="<?php echo site_url('/admin/shop/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')"><strong>Please update your categories here</strong></a>.
			</div>
		<?php endif; ?>
	</div>
	<br class="clear" /><br />

	<h2 class="underline">Availability</h2>

	<label for="status">Status:</label>
	<?php
		$values = array(
			'S' => 'In stock',
			'O' => 'Out of stock',
			'P' => 'Pre-order'
		);
		echo @form_dropdown('status',$values,set_value('status', $data['status']), 'id="status" class="formelement"');
	?>
	<br class="clear" />

	<?php if ($this->site->config['shopStockControl']): ?>
		<label for="stock">Stock:</label>
		<?php echo @form_input('stock',set_value('stock', $data['stock']), 'id="stock" class="formelement small"'); ?>
		<br class="clear" />
	<?php endif; ?>

	<label for="featured">Featured?</label>
	<?php
		$values = array(
			'N' => 'No',
			'Y' => 'Yes',
		);
		echo @form_dropdown('featured',$values,set_value('featured', $data['featured']), 'id="featured" class="formelement"');
	?>
	<span class="tip">Featured products will show on the shop front page.</span>
	<br class="clear" />

	<label for="published">Visible:</label>
	<?php
		$values = array(
			1 => 'Yes',
			0 => 'No (hide product)',
		);
		echo @form_dropdown('published',$values,set_value('published', $data['published']), 'id="published"');
	?>
	<br class="clear" />

</div>

<div id="desc" class="tab">

	<h2 class="underline">Product Description</h2>

	<label for="excerpt">Introduction <i>(Excerpt)</i>:</label>
	<span class="tip nolabel">The excerpt is a brief description of your product which is used in some templates.</span>
	<br class="clear" /><br />
	<?php
		$options = [
			'name'        => 'excerpt',
			'id'          => 'excerpt',
			'value'       => @set_value('excerpt', $data['excerpt']),
			'rows'        => '10',
			'cols'        => '10',
			'style'       => 'width:57%; margin-right:5px; height: 81px;',
			'class'       => 'formelement code half'
		];
		echo form_textarea($options);
	?>
	<div class="previewExcerpt"></div>
	<br class="clear" /><br />

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

	<label for="body">Body:</label>
	<?php echo @form_textarea('description', set_value('description', $data['description']), 'id="body" class="formelement code half"'); ?>
	<div class="preview"></div>
	<br class="clear" /><br />

</div>

<div id="variations" class="tab">

	<h2 class="underline">Options</h2>

	<label for="freePostage">Free Shipping?</label>
	<?php
		$values = array(
			0 => 'No',
			1 => 'Yes',
		);
		echo @form_dropdown('freePostage',$values,set_value('freePostage', $data['freePostage']), 'id="freePostage"');
	?>
	<br class="clear" />

	<label for="files">File:</label>
	<?php
		$options = '';
		$options[0] = 'This product is not a file';
		if ($files):
			foreach ($files as $file):
				$ext = @explode('.', $file['filename']);
				$options[$file['fileID']] = $file['fileRef'].' ('.strtoupper($ext[1]).')';
			endforeach;
		endif;
		echo @form_dropdown('fileID',$options,set_value('fileID', $data['fileID']),'id="files" class="formelement"');
	?>
	<span class="tip">You can make this product a downloadable file (e.g. a premium MP3 or document).</span>
	<br class="clear" />

	<label for="bands">Shipping Band:</label>
	<?php
		$options = '';
		$options[0] = 'No product is not restricted';
		if ($bands):
			foreach ($bands as $band):
				$options[$band['bandID']] = $band['bandName'];
			endforeach;
		endif;
		echo @form_dropdown('bandID', $options, set_value('bandID', $data['bandID']),'id="bands" class="formelement"');
	?>
	<span class="tip">You can restrict this product to a shipping band if necessary.</span>
	<br class="clear" /><br />


	<h2 class="underline">Variations</h2>

	<div id="variation1">
		<div class="addvars">
			<p><a href="#" class="addvar"><img src="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/btn_plus.gif" alt="Delete" class="padded" /> Add <?php echo $this->site->config['shopVariation1']; ?> Variations</a></p>
			<br class="clear" />
		</div>
		<div class="showvars" style="display: none;">

			<?php foreach (range(1,5) as $x): $i = $x-1; ?>

			<label for="variation1-<?php echo $x; ?>"><?php echo base_url() . $this->site->config['shopVariation1']; ?> <?php echo $x; ?>:</label>
			<?php echo @form_input('variation1-'.$x,set_value('variation1-'.$x, $variation1[$i]['variation']), 'id="variation1-'.$x.'" class="formelement"'); ?><span class="price"><strong><?php echo currency_symbol(); ?></strong></span><?php echo @form_input('variation1_price-'.$x,number_format(set_value('variation1_price-'.$x, $variation1[$i]['price']),2), 'class="formelement small"'); ?>
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
			<?php echo @form_input('variation2-'.$x,set_value('variation2-'.$x, $variation2[$i]['variation']), 'id="variation2-'.$x.'" class="formelement"'); ?><span class="price"><strong><?php echo currency_symbol(); ?></strong></span><?php echo @form_input('variation2_price-'.$x,number_format(set_value('variation2_price-'.$x, $variation2[$i]['price']),2), 'class="formelement small"'); ?>
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

			<label for="variation3-<?php echo $x; ?>"><?php echo $this->site->config['shopVariation3']; ?> <?php echo $x; ?>:</label>
			<?php echo @form_input('variation3-'.$x,set_value('variation3-'.$x, $variation3[$i]['variation']), 'id="variation3-'.$x.'" class="formelement"'); ?><span class="price"><strong><?php echo currency_symbol(); ?></strong></span><?php echo @form_input('variation3_price-'.$x,number_format(set_value('variation3_price-'.$x, $variation3[$i]['price']),2), 'class="formelement small"'); ?>
			<br class="clear" />

			<?php endforeach; ?>

		</div>
	</div>

</div>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

</form>
