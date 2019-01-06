var selectedModule = '';

function getTemplate(el){
	selectedModule = $(el).val();

	if (selectedModule.match('!')){
		selectedModule = '';
		$(el).val('');
		alert('Please select a template within the module.');
	}

	if (selectedModule != ''){
		$('.showModuleName').hide();
		$('#templateName').val(selectedModule);

		if (selectedModule == 'custom'){
			$('.showModulePath').show();
		}
		else {
			$('.showModulePath').hide();
			$('#modulePath').val(selectedModule);
		}
	}
	else if (selectedModule == ''){
		$('.showModuleName').show();
		$('.showModulePath').hide();
	}
}

function loadTemplate(data){
	$('textarea#body').val(data);
}


function revertTemplate(){
	if (confirm('By doing this you will lose any unsaved changes. Are you sure?')){

		getTemplate($('select#moduleSelect'));

		if (selectedModule == ''){
			$('textarea#body').val("{include:header}\n\n{block1}\n\n{include:footer}");
		} else {
			$.get($('select#moduleSelect').attr('rel')+'/'+selectedModule, loadTemplate);
		}

		return true;
	}
	else {
		return false;
	}
}

$(function(){
	$('select#moduleSelect').change(function(){
		revertTemplate();
	});

	$('input#default').click(function(){
		revertTemplate();
	});

	getTemplate($('select#moduleSelect'));

});
