(function($){$.fn.resizer=function(){var b=!($.browser.msie||$.browser.opera);function doResize(e){e=e.target||e;var a=e.value.length,ewidth=e.offsetWidth;if(a!=e.valLength||ewidth!=e.boxWidth){if(b&&(a<e.valLength||ewidth!=e.boxWidth))e.style.height="0px";var h=Math.max(e.expandMin,Math.min(e.scrollHeight,e.expandMax));e.style.overflow=(e.scrollHeight>h?"auto":"hidden");e.style.height=h+"px";e.valLength=a;e.boxWidth=ewidth}return true};this.each(function(){if(this.nodeName.toLowerCase()!="textarea")return;var p=this.className.match(/expand(\d+)\-*(\d+)*/i);this.expandMin=100;this.expandMax=(p?parseInt('0'+p[2],10):99999);doResize(this);if(!this.Initialized){this.Initialized=true;$(this).css("padding-top",0).css("padding-bottom",0);$(this).bind("keyup",doResize).bind("focus",doResize)}});return this}})(jQuery);

(function(){$.fn.insertAtCaret=function(e,f){if(!f)f='';return this.each(function(){if(document.selection){this.focus();d=document.selection.createRange();d.text=e+d.text+f;return false}else if(this.selectionStart||this.selectionStart=='0'){var a=this.selectionStart;var b=this.selectionEnd;var c=this.scrollTop;var d=this.value.substring(a,b);if(e=="\t"){d=d.replace(/\n/ig,"\n\t")}this.value=this.value.substring(0,a)+(e+d+f)+this.value.substring(b,this.value.length);this.selectionStart=a+e.length;this.selectionEnd=a+e.length;this.scrollTop=c;return false}else{this.value+=e;return false}})};})();

