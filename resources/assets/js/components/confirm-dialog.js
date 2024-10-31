import Modal from './modal';

export default class ConfirmDialog {
	constructor(caption, message, buttons) {
		this.caption = caption;
		this.message = message;
		this.buttons = buttons;
	}

	getButtonClass(role) {
		switch (role) {
			case 'primary':
				return 'btn-primary';
			case 'secondary':
				return 'btn-outline-subtle';
			default:
				return '';
		}
	}

	open() {
		this.modalInstance = new Modal();

		let modalEl = document.createElement('div');
		modalEl.classList.add('modal');
		modalEl.setAttribute('tabindex', '-1');
		modalEl.setAttribute('role', 'dialog');
		modalEl.setAttribute('aria-modal', 'true');

		let modalCaption = this.caption
			? `<div class="modal-header"><h5 class="modal-title">${this.caption}</h5></div>`
			: '';

		let modalHtml = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    ${modalCaption}
                    <div class="modal-body">
                        <div class="modal-inner">
                            ${this.message}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <section></section>
                    </div>
                </div>
            </div>
        `;

		modalEl.innerHTML = modalHtml;
		document.body.appendChild(modalEl);

		if (this.buttons && this.buttons.length) {
			let buttonContainer = modalEl.querySelector('.modal-footer > section');

			this.buttons.forEach((buttonConfig) => {
				let button = {
					disabled: false,
					caption: '?',
					role: 'primary',
					callback: function () {},
					...buttonConfig,
				};

				let buttonEl = document.createElement('button');
				buttonEl.type = 'button';
				buttonEl.classList.add('btn', this.getButtonClass(button.role));
				buttonEl.innerText = button.caption;
				buttonEl.disabled = button.disabled;

				buttonEl.addEventListener('click', () => {
					this.close();
					button.callback();
				});

				buttonContainer.appendChild(buttonEl);
			});
		}

		this.modalInstance.open(modalEl, {
			animations: false,
		});
	}

	close() {
		if (this.modalInstance && this.modalInstance.close) {
			this.modalInstance.close();
		}
	}
}
