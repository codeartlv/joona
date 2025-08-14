import Handler from './../handler.js';
import Modal from '../components/modal.js';
import Offcanvas from '../components/offcanvas.js';
import PerfectScrollbar from 'perfect-scrollbar';
import ConfirmDialog from '../components/confirm-dialog.js';
import BootstrapTooltip from 'bootstrap/js/dist/tooltip';
import axios from 'axios';

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
		});
	}

	userEdit(el, parameters) {
		el.addEventListener('click', () => {
			const modal = new Modal('admin-user-edit');
			modal.open(
				route('joona.user.edit', {
					id: parameters.id || 0,
				})
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
				}
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
			]
		);

		el.addEventListener('click', (event) => {
			confirmDialog.open();
			event.preventDefault();
			return false;
		});

		return confirmDialog;
	}

	colorThemeSwitch(el) {
		el.addEventListener('click', () => {
			const currentMode = document.documentElement.dataset.bsTheme;
			const nextMode = currentMode == 'light' ? 'dark' : 'light';

			document.documentElement.dataset.bsTheme = nextMode;

			const newIcon = el.dataset[`${currentMode}Icon`];
			const iconContainer = el.querySelector('[data-role="icon"]');

			if (iconContainer) {
				iconContainer.innerHTML = newIcon;
			}

			axios.post(
				route('joona.set-theme', {
					mode: nextMode,
				})
			);
		});
	}

	roleEdit(el, parameters) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			modal.open(
				route('joona.user.permission-edit-role', {
					id: parameters.id || 0,
				})
			);
		});
	}

	openOffcanvas(el, parameters) {
		el.addEventListener('click', () => {
			parameters = {
				name: 'default',
				url: '?',
				...parameters,
			};

			let modal = new Offcanvas(parameters.name);

			modal.open(parameters.url);
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
