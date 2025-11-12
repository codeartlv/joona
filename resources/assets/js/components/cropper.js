// CropperWrapper.js
import CropperJS from 'cropperjs';
import Modal from './modal.js';
import { parseRoute } from './../helpers';

export default class ImageCropper {
	events = {
		finish: [],
		crop: [],
	};

	constructor(imageId, imageUrl, saveUrl, presets, options = {}) {
		if (!imageUrl) {
			throw new Error('setup(imageUrl, presets): imageUrl is required.');
		}

		if (!Array.isArray(presets) || presets.length === 0) {
			throw new Error('setup(imageUrl, presets): presets must be a non-empty array.');
		}

		this._presets = this._normalizePresets(presets);
		this._saveUrl = saveUrl;
		this._imageUrl = imageUrl;
		this._imageId = imageId;
		this._form = null;

		this._opts = {
			cropperOptions: options.cropperOptions || {},
			...options,
		};

		this._mount = null;
	}

	open() {
		const url = parseRoute('joona.cropper');
		const modal = new Modal('cropper');

		modal.open(url).then((wnd) => {
			window.Joona.getInstance(wnd, 'components.form').then((form) => {
				this._form = form;
				this._form.element.action = this._saveUrl;

				this._form.instance.on('success', () => {
					modal.close();
					this._dispatchEvent('crop');
				});
			});

			this._mount = wnd.querySelector('[data-role="cropper.root"]');

			this._opts['select-container'] = null; // dropdown removed
			this._opts['save-button'] = wnd.querySelector(this._mount.dataset.saveButton);

			this.ready();
		});
	}

	ready() {
		// Internal state
		this._root = null;
		this._grid = null;
		this._imgByPreset = new Map(); // id -> <img>
		this._cropperByPreset = new Map(); // id -> CropperJS
		this._saveBtn = null;

		// per-preset data
		this._data = new Map(); // id -> crop data (natural image coords)
		this._started = new Set(); // id where user initiated a crop
		this._dirty = new Set(); // id where crop differs from baseline (baseline is "no crop")

		this._onCropEndHandlers = new Map(); // id -> handler
		this._onCropStartHandlers = new Map(); // id -> handler

		this._buildUi();
		this._mountAllCroppers();
	}

	on(event, callback) {
		this.events[event].push(callback);
	}

	_dispatchEvent(event, ...data) {
		if (this.events[event] && this.events[event].length) {
			for (let fn of this.events[event]) {
				fn.apply(this, data);
			}
		}
	}

	/** Deprecated with multi-view: always null to signal "no single current preset". */
	getCurrentPresetId() {
		return null;
	}

	/** Multi-view helpers */
	getCropData(presetId) {
		return this._data.get(presetId) ?? null;
	}

	getCroppedCanvas(presetId, options) {
		const c = this._cropperByPreset.get(presetId);
		return c ? c.getCroppedCanvas(options) : null;
	}

	/**
	 * Save all modified crops.
	 * Returns an object keyed by preset id, containing the crop data and preset meta.
	 * Only includes presets where the user actually started cropping and ended with a crop box.
	 */
	save() {
		// consolidate latest data from active croppers
		for (const [id, cropper] of this._cropperByPreset) {
			if (!cropper) continue;
			const hasCrop = !!cropper.cropped;
			const cur = hasCrop ? cropper.getData() : null;
			this._data.set(id, cur);

			// baseline is "no crop"
			if (this._isDifferent(cur, null)) {
				this._dirty.add(id);
			} else {
				this._dirty.delete(id);
			}
		}

		const result = {};
		for (const id of this._dirty) {
			// include only if the user started cropping on this preset
			if (!this._started.has(id)) continue;

			const data = this._data.get(id);
			if (!data) continue;

			const preset = this._presets.find((p) => p.id === id);
			result[id] = {
				crop: { ...data },
				preset: {
					id: preset.id,
					caption: preset.caption,
					width: preset.width,
					height: preset.height,
				},
			};
		}

		return result;
	}

