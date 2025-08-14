import BaseGallery from '@kiberpro/editorjs-gallery';

export default class GalleryWithCaptions extends BaseGallery {
	constructor(args) {
		super(args);

		const originalAppend = this.ui.appendImage.bind(this.ui);

		this.ui.appendImage = (file) => {
			originalAppend(file);
			const container = this.ui.nodes.itemsContainer.lastElementChild;
			this._injectItemCaptionInput(container, file.caption || '');
		};

		Array.from(this.ui.nodes.itemsContainer.children).forEach((container, i) => {
			const f = (this._data?.files || [])[i] || {};

			if (!container.querySelector('.image-gallery__item-caption-input')) {
				this._injectItemCaptionInput(container, f.caption || '');
			}
		});
	}

	private _injectItemCaptionInput(container: Element, value: string) {
		const input = document.createElement('input');
		input.type = 'text';
		input.className = 'image-gallery__item-caption-input form-control form-control-sm';
		input.placeholder = this.api.i18n.t('Image caption');
		input.value = value || '';
		input.readOnly = !!this.readOnly;

		container.appendChild(input);
	}

	async save() {
		const base = await super.save();

		const items = Array.from(this.ui.nodes.itemsContainer.children);

		base.files = items.map((container, i) => {
			const f = (this._data?.files || [])[i] || {};
			const el = container.querySelector<HTMLInputElement>(
				'.image-gallery__item-caption-input'
			);
			return {
				...f,
				caption: el ? el.value : f.caption || '',
			};
		});

		return base;
	}

	static get sanitize() {
		const parent = (super.sanitize ?? {}) as any;
		return {
			...parent,
			caption: {},
			files: { ...(parent.files ?? {}), caption: {} },
		};
	}
}
