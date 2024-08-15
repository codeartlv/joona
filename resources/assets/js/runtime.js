import Components from './handlers/components.js';
import Admin from './handlers/admin.js';
import { parseJsonLd } from './helpers.js';
import Lang from 'lang.js';

export default class Runtime {
	handlers = {};
	instances = {};
	locale = null;

	constructor() {}

	ready() {
		return new Promise((resolve, reject) => {
			this.setupEventListeners();

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', () => {
					this.initializeApplication(resolve, reject);
				});
			} else {
				this.initializeApplication(resolve, reject);
			}
		});
	}

	initializeApplication(resolve) {
		this.locale = document.documentElement.getAttribute('lang');

		let translationString = parseJsonLd(
			document.querySelector('head script[data-role="js-translations"]')
		);

		// Initialize translations
		let lang = new Lang({
			messages: translationString || {},
			locale: this.locale,
			fallback: this.locale,
		});

		window.trans = function (keyword, args, locale) {
			return lang.get(keyword, args, locale);
		};

		window.choice = function (keyword, count, args, locale) {
			return lang.transChoice(keyword, count, args, locale);
		};

		window.locale = this.locale;

		this.addHandlers(Components, Admin);

		this.init(document.body);

		resolve();
	}

	setupEventListeners() {
		window.addEventListener('resize', () => {
			// Add viewport height variable to document. This property allows to
			// precisely set viewport height, accounting for virtual keyboards on
			// mobile devices.

			var vh = window.innerHeight * 0.01;
			document.documentElement.style.setProperty('--vh', `${vh}px`);

			// Save window scrollbar width to be used in CSS.
			var scrollWidth = Math.ceil(
				(window.innerWidth - document.documentElement.clientWidth) / 2
			);
			document.documentElement.style.setProperty('--body-scroll-width', `${scrollWidth}px`);
		});

		window.dispatchEvent(new Event('resize'));

		window.addEventListener('scroll', () => {
			// Save true window scroll position to be used in CSS.
			document.documentElement.style.setProperty(
				'--body-scroll-position',
				`${window.scrollY}px`
			);
		});
		window.dispatchEvent(new Event('scroll'));
	}

	// Add module handlers
	addHandlers(...handlers) {
		handlers.forEach((handler) => {
			this.handlers[handler.pluginName.toLowerCase()] = new handler();
		});
	}

	// Convert data- attributes with additional type conversion
	convertValue(value) {
		if (value === 'true') {
			return true;
		}

		if (value === 'false') {
			return false;
		}

		if (!isNaN(value) && value.trim() !== '') {
			return Number(value);
		}

		return value;
	}

	// Collects data- attributes from element
	getParams(context, tag) {
		let parameters = {};
		tag = tag || 'bind';

		Array.from(context.attributes).forEach((attr) => {
			if (attr.name.startsWith('data-')) {
				let attributeValue = attr.name.slice(5);

				if (attributeValue !== tag) {
					parameters[attributeValue] = this.convertValue(attr.value);
				}
			}
		});

		return parameters;
	}

	getInstanceById(id) {
		return new Promise((resolve, reject) => {
			let element = document.getElementById(id);

			if (!element) {
				reject(new Error(`Element with ID #${id} not found.`));
				return;
			}

			return this.resolveInstance(element, resolve, reject);
		});
	}

	// Returns first instance of element
	getInstance(element, module, filter) {
		return new Promise((resolve, reject) => {
			this.getInstances(element, module, filter)
				.then((instances) => {
					if (instances.length > 0) {
						resolve(instances[0]);
					} else {
						reject(new Error('No instances found'));
					}
				})
				.catch((error) => {
					reject(error);
				});
		});
	}

	resolveInstance(element, resolve, reject) {
		let instance = this.instances[element.dataset._elementId];

		if (instance !== undefined) {
			resolve({ instance, element: element });
			return;
		}

		let tries = 0;
		const maxTries = 50;

		const interval = setInterval(() => {
			instance = this.instances[element.dataset._elementId];

			if (instance !== undefined || tries > maxTries) {
				clearInterval(interval);

				if (instance !== undefined) {
					resolve({ instance, element: element });
				} else {
					reject(new Error('Instance not found for element'));
				}
			}
			tries++;
		}, 20);
	}

	// Returns all instances of module
	getInstances(element, module, filter) {
		const foundElements = this.getElement(element, module, filter);

		const resolutions = Array.from(foundElements).map((element) => {
			return new Promise((resolve, reject) => {
				this.resolveInstance(element, resolve, reject);
			});
		});

		return Promise.all(resolutions);
	}

	getElement(context, func, filter = '') {
		if (context.dataset.bind === func) {
			return [context];
		}

		return context.querySelectorAll(`${filter}[data-bind="${func}"]`);
	}

	getElements(context, tag) {
		let callable = context.dataset[tag];

		if (!callable) {
			throw new Error(`No data-${tag} attribute found`);
		}

		callable = callable.replace(/(\-\w)/g, (attribute) => {
			return attribute[1].toUpperCase();
		});

		let parts = callable.split('.');
		let action = parts[1];
		let module = parts[0];

		let data = this.getParams(context, tag);

		return {
			module: module,
			action: action,
			data: data,
		};
	}

	init(context) {
		context = context || document.body;

		const bindSingle = (domElement) => {
			if (domElement.dataset.binded) {
				return;
			}

			const instance = this.getElements(domElement, 'bind');
			const { module: module, action: action } = instance;

			if (!action) {
				return;
			}

			const actionHandler = this.handlers[module]?.[action];

			if (typeof actionHandler === 'function') {
				domElement.dataset.binded = true;

				const result = actionHandler(domElement, instance.data, this);
				domElement.dataset._elementId = `el${Math.random().toString(36).substring(2, 15)}`;

				if (result && typeof result.then === 'function') {
					result.then(
						(instance) => (this.instances[domElement.dataset._elementId] = instance)
					);
				} else if (result) {
					this.instances[domElement.dataset._elementId] = result;
				}
			}
		};

		context.querySelectorAll('[data-bind]').forEach(bindSingle);
	}
}
