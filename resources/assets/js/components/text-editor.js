import pell from 'pell';

export default class TextEditor {
	editor = null;

	constructor(el, params) {
		let textarea = el.querySelector('textarea');
		let value = textarea.value;

		this.editor = pell.init({
			element: el.querySelector('[data-role="text-editor"]'),
			styleWithCSS: false,
			onChange: (html) => {
				textarea.value = html;
			},
			actions: [
				{
					name: 'bold',
					icon: '<i class="material-symbols-outlined">format_bold</i>',
				},
				{
					name: 'italic',
					icon: '<i class="material-symbols-outlined">format_italic</i>',
				},
				{
					name: 'underline',
					icon: '<i class="material-symbols-outlined">format_underlined</i>',
				},
				{
					name: 'olist',
					icon: '<i class="material-symbols-outlined">format_list_numbered</i>',
				},
				{
					name: 'ulist',
					icon: '<i class="material-symbols-outlined">format_list_bulleted</i>',
				},
				{
					name: 'link',
					icon: '<i class="material-symbols-outlined">link</i>',
				},
			],
		});

		this.editor.content.innerHTML = value;

		this.editor.content.addEventListener('focus', function () {
			document.execCommand('defaultParagraphSeparator', false, 'p');
		});

		if (!value.trim()) {
			this.editor.content.innerHTML = '<p><br></p>';
		}

		this.editor.content.addEventListener('paste', function (e) {
			e.preventDefault();
			const text = (e.originalEvent || e).clipboardData.getData('text/plain');
			document.execCommand('insertText', false, text);
		});
	}

	setValue(content) {
	    this.editor.content.innerHTML = content;
    	const textarea = this.editor.element.closest('.text-editor')?.querySelector('textarea');
    	
	    if (textarea) {
    	    textarea.value = content;
    	}
	}
}

