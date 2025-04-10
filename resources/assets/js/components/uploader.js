import { addSpinner, removeSpinner, parseJsonLd, parseRoute } from './../helpers';

export default class Uploader {
	uploadCounter = 0;
	events = {
		queueChange: [],
		beforeFileSelect: [],
	};

	constructor(element, params) {
		this.params = {
			uploadroute: '',
			deleteroute: '',
			submitbtn: '',
			limit: 1,
			list: null,
			name: '',
			fileOptions: {
				remove: {
					caption: trans('joona::common.delete'),
					icon: 'delete',
					callback: (id, thumbnail) => {
						this.deleteFile(id, thumbnail);
					},
				},
			},
			...params,
		};

		this.eventListeners = new Map();

		this.element = element;
		this.fileSelector = this.element.querySelector('input[type="file"]');
		this.submitButton = this.params.submitbtn
			? document.querySelector(this.params.submitbtn)
			: null;

		if (!this.fileSelector) {
			console.error('File input not found.');
			return;
		}

		if (this.params.limit > 1) {
			this.fileSelector.multiple = true;
		}

		if (this.params.list) {
			this.list = document.querySelector(this.params.list);
		}

		if (!this.list) {
			this.list = this.element;
		}

		this.triggerEl = element.querySelector('[data-role="trigger"]');

		this.dataField = document.createElement('input');
		this.dataField.type = 'hidden';
		this.dataField.name = this.fileSelector.name;

		this.fileSelector.setAttribute('name', '');
		this.fileSelector.parentNode.insertBefore(this.dataField, this.fileSelector.nextSibling);

		this.initUploader();

		let images = parseJsonLd(this.element.querySelector('[data-role="data"]'));

		if (images.length) {
			this.setImages(images);
			this.syncIds();
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

	initUploader() {
		this.fileSelector.addEventListener('change', () => {
			this.onFileSelect(this.fileSelector.files);
			this.fileSelector.value = '';
		});

		let handleDrag = (e) => {
			e.preventDefault();
			e.stopPropagation();
			if (e.type === 'dragenter') {
				this.element.classList.add('file-drag');
			}
		};

		let handleDragLeave = (e) => {
			e.preventDefault();
			e.stopPropagation();
			this.element.classList.remove('file-drag');
		};

		let handleDrop = (e) => {
			e.preventDefault();
			e.stopPropagation();

			let dataTransfer = e.dataTransfer || e.originalEvent.dataTransfer;
			let files = dataTransfer.files;

			if (files.length) {
				for (let i = 0; i < files.length; i++) {
					let allowed = this.getAllowedFileCount();

					if (allowed > 0) {
						this.uploadFile(files[i]);
					}
				}
			}
		};

		this.element.addEventListener('dragenter', handleDrag);
		this.element.addEventListener('dragover', handleDrag);
		this.element.addEventListener('dragleave', handleDragLeave);
		this.element.addEventListener('drop', handleDrop);

		this.syncIds();
	}

	getAllowedFileCount() {
		var allowedFiles = -1;
		var limit = Number(this.params.limit);

		if (limit > 0) {
			return this.params.limit - this.uploadCounter;
		}

		return allowedFiles;
	}

	// Copies file ID attributes to hidden input field
	syncIds() {
		let ids = [];
		let files = this.element.querySelectorAll('.upload-file');

		if (files.length) {
			files.forEach(function (file) {
				let id = Number(file.getAttribute('data-id'));
				if (!isNaN(id) && id > 0) {
					ids.push(id);
				}
			});

			this.dataField.value = ids.join(',');
		} else {
			this.dataField.value = '';
		}
	}

	// Removes all uploads
	clear() {
		this.dataField.value = '';

		let uploadFiles = this.list.querySelectorAll('.upload-file');
		uploadFiles.forEach(function (file) {
			file.remove();
		});

		this.trigger('queueChange');
	}

	// Gets called when file is selected via dialog
	onFileSelect(files) {
		for (var i = 0; i < files.length; i++) {
			this.uploadFile(files[i]);
		}
	}

	onComplete() {
		if (this.submitButton) {
			this.submitButton.disabled = false;
		}
	}

	checkLimits() {
		if (this.params.limit <= 0) {
			return;
		}

		if (this.uploadCounter >= this.params.limit) {
			this.triggerEl.classList.add('hidden');
		} else {
			this.triggerEl.classList.remove('hidden');
		}
	}

	// Generates new file thumbnail
	createThumbnail() {
		let template = this.element.querySelector('template[data-role="thumbnail"]');

		let thumbnail = template.content.cloneNode(true);

		let dropdownMenu = thumbnail.querySelector('[data-role="menu"]');
		var optionHtml = '';

		for (var fileOption in this.params.fileOptions) {
			var option = this.params.fileOptions[fileOption];

			optionHtml += `
			<a class="dropdown-item" data-action="${fileOption}">
				<i class="material-symbols-outlined">${option.icon}</i>
				${option.caption}
			</a>
			`;
		}

		dropdownMenu.innerHTML = optionHtml;

		return thumbnail.querySelector(':first-child');
	}

	// Binds interaction to file thumbnail
	bindEvents(thumbnail) {
		var opts = thumbnail.querySelectorAll('.dropdown-menu [data-action]');

		opts.forEach((option) => {
			option.addEventListener('click', () => {
				let action = this.params.fileOptions[option.dataset.action];
				action.callback(thumbnail.dataset.id, thumbnail);
			});
		});
	}

	deleteFile(id, thumbnail) {
		if (id) {
			if (this.params.deleteroute.length > 0) {
				let url = parseRoute(this.params.deleteroute);

				let csrfToken = document
					.querySelector('meta[name="csrf-token"]')
					.getAttribute('content');

				fetch(url, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': csrfToken,
					},
					body: JSON.stringify({
						id: id,
					}),
				});
			}
		}

		this.uploadCounter--;
		this.checkLimits();

		thumbnail.remove();
		this.syncIds();
		this.trigger('queueChange');
	}

