import './bootstrap';
import 'jquery';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

import $ from 'jquery'
window.jQery=$;
window.$=$;

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('[data-quill-editor]').forEach((editorElement) => {
		const input = document.getElementById(editorElement.dataset.quillInput);
		const form = editorElement.closest('form');

		if (!input || !form) {
			return;
		}

		const editor = new Quill(editorElement, {
			theme: 'snow',
			modules: {
				toolbar: [
					[{ header: [1, 2, 3, false] }],
					['bold', 'italic', 'underline', 'strike'],
					[{ list: 'ordered' }, { list: 'bullet' }],
					['link', 'clean'],
				],
			},
		});

		if (input.value) {
			editor.clipboard.dangerouslyPasteHTML(input.value);
		}

		form.addEventListener('submit', () => {
			input.value = editor.root.innerHTML;
		});
	});
});