	destroy() {
		// detach listeners and destroy croppers
		for (const [id, img] of this._imgByPreset) {
			const endH = this._onCropEndHandlers.get(id);
			const startH = this._onCropStartHandlers.get(id);
			if (img && endH) img.removeEventListener('cropend', endH);
			if (img && startH) img.removeEventListener('cropstart', startH);
		}

		for (const [, cropper] of this._cropperByPreset) {
			if (cropper) cropper.destroy();
		}

		this._cropperByPreset.clear();
		this._imgByPreset.clear();
		this._onCropEndHandlers.clear();
		this._onCropStartHandlers.clear();

		if (this._root) {
			this._root.replaceChildren();
		}

		this._data.clear();
		this._dirty.clear();
		this._started.clear();
		this._presets = [];
	}

	// ---------- Internal ----------

	_buildUi() {
		if (!this._root) {
			this._root = document.createElement('div');
			this._root.className = 'cropper-wrapper';
			this._mount.appendChild(this._root);
		} else {
			this._root.replaceChildren();
		}

		// Save button
		if (!this._opts['save-button']) {
			this._saveBtn = document.createElement('button');
			this._saveBtn.classList.add('btn');
			this._root.appendChild(this._saveBtn);
		} else {
			this._saveBtn = this._opts['save-button'];
		}

		this._dataInput = document.createElement('input');
		this._dataInput.type = 'hidden';
		this._dataInput.name = 'data';
		this._root.appendChild(this._dataInput);

		this._saveBtn.addEventListener('click', () => {
			const data = this.save();
			this._dataInput.value = JSON.stringify(data);

			this._dispatchEvent('finish', data);

			let inp = document.createElement('input');
			inp.type = 'hidden';
			inp.name = 'media_id';
			inp.value = this._imageId;

			this._form.element.appendChild(inp);
			this._form.instance.handler.submit();
		});

		// Grid container to render ALL presets
		this._grid = document.createElement('div');
		this._grid.className = 'cropper-grid';
		this._root.appendChild(this._grid);
	}

	_mountAllCroppers() {
		// For each preset, render a tile with caption + image
		for (const p of this._presets) {
			const tile = document.createElement('div');
			tile.className = 'cropper-tile';

			const img = document.createElement('img');
			img.alt = `Image to crop for preset "${p.caption}"`;
			img.classList.add('cropper-source-image');

			tile.appendChild(img);
			this._grid.appendChild(tile);

			this._imgByPreset.set(p.id, img);

			const imageContainer = document.createElement('div');
			imageContainer.style.aspectRatio = p._ratio;
			imageContainer.classList.add('image-container');

			imageContainer.append(img);
			tile.appendChild(imageContainer);

			img.onload = () => {
				this._initCropperForPreset(p, img);
			};

			img.src = this._imageUrl;
		}
	}

	_computeCenteredFitRect(targetRatio, imgW, imgH) {
		const imgRatio = imgW / imgH;
		let width, height;

		if (imgRatio >= targetRatio) {
			// image is wider — limit by height
			height = imgH;
			width = Math.round(height * targetRatio);
		} else {
			// image is taller — limit by width
			width = imgW;
			height = Math.round(width / targetRatio);
		}

		const x = Math.round((imgW - width) / 2);
		const y = Math.round((imgH - height) / 2);
		return { x, y, width, height };
	}

