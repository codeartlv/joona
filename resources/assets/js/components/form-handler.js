export default class FormHandler {
	form = null;
	state = null;
	request = null;
	events = {
		start: [],
		complete: [],
		loaded: [],
		failed: [],
		progress: [],
	};

	constructor(formElement, settings) {
		this.form = formElement;

		this.settings = {
			...settings,
		};

		this.form.addEventListener('submit', (e) => {
			e.preventDefault();

			this.submit();
			return false;
		});
	}

	bindEvent(event, callback) {
		this.events[event].push(callback);
	}

	dispatchEvent(event, ...data) {
		if (this.events[event] && this.events[event].length) {
			for (let fn of this.events[event]) {
				fn.apply(this, data);
			}
		}
	}

	started() {
		this.state = 'loading';
		this.dispatchEvent('start');
	}

	ended() {
		if (this.state !== 'success' && this.state !== 'error') {
			this.state = 'idle';
		}

		this.request = null;
		this.dispatchEvent('complete');
	}

	failed(status, message) {
		this.state = 'error';
		this.dispatchEvent('failed', status, message);
	}

	succeeded(response) {
		this.state = 'success';
		this.dispatchEvent('loaded', response);
	}

	progress(percent) {
		this.dispatchEvent('progress', percent);
	}

	send(method, url, formData) {
		return new Promise((resolve, reject) => {
			this.request = new XMLHttpRequest();
			this.request.open(method, url, true);

			this.request.addEventListener('loadstart', () => this.started());

			this.request.onload = () => {
				if (this.request.status >= 200 && this.request.status < 300) {
					try {
						const jsonResponse = JSON.parse(this.request.responseText);
						resolve(jsonResponse);
					} catch (e) {
						reject(`Failed to parse JSON response: ${e}`);
					}
				} else {
					reject(this.request.statusText);
				}
			};

			this.request.onerror = () => reject(this.request.statusText || 'Unknown error');
			this.request.ontimeout = () => reject('Timeout');
			this.request.onabort = () => reject('Aborted');

			this.request.send(formData);
		});
	}

	async submit() {
		if (this.state === 'loading' && this.request) {
			this.request.abort();
		}

		const formData = new FormData(this.form);
		const method = this.form.method || 'POST';
		const url = this.form.action || '';

		try {
			const response = await this.send(method, url, formData);
			this.succeeded(response);
		} catch (error) {
			this.failed(this.request.status || 0, error);
		} finally {
			this.ended();
		}
	}
}
