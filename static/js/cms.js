(function($){$.fn.resizer=function(){var b=!($.browser.msie||$.browser.opera);function doResize(e){e=e.target||e;var a=e.value.length,ewidth=e.offsetWidth;if(a!=e.valLength||ewidth!=e.boxWidth){if(b&&(a<e.valLength||ewidth!=e.boxWidth))e.style.height="0px";var h=Math.max(e.expandMin,Math.min(e.scrollHeight,e.expandMax));e.style.overflow=(e.scrollHeight>h?"auto":"hidden");e.style.height=h+"px";e.valLength=a;e.boxWidth=ewidth}return true};this.each(function(){if(this.nodeName.toLowerCase()!="textarea")return;var p=this.className.match(/expand(\d+)\-*(\d+)*/i);this.expandMin=100;this.expandMax=(p?parseInt('0'+p[2],10):99999);doResize(this);if(!this.Initialized){this.Initialized=true;$(this).css("padding-top",0).css("padding-bottom",0);$(this).bind("keyup",doResize).bind("focus",doResize)}});return this}})(jQuery);

(function(){$.fn.insertAtCaret=function(e,f){if(!f)f='';return this.each(function(){if(document.selection){this.focus();d=document.selection.createRange();d.text=e+d.text+f;return false}else if(this.selectionStart||this.selectionStart=='0'){var a=this.selectionStart;var b=this.selectionEnd;var c=this.scrollTop;var d=this.value.substring(a,b);if(e=="\t"){d=d.replace(/\n/ig,"\n\t")}this.value=this.value.substring(0,a)+(e+d+f)+this.value.substring(b,this.value.length);this.selectionStart=a+e.length;this.selectionEnd=a+e.length;this.scrollTop=c;return false}else{this.value+=e;return false}})};})();