(function ($) {
	var cmsActive = true;
	var editactive = false;
	var activeBlock = '';
	function edit(a) {
		var b = $(a).closest('div.halogycms_edit');
		var c = $(b).children('div.halogycms_blockelement');
		var d = $(b).children('div.halogycms_editblock');
		var e = $(b).children('div.halogycms_buttons');
		var f = $(a).siblings('a').show();
		activeBlock = $(b).contents().find('textarea.code');
		var g = $(activeBlock).val();
		if ($(b).width() > 0) {
			$(b).width($(b).width());
		} else {
			$(b).width('202px');
		}
		$(activeBlock).css("margin-top", $(e).height() + 5);
		$(d).addClass('halogycms_active');
		$(b).addClass('halogycms_active');
		showadmin();
		$(a).hide();
		$(c).hide();
		$(d).show();
		$(activeBlock).focus();
	}
	function cancel(a) {
		var b = $(a).closest('div.halogycms_edit');
		var c = $(b).children('div.halogycms_blockelement');
		var d = $(b).children('div.halogycms_editblock');
		var e = $(a).siblings('a').hide();
		var f = $(a).siblings('a.halogycms_editbutton').show();
		$(b).removeClass('halogycms_active');
		$(d).removeClass('halogycms_active');
		hideadmin();
		$(a).hide();
		$(d).hide();
		$(c).show()
	}
	function save(b) {
		var c = $(b).closest('div.halogycms_edit');
		var d = $(c).children('div.halogycms_blockelement');
		var e = $(c).children('div.halogycms_editblock');
		var f = $(b).siblings('a').hide();
		var g = $(b).siblings('a.halogycms_editbutton').show();
		var h = $(c).contents().find('textarea.code').val() + '[!!ADDBLOCK!!]';		
		$(c).removeClass('halogycms_active');
		$(e).removeClass('halogycms_active');
		hideadmin();
		$(b).hide();
		$(e).hide();
		$(d).html('<p class="halogycms_loader">Loading...</p>');
		$(d).show();
		$.post(b.href, {
			body: h
		}, function (a) {
			$(d).html(a)
		})
	}
	function checkajax() {
		if (updated == 0) {
			$('.content').css('opacity', '0.5')
		} else {
			$('.content').css('opacity', '1')
		}
	}
	function autosave(f) {
		var g = $(f).attr('href');
		var h = ($('div.halogycms_edit.halogycms_active').length);
		var i = 0;
		if (h > 0) {
			$('div.halogycms_edit.halogycms_active').each(function () {
				var b = $(this).children('div.halogycms_blockelement');
				var c = $(this).children('div.halogycms_editblock');
				var d = $(c).find('textarea.code').val();
				var e = $(this).find('.halogycms_savebutton').attr('href');
				$.post(e, {
					body: d
				}, function (a) {
					$(b).html(a);
					i++;
					if (i == h) {
						window.location = g
					}
				})
			});
			return false
		} else {
			return true
		}
	}
	function preview(a) {
		if (!$(a).hasClass('preview')) {
			cancel('.halogycms_cancelbutton');
			$('#halogycms_browser').hide();
			$('.halogycms_buttons').hide();
			$('div.halogycms_edit').addClass('halogycms_preview');
			$('div#halogycms_admin').addClass('halogycms_hidden');
			$(a).text('Edit').addClass('preview')
		} else {
			$('div.halogycms_edit').removeClass('halogycms_preview');
			$('div,a').removeClass('halogycms_hover');
			$('.halogycms_buttons').show();
			$(a).text('Preview').removeClass('preview')
		}
	}
	function showadmin() {
		$('div#halogycms_admin').animate({
			'top': '-12px',
			'height': '25px'
		}, 20, '', function () {
			$(this).find('.halogycms_button').fadeIn(100)
		})
	}
	function hideadmin() {
		if (!$('.halogycms_edit').hasClass('halogycms_active')) {
			$('div#halogycms_admin').animate({
				'top': '-20px',
				'height': '18px'
			}, 20, '', function () {
				$(this).find('.halogycms_button').hide()
			})
		}
	}
	function editpic(a) {
		if ($(a).length > 0) {
			var b = $(a).offset();
			var c = $('#halogycms_editpic').attr('rel') + '/' + $(a).attr('id');
			$('#halogycms_editpic').attr('href', c).css({
				'top': b.top,
				'left': b.left
			}).show()
		} else {
			return
		}
	}
	function editshow() {
		$('#halogycms_editpic').show()
	}
	function edithide() {
		if (!editactive) {
			$('#halogycms_editpic').hide()
		}
	}
	function showpopup(a) {
		var b = $(a).attr('href');
		$('#halogycms_popup').fadeIn(300).load(b, {}, function () {
			$(this).removeClass('loading')
		})
	}
	function showimages(a) {
		var b = $(a).closest('div.halogycms_edit');
		activeBlock = $(b).contents().find('textarea.code');
		$(activeBlock).focus();
		$('#halogycms_browser').fadeIn(300).load($(a).attr('href'), {}, function () {
			$(this).removeClass('loading')
		})
	}
	function insertimage(a) {
		$(activeBlock).focus();
		$(activeBlock).insertAtCaret('{image:' + $(a).attr('title') + '}');
		hidebrowser()
	}
	function showfiles(a) {
		var b = $(a).closest('div.halogycms_edit');
		activeBlock = $(b).contents().find('textarea.code');
		$(activeBlock).focus();
		$('#halogycms_browser').fadeIn(300).load($(a).attr('href'), {}, function () {
			$(this).removeClass('loading')
		})
	}
	function insertfile(a) {
		$(activeBlock).focus();
		$(activeBlock).insertAtCaret('{file:' + $(a).attr('title') + '}');
		hidebrowser()
	}
	function hidebrowser() {
		$('#halogycms_browser, #halogycms_popup').fadeOut(300, function () {
			$(this).html('');
			$(this).addClass('loading')
		})
	}
	function toggleFolder(a) {
		$(a).parent().parent('ul').next('ul').slideToggle(200)
	}
	function formatting(a, b) {
		var c = $(a).closest('div.halogycms_edit');
		var d = $(c).contents().find('textarea.code');
		if (b == 'bold') {
			$(d).insertAtCaret('**', '**')
		}
		if (b == 'italic') {
			$(d).insertAtCaret('*', '*')
		}
		if (b == 'h1') {
			$(d).insertAtCaret('# ', ' ')
		}
		if (b == 'h2') {
			$(d).insertAtCaret('## ', ' ')
		}
		if (b == 'h3') {
			$(d).insertAtCaret('### ', ' ')
		}
		if (b == 'url') {
			var e = prompt('Please enter a web address or email you want to link to:', '');
			if (e) {
				if (e.match('@')) {
					$(d).insertAtCaret('[', '](mailto:' + e + ')')
				} else if (e.match('^www\.')) {
					$(d).insertAtCaret('[', '](http://' + e + ')')
				} else if (!e.match('^http(s)?:\/\/(www\.)?|^\/')) {
					$(d).insertAtCaret('[', '](/' + e + ')')
				} else {
					$(d).insertAtCaret('[', '](' + e + ')')
				}
			} else {
				return false
			}
		}
		$(d).focus()
	}
	$('textarea.code').keypress(function (e) {
		if (e.keyCode == 9) {
			$(this).insertAtCaret("\t");
			return false
		}
	});
	$('textarea.code').resizer();
	$('.halogycms_confirm').live('click', function () {
		return confirm('You may lose unsaved changes. Continue?')
	});
	$('a.halogycms_toggle').live('click', function () {
		preview(this);
		return false
	});
	$('a.halogycms_editbutton').live('click', function () {
		edit(this);
		return false
	});
	$('div.halogycms_edit:not(.halogycms_preview)').live('dblclick', function () {
		edit($(this).find('a.halogycms_editbutton'));
		return false
	});
	$('a.halogycms_cancelbutton').live('click', function () {
		cancel(this);
		return false
	});
	$('a.halogycms_saveall').live('click', function () {
		return autosave(this)
	});
	$('a.halogycms_imagebutton').live('click', function () {
		showimages(this);
		return false
	});
	$('.halogycms_insertimage').live('click', function () {
		insertimage(this);
		return false
	});
	$('a.halogycms_filebutton').live('click', function () {
		showfiles(this);
		return false
	});
	$('.halogycms_insertfile').live('click', function () {
		insertfile(this);
		return false
	});
	$('a.halogycms_close').live('click', function () {
		hidebrowser();
		return false
	});
	$('a.halogycms_boldbutton').live('click', function () {
		formatting(this, 'bold');
		return false
	});
	$('a.halogycms_italicbutton').live('click', function () {
		formatting(this, 'italic');
		return false
	});
	$('a.halogycms_h1button').live('click', function () {
		formatting(this, 'h1');
		return false
	});
	$('a.halogycms_h2button').live('click', function () {
		formatting(this, 'h2');
		return false
	});
	$('a.halogycms_h3button').live('click', function () {
		formatting(this, 'h3');
		return false
	});
	$('a.halogycms_urlbutton').live('click', function () {
		formatting(this, 'url');
		return false
	});
	$('a.halogycms_togglefolder').live('click', function () {
		toggleFolder(this);
		return false
	});
	$('a.halogycms_savebutton').live('click', function () {
		save(this);
		return false
	});
	$('#halogycms_editpic').live('click', function () {
		showpopup(this);
		return false
	});
	$('div#halogycms_admin').hover(function () {
		showadmin()
	}, function () {
		hideadmin()
	});
	$('img.pic').live('mouseover', function () {
		editpic(this)
	});
	$('#halogycms_editpic').live('mouseover', function () {
		editshow()
	});
	$('img,#halogycms_editpic').live('mouseout', function () {
		edithide()
	});
	$('div.halogycms_edit:not(.halogycms_active,.halogycms_preview)').live('mouseover', function () {
		$(this).children('.halogycms_buttons').children('a.halogycms_editbutton').addClass('halogycms_hover');
		$(this).addClass('halogycms_hover')
	});
	$('div.halogycms_edit:not(.halogycms_active)').live('mouseout', function () {
		$(this).children('.halogycms_buttons').children('a.halogycms_editbutton').removeClass('halogycms_hover');
		$(this).removeClass('halogycms_hover')
	});
})(jQuery);