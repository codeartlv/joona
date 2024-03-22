import 'htmx.org';
import 'bootstrap';
import Alpine from 'alpinejs';

import AjaxForm from './components/ajax-form.js';
import Modal from './components/modal.js';
import ConfirmDialog from './components/confirm-dialog.js';
import PasswordValidator from './components/password-validator.js';
import Datepicker from './components/datepicker.js';
import Uploader from './components/uploader.js';
import PerfectScrollbar from 'perfect-scrollbar';
import { parseJsonLd } from './helpers.js';
import Lang from 'lang.js';

window.locale = document.documentElement.getAttribute('lang');

let translationString = parseJsonLd(
	document.querySelector('head script[data-role="js-translations"]')
);

var lang = new Lang({
	messages: translationString || {},
	locale: window.locale,
	fallback: window.locale,
});

Alpine.data('form', function () {
	return {
		init() {
			new AjaxForm(this.$el, this.$el.dataset);
		},
	};
});

Alpine.data('datepicker', function () {
	return {
		init() {
			new Datepicker(this.$el, this.$el.dataset);
		},
	};
});

Alpine.data('uploader', function () {
	return {
		init() {
			new Uploader(this.$el, this.$el.dataset);
		},
	};
});

Alpine.data('passwordValidator', function () {
	return {
		init() {
			new PasswordValidator(this.$el, this.$el.dataset);
		},
	};
});

Alpine.data('adminColorThemeSwitch', function () {
	return {
		changeTheme() {
			const currentMode = document.documentElement.dataset.bsTheme;
			const nextMode = currentMode == 'light' ? 'dark' : 'light';

			document.documentElement.dataset.bsTheme = nextMode;

			const newIcon = this.$el.dataset[`${currentMode}Icon`];
			const iconContainer = this.$el.querySelector('[data-role="icon"]');

			if (iconContainer) {
				iconContainer.innerHTML = newIcon;
			}

			axios.post(
				route('joona.set-theme', {
					mode: nextMode,
				})
			);
		},
	};
});

Alpine.data('adminEditMyProfile', function () {
	return {
		init() {
			this.$el.addEventListener('click', () => {
				const modal = new Modal();
				modal.open(route('joona.user.me'));
			});
		},
	};
});

Alpine.data('adminUserEdit', function () {
	return {
		init() {
			this.$el.addEventListener('click', () => {
				const modal = new Modal('admin-user-edit');
				const url = route('joona.user.edit', {
					id: this.$el.dataset.id || 0,
				});

				modal.open(url);
			});
		},
	};
});

Alpine.data('adminRoleEdit', function () {
	return {
		init() {
			this.$el.addEventListener('click', () => {
				const modal = new Modal();
				const url = route('joona.user.permission-edit-role', {
					id: this.$el.dataset.id || 0,
				});
				modal.open(url);
			});
		},
	};
});

Alpine.data('adminSidebar', function () {
	return {
		init() {
			new PerfectScrollbar(this.$el, {
				wheelPropagation: false,
				suppressScrollX: true,
			});
		},
	};
});

Alpine.data('adminToggleSidebar', function () {
	return {
		init() {
			this.$el.addEventListener('click', () => {
				const sidebar = document.querySelector(this.$el.dataset.sidebar);

				if (!sidebar) {
					return;
				}

				sidebar.classList.toggle('active');
			});
		},
	};
});

Alpine.data('confirm', function () {
	return {
		init() {
			this.$el.addEventListener('click', (event) => {
				let confirmDialog = new ConfirmDialog(
					this.$el.dataset.caption,
					this.$el.dataset.message,
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

				confirmDialog.open();

				event.preventDefault();
				return false;
			});
		},
	};
});

Alpine.data('adminUserEditForm', function () {
	return {
		init() {
			// Password toggle
			let choosePasswordSetup = (method) => {
				const passwordInput = this.$el.querySelector('[data-role="password-input"]');

				passwordInput.classList.add('d-none');

				if (method == 'set-password') {
					passwordInput.classList.remove('d-none');
				}
			};

			this.$el.querySelectorAll('[data-role="pass-setup"]').forEach((input) => {
				input.addEventListener('click', () => {
					choosePasswordSetup(input.value);
				});
			});

			let selected = this.$el.querySelector('[data-role="pass-setup"]:checked');

			if (selected) {
				choosePasswordSetup(selected.value);
			}

			// Level toggle
			let selectLevel = (level) => {
				this.$el.querySelectorAll('[data-toggle="level"]').forEach((element) => {
					element.classList.add('d-none');
				});

				this.$el
					.querySelectorAll(`[data-toggle="level"][data-level="${level}"]`)
					.forEach((element) => {
						element.classList.remove('d-none');
					});
			};

			let levelDropdown = this.$el.querySelector('[data-role="level-toggle"]');

			if (levelDropdown) {
				levelDropdown.addEventListener('change', (event) => {
					selectLevel(levelDropdown.value);
				});

				selectLevel(levelDropdown.value);
			}
		},
	};
});

Alpine.data('adminChangeMyPpassword', function () {
	return {
		init() {
			this.$el.addEventListener('click', () => {
				const modal = new Modal();
				modal.open(route('joona.user.my-password'));
			});
		},
	};
});

window.Alpine = Alpine;

window.trans = function (keyword, args, locale) {
	return lang.get(keyword, args, locale);
};

window.choice = function (keyword, count, args, locale) {
	return lang.get(keyword, count, args, locale);
};
