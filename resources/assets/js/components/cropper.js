// CropperWrapper.js
import CropperJS from 'cropperjs';
import Modal from './modal.js';
import { parseRoute } from './../helpers';

export default class ImageCropper {
	events = {
		finish: [],
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
				});
			});

			this._mount = wnd.querySelector('[data-role="cropper.root"]');

			this._opts['select-container'] = wnd.querySelector(this._mount.dataset.selectContainer);
			this._opts['save-button'] = wnd.querySelector(this._mount.dataset.saveButton);

			this.ready();
		});
	}

	ready() {
		// Internal state
		this._root = null;
		this._controls = null;
		this._select = null;
		this._img = null;
		this._cropper = null;
		this._saveBtn = null;

		this._savedData = new Map();
		this._initialData = new Map();
		this._dirty = new Set();
		this._currentPresetId = null;

		this._onCropEnd = null;

		this._buildUi();
		this._wireSelect();

		this._img.onload = () => {
			this._currentPresetId = this._presets[0].id;
			this._initCropper(this._presets[0]._ratio, this._currentPresetId);
		};
		this._img.src = this._imageUrl;
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

	/** Current preset id. */
	getCurrentPresetId() {
		return this._currentPresetId;
	}

	/** Current crop data (natural image coordinates). */
	getCurrentCropData() {
		return this._cropper ? this._cropper.getData() : null;
	}

	/** Canvas of the cropped region (may be tainted if image is cross-origin without CORS). */
	getCroppedCanvas(options) {
		return this._cropper ? this._cropper.getCroppedCanvas(options) : null;
	}

	/**
	 * Save all modified crops.
	 * Returns an object keyed by preset id, containing the crop data and preset meta.
	 * Only includes presets that differ from their initial auto-crop (i.e., actually changed).
	 */
	save() {
		if (this._cropper && this._currentPresetId) {
			const cur = this._cropper.cropped ? this._cropper.getData() : null;

			this._savedData.set(this._currentPresetId, cur);
			const base = this._initialData.get(this._currentPresetId);

			if (this._isDifferent(cur, base)) {
				this._dirty.add(this._currentPresetId);
			} else {
				this._dirty.delete(this._currentPresetId);
			}
		}

		const result = {};

		for (const id of this._dirty) {
			const data = this._savedData.get(id);
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
		if (this._cropper) {
			this._detachRuntimeListeners();
			this._cropper.destroy();
			this._cropper = null;
		}
		if (this._root) {
			this._root.replaceChildren();
		}
		this._select = null;
		this._img = null;
		this._controls = null;
		this._savedData.clear();
		this._initialData.clear();
		this._dirty.clear();
		this._currentPresetId = null;
		this._presets = [];
	}

	// ---------- Internal ----------

	_buildUi() {
		if (!this._root) {
			this._root = document.createElement('div');
			this._root.className = 'cropper-wrapper';
			this._root.style.width = '100%';
			this._mount.appendChild(this._root);
		} else {
			this._root.replaceChildren();
		}

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

		this._controls = document.createElement('div');
		this._controls.className = 'cropper-controls';

		this._select = document.createElement('select');
		this._select.setAttribute('aria-label', 'Crop presets');
		this._select.classList.add('form-select');
		this._select.classList.add('form-select-sm');

		for (const p of this._presets) {
			const opt = document.createElement('option');
			opt.value = p.id;
			opt.textContent = p.caption;
			this._select.appendChild(opt);
		}

		if (this._opts['select-container']) {
			this._opts['select-container'].appendChild(this._select);
		} else {
			this._controls.appendChild(this._select);
		}

		this._img = document.createElement('img');
		this._img.alt = 'Image to crop';
		this._img.style.maxWidth = '100%';
		this._img.style.height = 'auto';
		this._img.style.display = 'block';

		this._root.appendChild(this._controls);
		this._root.appendChild(this._img);
	}

	_wireSelect() {
		this._select.onchange = () => {
			const nextId = this._select.value;
			if (nextId === this._currentPresetId) return;

			// Save current crop for current preset
			if (this._cropper && this._currentPresetId) {
				const cur = this._cropper.cropped ? this._cropper.getData() : null;
				this._savedData.set(this._currentPresetId, cur);
				const base = this._initialData.has(this._currentPresetId)
					? this._initialData.get(this._currentPresetId)
					: null;
				if (this._isDifferent(cur, base)) this._dirty.add(this._currentPresetId);
				else this._dirty.delete(this._currentPresetId);
			}

			// Switch preset
			const preset = this._presets.find((p) => p.id === nextId);
			if (!preset) return;
			this._currentPresetId = preset.id;
			this._initCropper(preset._ratio, preset.id);
		};
	}

	_initCropper(aspectRatio, presetId) {
		if (this._cropper) {
			this._detachRuntimeListeners();
			this._cropper.destroy();
			this._cropper = null;
		}

		const saved = presetId ? this._savedData.get(presetId) : undefined;

		this._cropper = new CropperJS(this._img, {
			autoCrop: false,
			responsive: true,
			background: false,
			dragMode: 'crop',
			...this._opts.cropperOptions,
			aspectRatio,
			viewMode: 1,
			zoomOnWheel: false,
			checkCrossOrigin: false,
			ready: () => {
				if (saved) {
					// If we have a previous crop for this preset, show and restore it
					this._cropper.crop(); // ensure crop box is visible
					try {
						this._cropper.setData(saved);
					} catch (_) {}
				} else {
					// No previous crop: keep it invisible
					this._cropper.clear();
				}

				// Baseline: if first time we see this preset, record “no crop” or restored crop
				if (!this._initialData.has(presetId)) {
					this._initialData.set(presetId, saved || null);
				}

				this._attachRuntimeListeners();

				if (typeof this._opts.cropperOptions?.ready === 'function') {
					this._opts.cropperOptions.ready.call(this._cropper);
				}
			},
		});
	}

	_attachRuntimeListeners() {
		if (!this._img) {
			return;
		}

		this._onCropEnd = () => {
			if (!this._cropper || !this._currentPresetId) {
				return;
			}

			const cur = this._cropper.cropped ? this._cropper.getData() : null;

			this._savedData.set(this._currentPresetId, cur);

			const base = this._initialData.get(this._currentPresetId);

			if (this._isDifferent(cur, base)) {
				this._dirty.add(this._currentPresetId);
			} else {
				this._dirty.delete(this._currentPresetId);
				s;
			}
		};
		this._img.addEventListener('cropend', this._onCropEnd, { passive: true });
	}

	_detachRuntimeListeners() {
		if (this._img && this._onCropEnd) {
			this._img.removeEventListener('cropend', this._onCropEnd);
		}

		this._onCropEnd = null;
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
