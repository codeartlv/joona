export default class PasswordValidator {
	constructor(el, params) {
		params = {
			policy: '',
			...params,
		};

		this.container = el;

		this.passwordInput = el.querySelector('[data-role="password-validator.password-input"]');

		this.passwordProgress = el.querySelector('[data-role="password-validator.progress"]');

		this.passwordSteps = el.querySelectorAll('[data-step]');

		this.visbilityToggle = el.querySelector(
			'[data-role="password-validator.toggle-visbility"]'
		);

		this.eventListeners = new Map();

		this.init();

		const policy = {};

		const rules = params.policy ? params.policy.split(',') : [];

		for (let rule of rules) {
			const [ruleName, ruleValue] = rule.split(':');

			switch (ruleName) {
				case 'min':
					var value = ruleValue ? Number(ruleValue) : 0;

					if (value) {
						policy.min = value;
					}
					break;
				case 'max':
					var value = ruleValue ? Number(ruleValue) : 0;

					if (value) {
						policy.max = value;
					}

					break;
				case 'uppercase':
					policy.uppercase = true;
					break;
				case 'mixed':
					policy.mixed = true;
					break;
				case 'lowercase':
					policy.lowercase = true;
					break;
				case 'number':
					policy.number = true;
					break;
				case 'special':
					policy.special = true;
					break;
			}

			let step = el.querySelector(`[data-step="${ruleName}"]`);

			if (step) {
				step.classList.add('requires');
			}
		}

		if (rules.length > 0) {
			el.classList.add('has-policy');
		}

		this.policy = policy;
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

	init() {
		this.visbilityToggle.addEventListener('click', () => {
			this.container.classList.toggle('revealed');
			this.passwordInput.type = this.passwordInput.type === 'text' ? 'password' : 'text';
		});

		this.passwordInput.addEventListener('keyup', () => {
			this.checkPassword();
		});

		this.passwordInput.addEventListener('paste', () => {
			this.checkPassword();
		});
	}

	checkPassword() {
		const password = this.passwordInput.value;
		const length = Array.from(password).length;

		// Initialize state with policy keys and set all to false
		const state = Object.keys(this.policy).reduce((acc, key) => ({ ...acc, [key]: false }), {});

		// Check each policy condition
		if (this.policy.min && length >= this.policy.min) {
			state.min = true;
		}

		if (this.policy.max && length > this.policy.max) {
			state.max = true;
		}

		if (this.policy.number) {
			state.number = /[0-9]/.test(password);
		}

		if (this.policy.special) {
			state.special = /(?=.*[^\p{L}\p{N}])/u.test(password);
		}

		if (this.policy.mixed) {
			state.mixed = /(?=.*\p{Ll})(?=.*\p{Lu})/u.test(password);
		}

		if (this.policy.lowercase) {
			state.lowercase = /\p{Ll}/u.test(password);
		}

		if (this.policy.uppercase) {
			state.uppercase = /\p{Lu}/u.test(password);
		}

		// Update UI elements based on the state
		this.updateUI(state, password);
	}

	updateUI(state, password) {
		let perc = 0;

		if (state.max) {
			perc = 100;
		}

		delete state.max;

		let progress = Object.values(state).filter(Boolean).length;
		let total = Object.keys(state).length;
		perc = (progress / total) * 100;

		this.passwordSteps.forEach((element) => {
			element.classList.toggle('complete', state[element.getAttribute('data-step')]);
		});

		let isLong = password.length >= this.policy.max;

		this.container.classList.toggle('long-password', isLong);

		if (isLong) {
			perc = 100;
		}

		this.passwordProgress.style.width = `${perc}%`;
		this.updateProgressClass(perc);
	}

	updateProgressClass(perc) {
		const progressClass =
			perc >= 100 ? 'success' : perc >= 66 ? 'warning' : perc >= 33 ? 'danger' : 'danger';
		this.passwordProgress.classList.remove('danger', 'warning', 'success');

		if (progressClass) {
			this.passwordProgress.classList.add(progressClass);
		}

		const parentElement = this.passwordProgress.parentElement;
		parentElement.classList.toggle('is_filled', perc > 0);
	}
}