(function ($) {
	var cmsActive = true;
	var editactive = false;
	var activeBlock = '';
	function edit(a) {
		var b = $(a).closest('div.ficms_edit');
		var c = $(b).children('div.ficms_blockelement');
		var d = $(b).children('div.ficms_editblock');
		var e = $(b).children('div.ficms_buttons');
		var f = $(a).siblings('a').show();
		activeBlock = $(b).contents().find('textarea.code');
		var g = $(activeBlock).val();
		if ($(b).width() > 0) {
			$(b).width($(b).width());
		} else {
			$(b).width('202px');
		}
		$(activeBlock).css("margin-top", $(e).height() + 5);
		$(d).addClass('ficms_active');
		$(b).addClass('ficms_active');
		showadmin();
		$(a).hide();
		$(c).hide();
		$(d).show();
		$(activeBlock).focus();
	}
	function cancel(a) {
		var b = $(a).closest('div.ficms_edit');
		var c = $(b).children('div.ficms_blockelement');
		var d = $(b).children('div.ficms_editblock');
		var e = $(a).siblings('a').hide();
		var f = $(a).siblings('a.ficms_editbutton').show();
		$(b).removeClass('ficms_active');
		$(d).removeClass('ficms_active');
		hideadmin();
		$(a).hide();
		$(d).hide();
		$(c).show()
	}
	function save(b) {
		var c = $(b).closest('div.ficms_edit');
		var d = $(c).children('div.ficms_blockelement');
		var e = $(c).children('div.ficms_editblock');
		var f = $(b).siblings('a').hide();
		var g = $(b).siblings('a.ficms_editbutton').show();
		var h = $(c).contents().find('textarea.code').val() + '[!!ADDBLOCK!!]';
		$(c).removeClass('ficms_active');
		$(e).removeClass('ficms_active');
		hideadmin();
		$(b).hide();
		$(e).hide();
		$(d).html('<p class="ficms_loader">Loading...</p>');
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
		var h = ($('div.ficms_edit.ficms_active').length);
		var i = 0;
		if (h > 0) {
			$('div.ficms_edit.ficms_active').each(function () {
				var b = $(this).children('div.ficms_blockelement');
				var c = $(this).children('div.ficms_editblock');
				var d = $(c).find('textarea.code').val();
				var e = $(this).find('.ficms_savebutton').attr('href');
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
			cancel('.ficms_cancelbutton');
			$('#ficms_browser').hide();
			$('.ficms_buttons').hide();
			$('div.ficms_edit').addClass('ficms_preview');
			$('div#ficms_admin').addClass('ficms_hidden');
			$(a).text('Edit').addClass('preview')
		} else {
			$('div.ficms_edit').removeClass('ficms_preview');
			$('div,a').removeClass('ficms_hover');
			$('.ficms_buttons').show();
			$(a).text('Preview').removeClass('preview')
		}
	}
	function showadmin() {
		$('div#ficms_admin').animate({
			'top': '-12px',
			'height': '25px'
		}, 20, '', function () {
			$(this).find('.ficms_button').fadeIn(100)
		})
	}
	function hideadmin() {
		if (!$('.ficms_edit').hasClass('ficms_active')) {
			$('div#ficms_admin').animate({
				'top': '-20px',
				'height': '18px'
			}, 20, '', function () {
				$(this).find('.ficms_button').hide()
			})
		}
	}
	function editpic(a) {
		if ($(a).length > 0) {
			var b = $(a).offset();
			var c = $('#ficms_editpic').attr('rel') + '/' + $(a).attr('id');
			$('#ficms_editpic').attr('href', c).css({
				'top': b.top,
				'left': b.left
			}).show()
		} else {
			return
		}
	}
	function editshow() {
		$('#ficms_editpic').show()
	}
	function edithide() {
		if (!editactive) {
			$('#ficms_editpic').hide()
		}
	}
	function showpopup(a) {
		var b = $(a).attr('href');
		$('#ficms_popup').fadeIn(300).load(b, {}, function () {
			$(this).removeClass('loading')
		})
	}
	function showimages(a) {
		var b = $(a).closest('div.ficms_edit');
		activeBlock = $(b).contents().find('textarea.code');
		$(activeBlock).focus();
		$('#ficms_browser').fadeIn(300).load($(a).attr('href'), {}, function () {
			$(this).removeClass('loading')
		})
	}
	function insertimage(a) {
		$(activeBlock).focus();
		$(activeBlock).insertAtCaret('{image:' + $(a).attr('title') + '}');
		hidebrowser()
	}
	function showfiles(a) {
		var b = $(a).closest('div.ficms_edit');
		activeBlock = $(b).contents().find('textarea.code');
		$(activeBlock).focus();
		$('#ficms_browser').fadeIn(300).load($(a).attr('href'), {}, function () {
			$(this).removeClass('loading')
		})
	}
	function insertfile(a) {
		$(activeBlock).focus();
		$(activeBlock).insertAtCaret('{file:' + $(a).attr('title') + '}');
		hidebrowser()
	}
	function hidebrowser() {
		$('#ficms_browser, #ficms_popup').fadeOut(300, function () {
			$(this).html('');
			$(this).addClass('loading')
		})
	}
	function toggleFolder(a) {
		$(a).parent().parent('ul').next('ul').slideToggle(200)
	}
	function formatting(a, b) {
		var c = $(a).closest('div.ficms_edit');
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
	$('.ficms_confirm').live('click', function () {
		return confirm('You may lose unsaved changes. Continue?')
	});
	$('a.ficms_toggle').live('click', function () {
		preview(this);
		return false
	});
	$('a.ficms_editbutton').live('click', function () {
		edit(this);
		return false
	});
	$('div.ficms_edit:not(.ficms_preview)').live('dblclick', function () {
		edit($(this).find('a.ficms_editbutton'));
		return false
	});
	$('a.ficms_cancelbutton').live('click', function () {
		cancel(this);
		return false
	});
	$('a.ficms_saveall').live('click', function () {
		return autosave(this)
	});
	$('a.ficms_imagebutton').live('click', function () {
		showimages(this);
		return false
	});
	$('.ficms_insertimage').live('click', function () {
		insertimage(this);
		return false
	});
	$('a.ficms_filebutton').live('click', function () {
		showfiles(this);
		return false
	});
	$('.ficms_insertfile').live('click', function () {
		insertfile(this);
		return false
	});
	$('a.ficms_close').live('click', function () {
		hidebrowser();
		return false
	});
	$('a.ficms_boldbutton').live('click', function () {
		formatting(this, 'bold');
		return false
	});
	$('a.ficms_italicbutton').live('click', function () {
		formatting(this, 'italic');
		return false
	});
	$('a.ficms_h1button').live('click', function () {
		formatting(this, 'h1');
		return false
	});
	$('a.ficms_h2button').live('click', function () {
		formatting(this, 'h2');
		return false
	});
	$('a.ficms_h3button').live('click', function () {
		formatting(this, 'h3');
		return false
	});
	$('a.ficms_urlbutton').live('click', function () {
		formatting(this, 'url');
		return false
	});
	$('a.ficms_togglefolder').live('click', function () {
		toggleFolder(this);
		return false
	});
	$('a.ficms_savebutton').live('click', function () {
		save(this);
		return false
	});
	$('#ficms_editpic').live('click', function () {
		showpopup(this);
		return false
	});
	$('div#ficms_admin').hover(function () {
		showadmin()
	}, function () {
		hideadmin()
	});
	$('img.pic').live('mouseover', function () {
		editpic(this)
	});
	$('#ficms_editpic').live('mouseover', function () {
		editshow()
	});
	$('img,#ficms_editpic').live('mouseout', function () {
		edithide()
	});
	$('div.ficms_edit:not(.ficms_active,.ficms_preview)').live('mouseover', function () {
		$(this).children('.ficms_buttons').children('a.ficms_editbutton').addClass('ficms_hover');
		$(this).addClass('ficms_hover')
	});
	$('div.ficms_edit:not(.ficms_active)').live('mouseout', function () {
		$(this).children('.ficms_buttons').children('a.ficms_editbutton').removeClass('ficms_hover');
		$(this).removeClass('ficms_hover')
	});
})(jQuery);
