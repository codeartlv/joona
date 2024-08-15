export default class MultiSelect {
	constructor(container, options = {}) {
		this.container = container;

		let dropdown = this.container.querySelector('.dropdown-menu');
		dropdown.addEventListener('click', (e) => {
			e.stopPropagation();
		});

		this.container.querySelectorAll('input[type="checkbox"]').forEach((element) => {
			element.addEventListener('click', () => {
				this.updateChecked();
			});
		});

		this.updateChecked();
	}

	updateChecked() {
		var checkedInputs = this.container.querySelectorAll('input:checked');
		let textDisplay = this.container.querySelector('[data-role="selected-text"]');

		textDisplay.textContent = choice('joona::common.selected', checkedInputs.length, {
			count: checkedInputs.length,
		});
	}
}
