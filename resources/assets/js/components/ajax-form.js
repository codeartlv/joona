import { addSpinner, removeSpinner, setButtonLoading, unsetButtonLoading } from '../helpers';

import FormHandler from './form-handler.js';

export default class AjaxForm {
	handler = null;
	form = null;
	submitButton = null;
	settings = {};

	constructor(formElement, settings) {
		this.settings = {
			overlay: 'button',
			focus: null,
			...settings,
		};

		this.form = formElement;

		this.eventListeners = new Map();

		this.handler = new FormHandler(this.form, settings);

		this.handler.bindEvent('start', () => this.onStart());
		this.handler.bindEvent('complete', () => this.onComplete());
		this.handler.bindEvent('loaded', (response) => this.onSuccess(response));
		this.handler.bindEvent('failed', (status, message) => this.onError(status, message));

		let button = this.form.querySelector('button[type="submit"]');

		let formId = formElement.getAttribute('id');

		if (!button && formId) {
			button = document.querySelector(`button[form=${formId}]`);
		}
		
		if (button) {
			this.submitButton = button;
		} else if (this.settings.overlay == 'button') {
			this.settings.overlay = 'overlay';
		}

		if (this.settings.focus) {
			let field = this.form.querySelector(`[name="${this.settings.focus}"]`);

			if (field) {
				field.focus();
			}
		}
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

	onStart() {
		this.resetState();

		this.form.classList.add('loading');
		this.form.classList.add(`loading-${this.settings.overlay}`);

		if (this.settings.overlay == 'button') {
			setButtonLoading(this.handler.submitter);
		} else {
			addSpinner(this.form);
		}
	}

	onComplete() {
		this.form.classList.remove('loading');
		this.form.classList.remove(`loading-${this.settings.overlay}`);

		if (this.settings.overlay == 'button') {
			unsetButtonLoading(this.handler.submitter);
		} else {
			removeSpinner(this.form);
		}
	}

	onSuccess(response) {
		response = {
			status: 'error',
			fields: {},
			...response,
		};

		if (response.status == 'error') {
			this.showError(response);
			this.trigger('error', response);
		}

		if (response.status == 'success') {
			this.showSuccess(response);
			this.trigger('success', response);
		}
	}

	onError(status, message) {
		this.showError({
			status: 'error',
			fields: {
				'*': [`${status}: ${message}`],
			},
		});
	}

	getMessageContainer() {
		let container = this.form.querySelector('[data-role="form.response"]');

		if (!container) {
			container = document.createElement('div');
			container.dataset.role = 'form.response';
			this.form.prepend(container);
		}

		return container;
	}

	resetState() {
		let container = this.getMessageContainer();
		container.classList.remove(...container.classList);
		container.innerHTML = '';

		let feedbacks = this.form.querySelectorAll('.is-invalid');

		if (feedbacks) {
			feedbacks.forEach((element) => {
				element.classList.remove('is-invalid');
			});
		}

		let messageContainers = this.form.querySelectorAll(
			'.invalid-feedback:not(.custom-feedback)'
		);

		if (messageContainers) {
			messageContainers.forEach((element) => {
				element.remove();
			});
		}

		this.form.querySelectorAll('.invalid-feedback.custom-feedback').forEach((element) => {
			element.classList.remove('invalid-feedback');
		});
	}

	executeActions(response) {
		response = {
			actions: {},
			...response,
		};

		for (var action in response.actions) {
			var subject = response.actions[action];

			switch (action) {
				case 'redirect':
					document.location = subject;
					break;
				case 'reset':
					this.form.reset();
					break;
				case 'reload':
					document.location = document.location;
					break;
				case 'close_popup':
					if (window.JoonaModalInstance) {
						window.JoonaModalInstance.close();
					}
					break;
			}
		}
	}

	showSuccess(response) {
		response = {
			message: '',
			...response,
		};

		let container = this.getMessageContainer();
		container.classList.remove(...container.classList);
		container.classList.add('alert', 'alert-success');
		container.innerHTML = response.message;

		this.executeActions(response);
	}

	showError(response) {
		const globalMessages = [];

		for (let field in response.fields) {
			const messages = response.fields[field];

			if (field == '*' || !field) {
				globalMessages.push(messages.join('<br />'));
				continue;
			}

			let input = this.findControl(field);

			if (input.control) {
				input.control.classList.add('is-invalid');
				input.messages.innerHTML = messages.join('<br />');
			} else {
				globalMessages.push(messages.join('<br />'));
			}
		}

		if (globalMessages.length) {
			let container = this.getMessageContainer();
			container.classList.add('alert', 'alert-danger');
			container.innerHTML = globalMessages.join('<br />');
		}
	}

	findControl(name) {
		let messageContainer = null;
		let inputControl = this.form.querySelector(`[name="${name}"]:not([type="hidden"])`);

		if (!inputControl) {
			inputControl = this.form.querySelector(`[data-field="${name}"]`);
		}

		if (inputControl) {
			messageContainer = this.form.querySelector(`[data-field-message="${name}"]`);

			if (!messageContainer) {
				messageContainer = document.createElement('div');
				inputControl.after(messageContainer);
			} else {
				messageContainer.classList.add('custom-feedback');
			}

			messageContainer.classList.add('invalid-feedback');
		}

		return {
			control: inputControl,
			messages: messageContainer,
		};
	}
}
