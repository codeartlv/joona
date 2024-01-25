import Handler from './../handler.js';
import Modal from '../components/modal.js';
import PerfectScrollbar from 'perfect-scrollbar';
import ConfirmDialog from '../components/confirm-dialog.js';
import axios from 'axios';

export default class Admin extends Handler {
	static get pluginName() {
		return 'admin';
	}

	editMyProfile(el, parameters, runtime) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			const url = runtime.route('joona.user.me');

			modal.open(url);
		});
	}

	changeMyPassword(el, parameters, runtime) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			const url = runtime.route('joona.user.my-password');
			modal.open(url);
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

	userEdit(el, parameters, runtime) {
		el.addEventListener('click', () => {
			const modal = new Modal('admin-user-edit');
			const url = runtime.route('joona.user.edit', {
				id: parameters.id || 0,
			});
			modal.open(url);
		});
	}

	userEditForm(el, parameters, runtime) {
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

		let levelDropdown = el.querySelector('[data-role="level-toggle"]');

		levelDropdown.addEventListener('change', (event) => {
			selectLevel(levelDropdown.value);
		});

		selectLevel(levelDropdown.value);
	}

	sidebar(el) {
		new PerfectScrollbar(el, {
			wheelPropagation: false,
			suppressScrollX: true,
		});
	}

	confirm(el, parameters, runtime) {
		el.addEventListener('click', (event) => {
			let confirmDialog = new ConfirmDialog(parameters.caption, parameters.message, [
				{
					caption: runtime.lang('common.cancel'),
					role: 'secondary',
					callback: () => {
						return false;
					},
				},
				{
					caption: runtime.lang('common.ok'),
					role: 'primary',
					callback: () => {
						document.location = el.href;
						return true;
					},
				},
			]);

			confirmDialog.open();

			event.preventDefault();
			return false;
		});
	}

	colorThemeSwitch(el, params, runtime) {
		el.addEventListener('click', () => {
			const currentMode = document.documentElement.dataset.bsTheme;
			const nextMode = currentMode == 'light' ? 'dark' : 'light';

			document.documentElement.dataset.bsTheme = nextMode;

			const newIcon = el.dataset[`${currentMode}Icon`];
			const iconContainer = el.querySelector('[data-role="icon"]');

			if (iconContainer) {
				iconContainer.innerHTML = newIcon;
			}

			const url = runtime.route('joona.set-theme', {
				mode: nextMode,
			});

			axios.post(url);
		});
	}

	roleEdit(el, parameters, runtime) {
		el.addEventListener('click', () => {
			const modal = new Modal();
			const url = runtime.route('joona.user.permission-edit-role', {
				id: parameters.id || 0,
			});
			modal.open(url);
		});
	}
}
