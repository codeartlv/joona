import BootstrapOffcanvas from 'bootstrap/js/dist/offcanvas';
import axios from 'axios';
import { addSpinner } from '../helpers';
import DataStore from '../store';

export default class Offcanvas {
	constructor(id, settings) {
		this.settings = {
			backdrop: true,
			...(settings || {}),
		};

		this.id = id;
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

	static get(id) {
		return DataStore.getData(`offcanvas_${id}`);
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
					console.error(e);

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

	open(url, options) {
		options = {
			backdropClose: true,
			bodyScroll: false,
			position: 'end',
			...options,
		};

		return new Promise((resolve, reject) => {
			const offcanvasEl = document.createElement('div');
			offcanvasEl.classList.add(
				'offcanvas',
				'offcanvas-loading',
				`offcanvas-${options.position}`,
				`offcanvas--${this.id}`
			);

			if (options.bodyScroll) {
				offcanvasEl.dataset.bsScroll = 'true';
			}

			if (!options.backdropClose) {
				offcanvasEl.dataset.bsBackdrop = 'static';
			}

			offcanvasEl.innerHTML = `
			<div class="offcanvas-header">
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="material-symbols-outlined">close</i>
				</button>
			</div>
			<div class="offcanvas-body">

			</div>`;

			document.body.append(offcanvasEl);

			const body = offcanvasEl.querySelector('.offcanvas-body');

			addSpinner(body, 'secondary');

			offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
				this.offcanvasInstance.dispose();
				offcanvasEl.remove();

				DataStore.clearData(`offcanvas_${this.id}`);
			});

			this.offcanvasInstance = new BootstrapOffcanvas(offcanvasEl, this.settings);
			this.offcanvasInstance.show();

			this.load(url)
				.then(() => {
					resolve(offcanvasEl);
				})
				.catch(() => {
					reject();
				});
		});
	}
}
