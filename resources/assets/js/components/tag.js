import { parseRoute, parseJsonLd } from './../helpers';
import Tagify from '@yaireo/tagify';

export default class Tag {
	#inputEl;
	#params;
	#tagify;
	#abortController = null;
	#debounceTimer = null;
	static _csrfToken = null;

	constructor(element, params = {}) {
		this.#inputEl = element;
		this.#params = {
			route: null,
			valuesdataid: 'tags',
			name: element.name,
			free: false,
			limit: 0,
			...params,
		};

		this.#inputEl.name = '';

		const script = document.querySelector(`script[data-id="${this.#params.valuesdataid}"]`);
		this.initialValues = parseJsonLd(script);
		script.remove();

		const settings = {
			duplicates: false,
			enforceWhitelist: !this.#params.free,
			whitelist: this.initialValues,
			autocomplete: true,
			addTagOnBlur: false,
			userInput: true,
			editTags: false,
			dropdown: {
				fuzzySearch: true,
				enabled: true,
			},
		};

		if (this.#params.limit && Number(this.#params.limit) > 0) {
			settings.maxTags = Number(this.#params.limit);
		}

		// Initialize Tagify
		this.#tagify = new Tagify(this.#inputEl, settings);

		this.#tagify.addTags(this.initialValues);

		if (!Tag._csrfToken) {
			Tag._csrfToken =
				document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
		}

		if (this.#params.route) {
			this.#tagify.on('input', this.#onInput.bind(this));
		}

		this.#tagify.on('change', () => this.#updateHiddenInputs());

		this.#updateHiddenInputs();
	}

	// Debounced input handler
	async #onInput(e) {
		const value = e.detail.value.trim();

		if (!value) {
			this.#tagify.dropdown.hide();
			return;
		}

		this.#abortController?.abort();
		this.#abortController = new AbortController();

		this.#tagify.settings.whitelist = [];
		this.#tagify.loading(true).dropdown.hide();

		const url = `${parseRoute(this.#params.route)}?query=${encodeURIComponent(value)}`;

		try {
			const res = await fetch(url, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': Tag._csrfToken,
				},
				signal: this.#abortController.signal,
			});
			if (!res.ok) {
				throw new Error(`HTTP ${res.status}`);
			}
			const { suggestions = [] } = await res.json();

			// Update dropdown
			this.#tagify.settings.whitelist = suggestions;
			this.#tagify.loading(false).dropdown.show(value);
		} catch (err) {
			if (err.name !== 'AbortError') {
				console.error('Tagify fetch error:', err);
				this.#tagify.loading(false).dropdown.hide();
			}
		}
	}

	// Get current Tagify value
	get values() {
		return this.#tagify.value; // Array of {value, id}
	}

	// Rebuild hidden inputs after any change
	#updateHiddenInputs() {
		// Remove old hidden fields
		document
			.querySelectorAll(`input[type="hidden"][data-id="${this.#params.valuesdataid}"]`)
			.forEach((el) => el.remove());

		// Add new ones in reverse to preserve order
		this.values
			.slice()
			.reverse()
			.forEach((tag) => {
				const input = document.createElement('input');

				input.type = 'hidden';
				input.name = `${this.#params.name}[]`;
				input.dataset.id = this.#params.valuesdataid;
				input.value = this.#params.free
					? tag.id
						? `${tag.id}|`
						: `0|${tag.value}`
					: tag.id;
				this.#inputEl.after(input);
			});
	}

	// Expose a destroy method to clean up
	destroy() {
		clearTimeout(this.#debounceTimer);
		this.#abortController?.abort();
		this.#tagify.destroy();
	}
}

