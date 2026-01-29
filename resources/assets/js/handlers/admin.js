import Handler from './../handler.js';
import Modal from '../components/modal.js';
import Offcanvas from '../components/offcanvas.js';
import PerfectScrollbar from 'perfect-scrollbar';
import ConfirmDialog from '../components/confirm-dialog.js';
import BootstrapTooltip from 'bootstrap/js/dist/tooltip';
import axios from 'axios';
import { addSpinner, removeSpinner } from '../helpers';

export default class Admin extends Handler {
	static get pluginName() {
		return 'admin';
	}

	editMyProfile(el) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			modal.open(route('joona.user.me'));
		});
	}

	changeMyPassword(el) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			modal.open(route('joona.user.my-password'));
		});
	}

	sidebar(el) {
		new PerfectScrollbar(el, {
			wheelPropagation: false,
			suppressScrollX: true,
		});
	}

	toggleSidebar(el, parameters) {
		el.addEventListener('click', () => {
			const sidebar = document.querySelector(parameters.sidebar);

			if (!sidebar) {
				return;
			}

			sidebar.classList.toggle('active');
		});
	}

	tooltip(element, parameters) {
		return new BootstrapTooltip(element, {
			title: parameters.title,
			html: parameters.html || false,
		});
	}

	userEdit(el, parameters) {
		el.addEventListener('click', () => {
			const modal = new Modal('admin-user-edit');
			modal.open(
				route('joona.user.edit', {
					id: parameters.id || 0,
				}),
			);
		});
	}

	userEditForm(el, parameters) {
		parameters = {
			classmode: null,
			...parameters,
		};

		let classDropdown = el.querySelector('[data-role="class"]');
		let levelDropdown = el.querySelector('[data-role="level-toggle"]');

		// Password toggle
		let choosePasswordSetup = (method) => {
			const passwordInput = el.querySelector('[data-role="password-input"]');

			passwordInput.classList.add('d-none');

			if (method == 'set-password') {
				passwordInput.classList.remove('d-none');
			}
		};

		el.querySelectorAll('[data-role="pass-setup"]').forEach((input) => {
			input.addEventListener('click', () => {
				choosePasswordSetup(input.value);
			});
		});

		let selected = el.querySelector('[data-role="pass-setup"]:checked');

		if (selected) {
			choosePasswordSetup(selected.value);
		}

		// Level toggle
		let selectLevel = (level) => {
			el.querySelectorAll('[data-toggle="level"]').forEach((element) => {
				element.classList.add('d-none');
			});

			el.querySelectorAll(`[data-toggle="level"][data-level="${level}"]`).forEach(
				(element) => {
					element.classList.remove('d-none');
				},
			);
		};

		// Class toggle
		let selectClass = (className) => {
			if (parameters.classmode != 'interchangeable') {
				return;
			}

			let rolesBlock = el.querySelector('[data-role="roles"]');

			rolesBlock.classList.toggle('d-none', className.length != 0);
		};

		// Roles checkboxes
		let roles = el.querySelectorAll('[data-role="role-value"]');

		roles.forEach((role) => {
			if (parameters.classmode != 'interchangeable') {
				return;
			}

			if (!classDropdown) {
				return;
			}

			role.addEventListener('click', () => {
				let checkedLength = el.querySelectorAll('[data-role="role-value"]:checked').length;

				if (checkedLength > 0) {
					classDropdown.value = '';
					classDropdown.disabled = true;
				} else {
					classDropdown.disabled = false;
				}
			});
		});

		if (levelDropdown) {
			levelDropdown.addEventListener('change', (event) => {
				selectLevel(levelDropdown.value);
			});

			selectLevel(levelDropdown.value);
		}

		if (classDropdown) {
			classDropdown.addEventListener('change', (event) => {
				selectClass(classDropdown.value);
			});

			selectClass(classDropdown.value);
		}
	}

	sidebar(el) {
		return new PerfectScrollbar(el, {
			wheelPropagation: false,
			suppressScrollX: true,
		});
	}

	confirm(el, parameters) {
		let confirmDialog = new ConfirmDialog(
			parameters.caption ? parameters.caption : trans('joona::common.confirm'),
			parameters.message,
			[
				{
					caption: trans('joona::common.cancel'),
					role: 'secondary',
					callback: () => {
						return false;
					},
				},
				{
					caption: trans('joona::common.ok'),
					role: 'primary',
					callback: () => {
						document.location = el.href;
						return true;
					},
				},
			],
		);

		el.addEventListener('click', (event) => {
			confirmDialog.open();
			event.preventDefault();
			return false;
		});

		return confirmDialog;
	}

	notificationList(el, params) {
		return new (function (el, params) {
			this.loading = false;
			this.completed = false;

			this.container = el.querySelector('[data-role="notification-list.container"]');
			this.trigger = el.querySelector('[data-bs-toggle="dropdown"]');

			this.trigger.addEventListener('shown.bs.dropdown', () => {
				this.container
					.querySelectorAll('[data-role="notification"],.alert')
					.forEach((el) => el.remove());
				this.load();
			});

			this.load = (page) => {
				this.loading = true;

				setTimeout(() => {
					addSpinner(this.container);
				}, 10);

				axios
					.get(
						route('joona.user.notifications', {
							lastId: page,
						}),
					)
					.then((response) => {
						this.completed = response.data.complete;
						this.loading = false;
						removeSpinner(this.container);

						this.onLoad(response);
					});
			};

			this.onLoad = (response) => {
				this.container.insertAdjacentHTML('beforeend', response.data.content);
				this.updateBadge(response.data.badge);
				window.Joona.init(this.container);
				this.scrollbar.update();
			};

			this.updateBadge = function (count) {
				this.trigger.querySelector('.badge').innerText = count > 0 ? count : '';
				el.classList.toggle('has-badge', count > 0);
				this.trigger.querySelector('i').classList.toggle('ringing', count > 0);
			};

			axios.get(route('joona.user.notifications-count')).then((response) => {
				this.updateBadge(parseInt(response.data.badge));
			});

			this.scrollbar = new PerfectScrollbar(this.container, {
				wheelPropagation: false,
				suppressScrollX: true,
			});

			this.container.addEventListener('click', (e) => {
				if (e.target.closest('[data-role="notification"]')) {
					e.stopPropagation();
				}
			});

			this.container.addEventListener('ps-y-reach-end', (e) => {
				if (this.loading || this.completed) {
					return;
				}

				const notifications = this.container.querySelectorAll(
					'[data-role="notification"][data-id]',
				);

				const lastNotification = notifications[notifications.length - 1];

				let lastId = lastNotification ? lastNotification.dataset.id : null;

				this.load(lastId);
			});
		})(el, params);
	}

	colorThemeSwitch(el, params) {
		let resetStates = () => {
			let other = document.querySelectorAll(
				'[data-bind="admin.colorThemeSwitch"][data-theme]',
			);

			other.forEach((tag) => {
				tag.classList.remove('active');

				let currentMode = document.documentElement.dataset.bsTheme;

				if (tag.dataset.theme && currentMode == tag.dataset.theme) {
					tag.classList.add('active');
				}
			});
		};

		params = {
			theme: '',
			...params,
		};

		resetStates();

		el.addEventListener('click', () => {
			const currentMode = document.documentElement.dataset.bsTheme;
			let nextMode = currentMode == 'light' ? 'dark' : 'light';

			if (!params.theme) {
				const newIcon = el.dataset[`${currentMode}Icon`];
				const iconContainer = el.querySelector('[data-role="icon"]');

				if (iconContainer) {
					iconContainer.innerHTML = newIcon;
				}
			} else {
				nextMode = params.theme;
			}

			document.documentElement.dataset.bsTheme = nextMode;
			resetStates();

			axios.post(
				route('joona.set-theme', {
					mode: nextMode,
				}),
			);
		});
	}

	roleEdit(el, parameters) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			modal.open(
				route('joona.user.permission-edit-role', {
					id: parameters.id || 0,
				}),
			);
		});
	}

	openOffcanvas(el, parameters) {
		el.addEventListener('click', () => {
			parameters = {
				name: 'default',
				url: '?',
				backdrop: true,
				...parameters,
			};

			let offcanvas = new Offcanvas(parameters.name, parameters);
			offcanvas.open(parameters.url);
		});
	}

	openPopup(el, parameters) {
		el.addEventListener('click', () => {
			parameters = {
				name: 'default',
				url: '?',
				...parameters,
			};

			let modal = new Modal(parameters.name);

			modal.open(parameters.url);
		});
	}
}
