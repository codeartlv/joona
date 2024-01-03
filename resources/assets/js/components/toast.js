import BootstrapToast from 'bootstrap/js/dist/toast';

export default class Toast {
	constructor() {
		let toastContainer = document.querySelector('.toast-container');

		if (!toastContainer) {
			toastContainer = document.createElement('div');
			toastContainer.classList.add(
				'toast-container',
				'position-fixed',
				'bottom-0',
				'end-0',
				'p-3'
			);

			document.body.appendChild(toastContainer);
		}
	}

	show({ message, icon, content, buttons, role = 'info' }) {
		let iconHtml = '';

		if (icon) {
			iconHtml = `<img src="${icon}" class="rounded me-2" />`;
		}

		if (role == 'success') {
			iconHtml = `<i class="material-symbols-outlined">done</i>`;
		}

		if (role == 'error') {
			iconHtml = `<i class="material-symbols-outlined">error</i>`;
		}

		let template = `
		<div class="toast toast-${role}" role="alert" aria-live="assertive" aria-atomic="true">
    		<div class="toast-header">
      			${iconHtml}
      			<strong class="me-auto">${message}</strong>
      			<button type="button" class="btn-close" data-bs-dismiss="toast">
					<i class="material-symbols-outlined">close</i>
				</button>
    		</div>
    		<div class="toast-body">
      			${content}
    		</div>
  		</div>`;

		let toastContainer = document.querySelector('.toast-container');

		toastContainer.insertAdjacentHTML('beforeend', template);
		let toastElement = toastContainer.querySelector('.toast:last-child');

		const toastComponent = new BootstrapToast(toastElement, {});
		toastComponent.show();

		toastElement.addEventListener('hidden.bs.toast', () => {
			toastElement.remove();
		});
	}
}