	_initCropperForPreset(preset, imgEl) {
		//return;

		const prev = this._cropperByPreset.get(preset.id);

		if (prev) {
			prev.destroy();
			this._cropperByPreset.delete(preset.id);
		}

		const cropper = new CropperJS(imgEl, {
			autoCrop: true,
			responsive: true,
			background: false,
			dragMode: 'crop',
			...this._opts.cropperOptions,
			aspectRatio: preset._ratio,
			viewMode: 1,
			zoomOnWheel: false,
			checkCrossOrigin: false,
			crop: () => {
				let data = cropper.getCropBoxData();
				let hasCrop = 'left' in data;

				const container = imgEl
					.closest('.cropper-tile')
					.querySelector('.cropper-container');
				container.classList.toggle('has-crop', hasCrop);
			},
			ready: () => {
				// CHANGED: initialize a centered, maximal crop that fits the preset ratio
				try {
					const imgData = cropper.getImageData(); // natural image space
					const { naturalWidth: iw, naturalHeight: ih } = imgData;

					const rect = this._computeCenteredFitRect(preset._ratio, iw, ih);

					// Apply in natural pixel coordinates
					cropper.setData({
						x: rect.x,
						y: rect.y,
						width: rect.width,
						height: rect.height,
						rotate: 0,
						scaleX: 1,
						scaleY: 1,
					});

					// Ensure the crop box is visible
					cropper.crop();
				} catch (_) {
					// Fallback: ensure cropped state visible even if something above fails
					try {
						cropper.crop();
					} catch (_) {}
				}

				const header = document.createElement('div');
				header.className = 'cropper-tile__header';
				header.textContent = `${preset.caption} (${preset.width}×${preset.height})`;

				const canvas = imgEl.closest('.cropper-tile').querySelector('.cropper-canvas');
				canvas.appendChild(header);

				const reset = document.createElement('a');
				reset.href = 'javascript:;';
				reset.className = 'material-symbols-outlined cropper__reset';
				reset.text = 'reset_focus';
				canvas.appendChild(reset);

				const dragBox = imgEl.closest('.cropper-tile').querySelector('.cropper-drag-box');
				dragBox.addEventListener('click', (e) => {
					const under = (document.elementsFromPoint?.(e.clientX, e.clientY) || []).find(
						(el) => el.closest && el.closest('a')
					);

					const a = under && under.closest ? under.closest('a') : null;
					if (a) {
						a.click();
						e.preventDefault();
						e.stopPropagation();
					}
				});

				reset.addEventListener('click', () => {
					// Reset back to our initial centered-fit crop instead of clearing
					try {
						const imgData = cropper.getImageData();
						const { naturalWidth: iw, naturalHeight: ih } = imgData;
						const rect = this._computeCenteredFitRect(preset._ratio, iw, ih);
						cropper.setData({
							x: rect.x,
							y: rect.y,
							width: rect.width,
							height: rect.height,
							rotate: 0,
							scaleX: 1,
							scaleY: 1,
						});
						cropper.crop();
					} catch (_) {
						// if anything goes wrong, keep previous behavior
						cropper.clear();
					}
				});

				// downstream hook if provided
				if (typeof this._opts.cropperOptions?.ready === 'function') {
					this._opts.cropperOptions.ready.call(cropper);
				}
			},
		});

		// Attach per-preset listeners
		const onStart = () => {
			this._started.add(preset.id);
			// Ensure crop box is visible once the user starts interacting
			try {
				cropper.crop();
			} catch (_) {}
		};

		const onEnd = () => {
			// Update stored data and dirty flag against baseline (null/no-crop)
			const cur = cropper.cropped ? cropper.getData() : null;
			this._data.set(preset.id, cur);

			if (this._isDifferent(cur, null)) {
				this._dirty.add(preset.id);
			} else {
				this._dirty.delete(preset.id);
			}
		};

		imgEl.addEventListener('cropstart', onStart, { passive: true });
		imgEl.addEventListener('cropend', onEnd, { passive: true });

		this._onCropStartHandlers.set(preset.id, onStart);
		this._onCropEndHandlers.set(preset.id, onEnd);
		this._cropperByPreset.set(preset.id, cropper);
	}

	_normalizePresets(input) {
		const seen = new Map();

		return input.map((p, idx) => {
			const w = Number(p.width);
			const h = Number(p.height);

			if (!Number.isFinite(w) || !Number.isFinite(h) || w <= 0 || h <= 0) {
				throw new Error(`Invalid width/height in preset at index ${idx}.`);
			}

			let id = String(p.id ?? `preset_${idx}`);

			if (seen.has(id)) {
				const n = seen.get(id) + 1;
				seen.set(id, n);
				id = `${id}__${n}`;
			} else {
				seen.set(id, 1);
			}

			return {
				id,
				caption: String(p.caption ?? id),
				width: w,
				height: h,
				_ratio: w / h,
			};
		});
	}

	_isDifferent(a, b, eps = 0.5) {
		if (!a || !b) return !!a || !!b;

		const keys = ['x', 'y', 'width', 'height', 'rotate', 'scaleX', 'scaleY'];

		for (const k of keys) {
			const av = Number(a[k] ?? (k === 'scaleX' || k === 'scaleY' ? 1 : 0));
			const bv = Number(b[k] ?? (k === 'scaleX' || k === 'scaleY' ? 1 : 0));

			if (!Number.isFinite(av) || !Number.isFinite(bv)) {
				continue;
			}

			if (Math.abs(av - bv) > eps) {
				return true;
			}
		}
		return false;
	}
}
