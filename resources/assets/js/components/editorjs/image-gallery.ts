import BaseGallery from '@kiberpro/editorjs-gallery';

type GalleryFile = {
	url?: string;
	caption?: string;
	name?: string;
	filename?: string;
	originalName?: string;
	original_filename?: string;
	title?: string;
	mime?: string;
	mimeType?: string;
	type?: string;
	[key: string]: unknown;
};

const IMAGE_EXTENSIONS = new Set([
	'avif',
	'bmp',
	'gif',
	'ico',
	'jpeg',
	'jpg',
	'png',
	'svg',
	'tif',
	'tiff',
	'webp',
]);

const VIDEO_EXTENSIONS = new Set(['mp4']);

export default class GalleryWithCaptions extends BaseGallery {
	constructor(args) {
		super(args);

		const originalAppend = this.ui.appendImage.bind(this.ui);

		this.ui.appendImage = (file) => {
			originalAppend(file);
			const container = this.ui.nodes.itemsContainer.lastElementChild;
			this._decorateFilePreview(container, file);
			this._injectItemCaptionInput(container, file.caption || '');
		};

		Array.from(this.ui.nodes.itemsContainer.children).forEach((container, i) => {
			const f = (this._data?.files || [])[i] || {};

			this._decorateFilePreview(container, f);

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

	private _decorateFilePreview(container: Element | null, file: GalleryFile) {
		if (!container) {
			return;
		}

		if (this._shouldShowAsFile(file)) {
			this._showFilePlaceholder(container, file);
			return;
		}

		const picture = container.querySelector<HTMLImageElement>('img.image-gallery__image-picture');

		if (!picture) {
			return;
		}

		picture.addEventListener('error', () => {
			this._showFilePlaceholder(container, file);
		});
	}

	private _shouldShowAsFile(file: GalleryFile): boolean {
		const mime = String(file.mime || file.mimeType || file.type || '').toLowerCase();

		if (mime.startsWith('image/') || mime.startsWith('video/')) {
			return false;
		}

		const extension = this._extensionFromFile(file);

		if (IMAGE_EXTENSIONS.has(extension) || VIDEO_EXTENSIONS.has(extension)) {
			return false;
		}

		if (mime && mime !== 'application/octet-stream') {
			return true;
		}

		return Boolean(extension);
	}

	private _extensionFromFile(file: GalleryFile): string {
		const candidates = [
			file.name,
			file.filename,
			file.originalName,
			file.original_filename,
			file.url,
		];

		for (const candidate of candidates) {
			if (typeof candidate !== 'string' || candidate.trim() === '') {
				continue;
			}

			const path = candidate.split('?')[0].split('#')[0];
			const base = path.split('/').pop() || path;
			const separator = base.lastIndexOf('.');

			if (separator <= 0 || separator === base.length - 1) {
				continue;
			}

			return base.slice(separator + 1).toLowerCase();
		}

		return '';
	}

	private _fileLabel(file: GalleryFile): string {
		const fromFields = [
			file.name,
			file.filename,
			file.originalName,
			file.original_filename,
			file.title,
		].find((value) => typeof value === 'string' && value.trim() !== '');

		if (fromFields) {
			return fromFields.trim();
		}

		if (typeof file.url === 'string' && file.url.trim() !== '') {
			try {
				const url = new URL(file.url, window.location.origin);
				const base = decodeURIComponent(url.pathname.split('/').pop() || '');

				if (base) {
					return base;
				}
			} catch {
				const base = decodeURIComponent(file.url.split('?')[0].split('/').pop() || '');

				if (base) {
					return base;
				}
			}
		}

		return this.api.i18n.t('File');
	}

	private _showFilePlaceholder(container: Element, file: GalleryFile) {
		container.querySelector('.image-gallery__image-picture')?.remove();

		if (!container.querySelector('.image-gallery__file-name')) {
			const label = document.createElement('div');
			label.className = 'image-gallery__file-name';
			label.textContent = this._fileLabel(file);
			label.title = label.textContent;

			const insertBefore =
				container.querySelector('.image-gallery__image-trash') ||
				container.querySelector('.image-gallery__item-caption-input');

			if (insertBefore) {
				container.insertBefore(label, insertBefore);
			} else {
				container.prepend(label);
			}
		}

		container.classList.add('image-gallery__image--file');
		container.classList.add('image-gallery__image--filled');
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
			files: { ...(parent.files ?? {}), caption: {}, name: {} },
		};
	}
}