	updateThumbnail(thumbnail, file) {
		if ('id' in file) {
			thumbnail.dataset.id = file.id;
		}

		if ('filename' in file && file.filename) {
			const extension =
				'filename' in file && file.filename
					? file.filename.split('.').pop().toLowerCase()
					: null;

			let icon = thumbnail.querySelector('[data-role="file-icon"]');

			if (extension) {
				icon.classList.add(`fiv-icon-${extension}`);
				this.setThumbnailType(thumbnail, extension);
			}

			thumbnail.querySelector('[data-role="filename"]').innerText = file.filename;
		}

		if ('message' in file) {
			thumbnail.querySelector('[data-role="message"]').innerText = file.message;
		}

		if ('locked' in file) {
			thumbnail.dataset.locked = file.locked;

			if (file.locked) {
				thumbnail.classList.add('locked');
			}
		}

		if ('thumbnail' in file) {
			if (file.thumbnail) {
				thumbnail.classList.add('has-thumb');
				thumbnail.classList.remove('no-preview');

				thumbnail.querySelector(
					'[data-role="thumbnail"]'
				).style.backgroundImage = `url('${file.thumbnail}')`;
			} else {
				thumbnail.classList.add('no-preview');
			}
		}
	}

	setImages(images) {
		this.clear();

		if (!images.length) {
			return;
		}

		for (var i = 0; i < images.length; i++) {
			var allowed = this.getAllowedFileCount();

			if (allowed != -1 && allowed <= 0) {
				continue;
			}

			let file = images[i];

			let thumbnail = this.createThumbnail();
			thumbnail.classList.add('completed', 'success');

			this.updateThumbnail(thumbnail, file);
			this.appendThumbnailToList(thumbnail, true);
			this.bindEvents(thumbnail);
			this.uploadCounter++;
		}

		this.checkLimits();
	}

	// Insert thumbnail to list if possible
	appendThumbnailToList(thumbnail, append) {
		append = append || false;

		if (append) {
			if (this.triggerEl) {
				this.triggerEl.before(thumbnail);
			} else {
				this.list.appendChild(thumbnail);
			}
		} else {
			this.list.insertBefore(thumbnail, this.list.firstChild);
		}
	}

	setThumbnailType(thumbnail, type) {
		const regex = /\bfile-\S+/g;

		const classes = thumbnail.className.split(' ');
		const filteredClasses = classes.filter((c) => !c.match(regex));
		thumbnail.className = filteredClasses.join(' ');

		thumbnail.classList.add(`file-${type}`);

		if (type == 'image') {
			thumbnail.classList.remove('no-preview');
		} else {
			thumbnail.classList.add('no-preview');
		}
	}

	// Main upload routine
	uploadFile(file) {
		const thumbnail = this.createThumbnail();
		const progress = thumbnail.querySelector('[data-role="progress"]');

		this.uploadCounter++;
		this.checkLimits();

		if (this.submitButton) {
			this.submitButton.disabled = true;
		}

		addSpinner(thumbnail, 'light');

		if (['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
			var reader = new FileReader();

			reader.onload = (e) => {
				this.updateThumbnail(thumbnail, {
					thumbnail: e.target.result,
				});
			};

			reader.readAsDataURL(file);
		} else {
			thumbnail.classList.add('no-preview');
		}

		this.updateThumbnail(thumbnail, {
			id: null,
			filename: file.name,
		});

		this.appendThumbnailToList(thumbnail);

		this.bindEvents(thumbnail);

		let url = parseRoute(this.params.uploadroute);

		let ajax = new XMLHttpRequest();
		ajax.open('POST', url);

		let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
		ajax.setRequestHeader('X-CSRF-TOKEN', csrfToken);

		ajax.upload.addEventListener('progress', function (e) {
			var percent = (e.loaded / e.total) * 100;
			progress.style.width = percent + '%';
		});

		ajax.addEventListener('load', () => {
			progress.style.width = '100%';

			if (ajax.status >= 200 && ajax.status < 300) {
				var response = JSON.parse(ajax.responseText);

				thumbnail.classList.add('completed');
				removeSpinner(thumbnail);

				if (response.error) {
					thumbnail.classList.add('error');
				} else {
					thumbnail.classList.add('success');
				}

				this.updateThumbnail(thumbnail, response);

				this.onComplete();
				this.syncIds();
				return;
			}

			thumbnail.classList.add('completed');
			thumbnail.classList.add('error');
			thumbnail.querySelector('[data-role="message"]').innerText = ajax.statusText;
		});

		ajax.addEventListener('error', (e) => {
			this.onComplete();
			thumbnail.classList.add('error');
		});

		ajax.addEventListener('abort', (e) => {
			this.onComplete();
			thumbnail.remove();
		});

		const form = new FormData();
		form.append('file', file);
		ajax.send(form);

		this.trigger('queueChange');
	}
}
