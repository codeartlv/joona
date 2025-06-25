import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import ImageGallery from '@kiberpro/editorjs-gallery';
import Quote from '@editorjs/quote';
import Table from '@editorjs/table';
import Embed from '@editorjs/embed';
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

		params = {
			tools: {},
			i18n: {
				messages: {
					toolNames: {
						'Ordered List': trans('joona::common.editorjs.tools.list.ordered'),
						'Unordered List': trans('joona::common.editorjs.tools.list.unordered'),
						Checklist: trans('joona::common.editorjs.tools.list.checklist'),
						Heading: trans('joona::common.editorjs.tools.heading.name'),
						Gallery: trans('joona::common.editorjs.tools.gallery.name'),
						Text: trans('joona::common.editorjs.tools.text.name'),
						Quote: trans('joona::common.editorjs.tools.quote.name'),
						Table: trans('joona::common.editorjs.tools.table.name'),
					},
					tools: {},
				},
			},
			...params,
		};

		params.tools.heading = {
			class: Header,
			config: {
				levels: [1, 2, 3, 4, 5, 6],
				defaultLevel: 3,
			},
		};
		params.tools.list = List;
		params.tools.quote = Quote;
		params.tools.table = Table;
		params.tools.embed = Embed;

		if (this.settings.upload_url) {
			params.tools.gallery = {
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

		this.editor = new EditorJS(params);

		return this.editor;
	}
}
