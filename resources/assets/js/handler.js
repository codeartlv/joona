export default class Handler {
	constructor() {
		if (this.constructor === Handler) {
			throw new Error('Handler is an abstract class and cannot be instantiated directly.');
		}
	}

	static get pluginName() {
		throw new Error("Plugin must implement 'pluginName' getter");
	}
}
