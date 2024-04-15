import Sortable from 'sortablejs';
import { addSpinner, removeSpinner, parseJsonLd } from './../helpers';

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

		this.trigger = element.querySelector('[data-role="trigger"]');

		this.dataField = document.createElement('input');
		this.dataField.type = 'hidden';
		this.dataField.name = this.fileSelector.name;

		this.fileSelector.setAttribute('name', '');
		this.fileSelector.parentNode.insertBefore(this.dataField, this.fileSelector.nextSibling);

		this.initSortable();
		this.initUploader();

		let images = parseJsonLd(this.element.querySelector('[data-role="data"]'));

		if (images.length) {
			this.setImages(images);
		}
	}

	// Event handling
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

	initSortable() {
		var pid, freezed, positions;

		new Sortable(this.element, {
			animation: 150,
			delay: 500,
			delayOnTouchOnly: true,
			filter: '.upload-area',
			preventOnFilter: false,
			onStart: () => {
				freezed = [].slice.call(this.element.querySelectorAll('.upload-area'));
				positions = freezed.map(function (el) {
					return Sortable.utils.index(el);
				});
			},
			onMove: function (evt) {
				var vector,
					freeze = false;
				clearTimeout(pid);

				pid = setTimeout(function () {
					var list = evt.to;

					freezed.forEach(function (el, i) {
						var idx = positions[i];

						if (list.children[idx] !== el) {
							var realIdx = Sortable.utils.index(el);
							list.insertBefore(el, list.children[idx + (realIdx < idx)]);
						}
					});
				}, 0);

				freezed.forEach(function (el, i) {
					if (el === evt.related) {
						freeze = true;
					}

					if (
						evt.related.nextElementSibling === el &&
						evt.relatedRect.top < evt.draggedRect.top
					) {
						vector = -1;
					}
				});

				return freeze ? false : vector;
			},
			onEnd: () => {
				this.syncIds();
			},
		});
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
		let files = document.querySelectorAll('.upload-file');

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

		this.dispatchEvent('queueChange');
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
			this.trigger.classList.add('hidden');
		} else {
			this.trigger.classList.remove('hidden');
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
			let url = route(this.params.deleteroute);

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

		this.uploadCounter--;
		this.checkLimits();

		thumbnail.remove();
		this.syncIds();
		this.dispatchEvent('queueChange');
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
			thumbnail.dataset.id = file.id;
			thumbnail.dataset.type = file.type;
			thumbnail.dataset.locked = file.locked;

			if (file.locked) {
				thumbnail.classList.add('locked');
			}

			thumbnail.querySelector(
				'[data-role="thumbnail"]'
			).style.backgroundImage = `url('${file.url}')`;
			thumbnail.classList.add('completed', 'success');

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
			if (this.trigger) {
				this.trigger.before(thumbnail);
			} else {
				this.list.appendChild(thumbnail);
			}
		} else {
			this.list.insertBefore(thumbnail, this.list.firstChild);
		}
	}

	// Main upload routine
	uploadFile(file) {
		const thumbnail = this.createThumbnail();
		const progress = thumbnail.querySelector('[data-role="progress"]');
		const extension = file.name.split('.').pop();

		this.uploadCounter++;
		this.checkLimits();

		if (this.submitButton) {
			this.submitButton.disabled = true;
		}

		addSpinner(thumbnail, 'light');

		if (['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
			var reader = new FileReader();

			reader.onload = function (e) {
				thumbnail.querySelector(
					'[data-role="thumbnail"]'
				).style.backgroundImage = `url('${e.target.result}')`;

				thumbnail.classList.add('has-thumb');
			};

			reader.readAsDataURL(file);
		} else {
			thumbnail.classList.add(`no-preview file-${extension}`);
		}

		thumbnail.querySelector('[data-role="filename"]').innerText = file.name;

		this.appendThumbnailToList(thumbnail);

		this.bindEvents(thumbnail);

		let url = route(this.params.uploadroute);

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

			var response = JSON.parse(ajax.responseText);

			thumbnail.classList.add('completed');
			removeSpinner(thumbnail);

			if (response.error) {
				thumbnail.classList.add('error');
				thumbnail.querySelector('[data-role="message"]').innerText = response.message;
			} else {
				thumbnail.classList.add('success');
				thumbnail.dataset.id = response.id;

				if (response.url) {
					thumbnail.querySelector(
						'[data-role="thumbnail"]'
					).style.backgroundImage = `url('${response.url}')`;
				}
			}

			this.onComplete();
			this.syncIds();
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

		this.dispatchEvent('queueChange');
	}
}
