import BootstrapOffcanvas from 'bootstrap/js/dist/offcanvas';
import axios from 'axios';
import { addSpinner } from '../helpers';
import DataStore from '../store';

export default class Offcanvas {
	constructor(id, settings) {
		this.settings = {
			backdrop: true,
			scroll: false,
			position: 'end',
			...(settings || {}),
		};

		this.id = id;
		this.currentUrl = null;
		this.offcanvasInstance = null;
	}

	static get(id) {
		return DataStore.getData(`offcanvas_${id}`);
	}

	static getByUrl(url) {
		return DataStore.getData(`offcanvas_url_${url}`);
	}

	static getNextZIndexes() {
		const openOffcanvases = document.querySelectorAll('.offcanvas.show');
		const baseZ = 1050;

		if (openOffcanvases.length === 0) {
			return {
				elZ: baseZ,
				backdropZ: baseZ - 10,
			};
		}

		let highestZ = baseZ;

		openOffcanvases.forEach((el) => {
			const z = parseInt(window.getComputedStyle(el).zIndex);

			if (z > highestZ) {
				highestZ = z;
			}
		});

		return {
			backdropZ: highestZ + 1,
			elZ: highestZ + 2,
		};
	}

	reload(url) {
		const newUrl = url ? url : this.currentUrl;

		const offcanvasEl = document.querySelector(`.offcanvas--${this.id}`);

		if (!offcanvasEl) {
			this.open(newUrl);
			return;
		}

		const offcanvasBody = offcanvasEl.querySelector('.offcanvas-body');

		offcanvasEl.classList.add('offcanvas-loading');
		offcanvasBody.innerHTML = '';
		addSpinner(offcanvasBody, 'secondary');

		this.load(newUrl);
	}

	load(url) {
		this.currentUrl = url;

		return new Promise((resolve, reject) => {
			const offcanvasEl = document.querySelector(`.offcanvas--${this.id}`);

			if (!offcanvasEl) {
				console.warn(`Offcanvas with ID ${this.id} is not currently opened.`);
				return;
			}

			axios
				.get(url)
				.then((response) => {
					offcanvasEl.innerHTML = response.data;
					resolve(offcanvasEl);

					window.Joona.init(offcanvasEl);
					offcanvasEl.classList.remove('offcanvas-loading');

					let openEvent = new CustomEvent('joona:contentLoad', {
						detail: {
							source: 'offcanvas',
							element: offcanvasEl,
						},
					});

					document.dispatchEvent(openEvent);

					DataStore.setData(`offcanvas_${this.id}`, this);
				})
				.catch((e) => {
					if (!e.response) {
						return;
					}

					let message =
						'message' in e.response.data ? e.response.data.message : e.message;

					offcanvasEl.innerHTML = `
						<div class="offcanvas-header">
							<h5 class="offcanvas-title">${trans('joona::common.error')}</h5>
							<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
								<i class="material-symbols-outlined">close</i>
							</button>
						</div>
						<div class="offcanvas-body">
							<div class="alert alert-danger">${message}</div>
						</div>
					`;
					reject(e);
				});
		});
	}

	async open(url) {
		this.currentUrl = url;

		const existingInstance = Offcanvas.getByUrl(url);

		if (existingInstance) {
			await new Promise((resolve) => {
				const el = document.querySelector(`.offcanvas--${existingInstance.id}`);
				if (el) {
					el.addEventListener('hidden.bs.offcanvas', () => resolve(), { once: true });
					existingInstance.offcanvasInstance.hide();
				} else {
					resolve();
				}
			});
		}

		return new Promise((resolve, reject) => {
			const { elZ, backdropZ } = Offcanvas.getNextZIndexes();

			const offcanvasEl = document.createElement('div');
			offcanvasEl.classList.add(
				'offcanvas',
				'offcanvas-loading',
				`offcanvas-${this.settings.position}`,
				`offcanvas--${this.id}`,
			);

			// Apply the calculated Z-Index to the element
			offcanvasEl.style.zIndex = elZ;

			if (this.settings.scroll) offcanvasEl.dataset.bsScroll = 'true';
			if (this.settings.backdrop === 'static') {
				offcanvasEl.dataset.bsBackdrop = 'static';
				offcanvasEl.dataset.bsKeyboard = 'false';
			}

			offcanvasEl.innerHTML = `
            <div class="offcanvas-header">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="material-symbols-outlined">close</i>
                </button>
            </div>
            <div class="offcanvas-body"></div>`;

			document.body.append(offcanvasEl);

			const body = offcanvasEl.querySelector('.offcanvas-body');
			addSpinner(body, 'secondary');

			offcanvasEl.addEventListener('show.bs.offcanvas', () => {
				setTimeout(() => {
					const backdrops = document.querySelectorAll('.offcanvas-backdrop.show');
					if (backdrops.length > 0) {
						const currentBackdrop = backdrops[backdrops.length - 1];
						currentBackdrop.style.zIndex = backdropZ;
					}
				}, 10);
			});

			offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
				if (this.offcanvasInstance) {
					this.offcanvasInstance.dispose();
					this.offcanvasInstance = null;
				}

				offcanvasEl.remove();
				DataStore.clearData(`offcanvas_${this.id}`);
				DataStore.clearData(`offcanvas_url_${this.currentUrl}`);
			});

			this.offcanvasInstance = new BootstrapOffcanvas(offcanvasEl, this.settings);

			DataStore.setData(`offcanvas_${this.id}`, this);
			DataStore.setData(`offcanvas_url_${this.currentUrl}`, this);

			this.offcanvasInstance.show();

			this.load(url)
				.then(() => resolve(offcanvasEl))
				.catch((e) => reject(e));
		});
	}
}

