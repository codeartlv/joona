import axios from 'axios';
import BootstrapModal from 'bootstrap/js/dist/modal';
import { addSpinner, removeSpinner } from '../helpers';

export default class Modal {
	constructor(id) {
		this.eventListeners = new Map();
		this.id = id;
	}

	on(event, callback) {
		if (!this.eventListeners.has(event)) {
			this.eventListeners.set(event, []);
		}

		this.eventListeners.get(event).push(callback);
	}

	trigger(event, ...args) {
		const listeners = this.eventListeners.get(event);

		if (listeners && listeners.length) {
			listeners.forEach((listener) => listener(...args));
		}
	}

	close() {
		return new Promise((resolve, reject) => {
			if (!this.modalInstance) {
				reject();
				return;
			}

			this.modalInstance._element.addEventListener('hidden.bs.modal', resolve);
			this.modalInstance.hide();
		});
	}

	open(urlOrElement, options) {
		options = {
			animations: true,
			...options,
		};

		return new Promise((resolve, reject) => {
			let processOpen = () => {
				window.JoonaModalInstance = this;
				this.trigger('open');

				let modalEl;
				let modalDialogEl;
				let modalDialogContent;

				if (typeof urlOrElement == 'string') {
					modalEl = document.createElement('div');

					if (options.animations) {
						modalEl.classList.add('modal', 'fade');
					}

					modalEl.dataset.tabindex = -1;

					modalDialogEl = document.createElement('div');
					modalDialogEl.classList.add(
						'modal-dialog',
						'modal-dialog-centered',
						'modal-dialog-scrollable'
					);

					if (this.id) {
						modalDialogEl.classList.add(`modal-dialog--${this.id}`);
					}

					modalEl.appendChild(modalDialogEl);

					document.body.appendChild(modalEl);
				} else {
					modalEl = urlOrElement;
				}

				this.modalInstance = new BootstrapModal(modalEl, {
					backdrop: 'static',
				});

				modalEl.addEventListener('show.bs.modal', () => {
					const backdropEl = this.modalInstance._backdrop._getElement();
					addSpinner(backdropEl, 'light');
				});

				modalEl.addEventListener('shown.bs.modal', () => {
					const backdropEl = this.modalInstance._backdrop._getElement();
					removeSpinner(backdropEl);

					if (modalDialogContent) {
						modalDialogEl.innerHTML = modalDialogContent;
					}

					window.Joona.init(modalEl);

					let openEvent = new CustomEvent('joona:contentLoad', {
						detail: {
							source: 'modal',
							element: modalEl,
						},
					});

					document.dispatchEvent(openEvent);

					resolve(modalEl);
				});

				modalEl.addEventListener('hidden.bs.modal', () => {
					this.trigger('close');

					this.modalInstance.dispose();
					window.JoonaModalInstance = null;
					modalEl.remove();
				});

				if (typeof urlOrElement == 'string') {
					axios
						.get(urlOrElement)
						.then((response) => {
							modalDialogContent = response.data;

							this.modalInstance.show();
						})
						.catch((e) => {
							let message =
								'message' in e.response.data ? e.response.data.message : e.message;

							modalDialogEl.innerHTML = `
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">${trans('joona::common.error')}</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<i class="material-symbols-outlined">close</i>
								</button>
							</div>
							<div class="modal-body">
								<div class="modal-inner">
									<div class="alert alert-danger mb-0">${message}</div>
								</div>
							</div>
						</div>
						`;

							this.modalInstance.show();
							reject();
						});
				} else {
					this.modalInstance.show();
				}
			};

			if (window.JoonaModalInstance) {
				window.JoonaModalInstance.close().then(processOpen);
			} else {
				processOpen();
			}
		});
	}
}
