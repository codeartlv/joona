import { parseUrl } from './helpers';

export default class Runtime {
	handlers = {};
	routes = {};
	translations = {};

	constructor() {
		window.JoonaPluginInstances = window.JoonaPluginInstances || new Map();
		this.instances = window.JoonaPluginInstances;
	}

	// Add module handlers
	addHandlers(...handlers) {
		handlers.forEach((handler) => {
			this.handlers[handler.pluginName.toLowerCase()] = new handler();
		});
	}

	addRoutes(routes) {
		this.routes = { ...this.routes, ...routes };
	}

	addTranslations(keywords) {
		this.translations = { ...this.translations, ...keywords };
	}

	getRoute(name, parameters) {
		parameters = { ...parameters };

		if (!this.routes[name]) {
			console.warn(`Route "${name}" not found`);
			return;
		}

		return parseUrl(this.routes[name], parameters);
	}

	lang(keyword, args) {
		args = args || {};

		if (typeof this.translations[keyword] == 'undefined') {
			return keyword;
		}

		let value = String(this.translations[keyword]);

		return value.replace(/:([a-zA-Z0-9_]+)/g, (match, key) => {
			return args.hasOwnProperty(key) ? args[key] : match;
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

	// Returns all instances of module
	getInstances(element, module, filter) {
		const foundElements = this.getElement(element, module, filter);

		const resolutions = Array.from(foundElements).map((element) => {
			return new Promise((resolve, reject) => {
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
				domElement.dataset._elementId = `el${Math.random()
					.toString(36)
					.substring(2, 15)}`;

				if (result && typeof result.then === 'function') {
					result.then(
						(instance) =>
							(this.instances[domElement.dataset._elementId] =
								instance)
					);
				} else if (result) {
					this.instances[domElement.dataset._elementId] = result;
				}
			}
		};

		context.querySelectorAll('[data-bind]').forEach(bindSingle);
	}
}
