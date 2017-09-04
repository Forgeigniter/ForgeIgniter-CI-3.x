(function () {
	$.fn.insertAtCaret = function (e, f) {
		if (!f) f = '';
		return this.each(function () {
			if (document.selection) {
				this.focus();
				d = document.selection.createRange();
				d.text = e + d.text + f;
				return false
			} else if (this.selectionStart || this.selectionStart == '0') {
				var a = this.selectionStart;
				var b = this.selectionEnd;
				var c = this.scrollTop;
				var d = this.value.substring(a, b);
				if (e == "\t") {
					d = d.replace(/\n/ig, "\n\t")
				}
				this.value = this.value.substring(0, a) + (e + d + f) + this.value.substring(b, this.value.length);
				this.selectionStart = a + e.length;
				this.selectionEnd = a + e.length;
				this.scrollTop = c;
				return false
			} else {
				this.value += e;
				return false
			}
		})
	};
})();


(function ($) {
	var editactive = false;
	var activeBlock = '';
	function showForm(a) {
		$url = a.href;
		$('.showform').removeClass('active');
		$(a).addClass('active');
		if ($url != '#') {
			$('div.hidden').load($url, function () {
				$('div.hidden:visible').slideUp(200);
				$('div.hidden').slideDown(200, function () {
					$.scrollTo('#content', 200);
					$('input:first:not(.button)').focus()
				})
			})
		} else {
			$('div.hidden:visible').slideUp(200);
			$('div.hidden').slideDown(200, function () {
				$.scrollTo('#content', 200);
				$('input:first:not(.button)').focus()
			})
		}
		return false
	}
	function hideForm(a) {
		$.scrollTo(0, 200);
		$('.showform').removeClass('active');
		$('div.hidden:visible').slideUp(200, function () {
			$('div.hidden').html('')
		})
	}
	function checkajax() {
		if (updated == 0) {
			$('.content').css('opacity', '0.5')
		} else {
			$('.content').css('opacity', '1')
		}
	}
	function toggle() {
		var a = $('#controls:hidden').length;
		$('#controls').toggle('400', function () {
			if (a) {
				$('div.edit').removeClass('preview');
				$('.buttons').show();
				$('a#toggle').html('Preview')
			} else {
				$('div.edit').addClass('preview');
				$('.buttons').hide();
				$('a#toggle').html('Edit')
			}
		})
	}
	function toggleFolder(a) {
		$(a).parent().parent('ul').next('ul').slideToggle(200)
	}
	function editpic(a) {
		if ($(a).length > 0) {
			var b = $(a).offset();
			var c = $('#editpic').attr('rel') + '/' + $(a).attr('id');
			$('#editpic').attr('href', c).css({
				'top': b.top,
				'left': b.left
			}).show()
		} else {
			return
		}
	}
	function editshow() {
		$('#editpic').show()
	}
	function edithide() {
		if (!editactive) {
			$('#editpic').hide()
		}
	}
	function showimages(a) {
		var b = $('textarea#body');
		$(b).focus();
		$('#halogycms_browser').fadeIn(300).load($(a).attr('href'), {}, function () {
			$(this).removeClass('loading')
		})
	}
	function insertimage(a) {
		var b = $('textarea#body');
		$(b).focus();
		$(b).insertAtCaret('{image:' + $(a).attr('title') + '}');
		hidebrowser()
	}
	function showfiles(a) {
		var b = $('textarea#body');
		$(b).focus();
		$('#halogycms_browser').fadeIn(300).load($(a).attr('href'), {}, function () {
			$(this).removeClass('loading')
		})
	}
	function insertfile(a) {
		var b = $('textarea#body');
		$(b).focus();
		$(b).insertAtCaret('{file:' + $(a).attr('title') + '}');
		hidebrowser()
	}
	function hidebrowser() {
		$('#halogycms_browser').fadeOut(300, function () {
			$(this).html('');
			$(this).addClass('loading')
		})
	}
	function formatting(a, b) {
		var c = $('textarea#body');
		if (b == 'bold') {
			$(c).insertAtCaret('**', '**')
		}
		if (b == 'italic') {
			$(c).insertAtCaret('*', '*')
		}
		if (b == 'h1') {
			$(c).insertAtCaret('# ', "\n\n")
		}
		if (b == 'h2') {
			$(c).insertAtCaret('## ', "\n\n")
		}
		if (b == 'h3') {
			$(c).insertAtCaret('### ', "\n\n")
		}
		if (b == 'url') {
			var d = prompt('Please enter a web address or email you want to link to:', '');
			if (d) {
				if (d.match('@')) {
					$(c).insertAtCaret('[', '](mailto:' + d + ')')
				} else if (d.match('^www\.')) {
					$(c).insertAtCaret('[', '](http://' + d + ')')
				} else if (!d.match('^http:\/\/(www\.)?')) {
					$(c).insertAtCaret('[', '](/' + d + ')')
				} else {
					$(c).insertAtCaret('[', '](' + d + ')')
				}
			} else {
				return false
			}
		}
		$(c).focus()
	}
	$(function () {
		$('textarea.code').keypress(function (e) {
			if (e.keyCode == 9) {
				$(this).insertAtCaret("\t");
				return false
			}
		});
		$('a.toggle').on('click', function () {
			toggle();
			return false
		});
		$('a.previewbutton').on('click', function () {
			$(this).hide();
			return false
		});
		$('.halogycms_confirm').on('click', function () {
			return confirm('You may lose unsaved changes. Continue?')
		});
		$('a.halogycms_imagebutton').on('click', function () {
			showimages(this);
			return false
		});
		$('.halogycms_insertimage').on('click', function () {
			insertimage(this);
			return false
		});
		$('a.halogycms_filebutton').on('click', function () {
			showfiles(this);
			return false
		});
		$('.halogycms_insertfile').on('click', function () {
			insertfile(this);
			return false
		});
		$('a.halogycms_close').on('click', function () {
			hidebrowser();
			return false
		});
		$('a.boldbutton').on('click', function () {
			formatting(this, 'bold');
			return false
		});
		$('a.italicbutton').on('click', function () {
			formatting(this, 'italic');
			return false
		});
		$('a.h1button').on('click', function () {
			formatting(this, 'h1');
			return false
		});
		$('a.h2button').on('click', function () {
			formatting(this, 'h2');
			return false
		});
		$('a.h3button').on('click', function () {
			formatting(this, 'h3');
			return false
		});
		$('a.urlbutton').on('click', function () {
			formatting(this, 'url');
			return false
		});
		$('a.togglefolder').on('click', function () {
			toggleFolder(this);
			return false
		});
		$('a.showform').on('click', function () {
			showForm(this);
			return false
		});
		$('input#cancel').on('click', function () {
			hideForm(this);
			return false
		});
		$('.cancel').on('click', function () {
			$('div.hidden').slideUp('100');
			return false
		});
	});
})(jQuery);