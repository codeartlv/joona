class DataStore {
	constructor() {
		if (!DataStore.instance) {
			this.data = {};
			DataStore.instance = this;
		}
		return DataStore.instance;
	}

	setData(key, value) {
		this.data[key] = value;
	}

	getData(key) {
		return this.data[key];
	}

	clearData(key) {
		delete this.data[key];
	}
}

const instance = new DataStore();
Object.freeze(instance);

export default instance;
