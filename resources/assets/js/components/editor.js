import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import ImageGallery from '@kiberpro/editorjs-gallery';
import Sortable from 'sortablejs';

import { parseJsonLd } from './../helpers';

export default class Editor {
	async getContent() {
		try {
			return await this.editor.save();
		} catch (err) {
			console.error('Saving failed:', err);
			return null;
		}
	}

	constructor(el, params) {
		this.settings = {
			name: 'content',
			upload_url: null,
			...params,
		};

		this.container = el;

		let form = this.container.closest('form');

		if (!form) {
			console.error('Editor required form to save data.');
			return;
		}

		window.Joona.getInstance(form, 'components.form').then((instance) => {
			instance.instance.handler.intercept = async (fd) => {
				const content = await this.getContent();
				fd.append(this.settings.name, JSON.stringify(content));
			};
		});

		let dataElement = document.createElement('input');
		dataElement.type = 'hidden';
		dataElement.name = this.settings.name;
		this.container.appendChild(dataElement);

		this.editorId = `ed${Math.random().toString(36).substring(2, 15)}`;

		let editorWrapper = document.createElement('div');
		editorWrapper.id = this.editorId;
		editorWrapper.classList.add('editor');

		this.container.appendChild(editorWrapper);
	}

	init(params) {
		params.holder = this.editorId;
		params.data = parseJsonLd(this.container.querySelector('[data-role="data"]'));

		let tools = {
			heading: Header,
			list: {
				class: List,
				config: {},
			},
		};

		if (this.settings.upload_url) {
			tools.gallery = {
				class: ImageGallery,
				config: {
					sortableJs: Sortable,
					buttonContent: trans('joona::common.select_files'),
					endpoints: {
						byFile: this.settings.upload_url,
					},
				},
			};
		}

		params = {
			tools: tools,
			...params,
		};

		this.editor = new EditorJS(params);

		return this.editor;
	}
}
