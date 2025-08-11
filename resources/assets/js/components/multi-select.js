export default class MultiSelect {
	constructor(container, options = {}) {
		this.container = container;

		this.toggleCheck = this.container.querySelector('input[data-role="toggle"]');

		let dropdown = this.container.querySelector('.dropdown-menu');
		if (dropdown) {
			dropdown.addEventListener('click', (e) => {
				e.stopPropagation();
			});
		}

		let checkboxes = this.container.querySelectorAll('input[data-role="option"]');

		// Add event listener to the option checkboxes
		if (checkboxes) {
			checkboxes.forEach((element) => {
				element.addEventListener('change', () => {
					this.updateChecked();
				});
			});
		}

		// Event listener for the toggle checkbox
		if (this.toggleCheck) {
			this.toggleCheck.addEventListener('change', () => {
				this.handleToggleChange();
			});
		}

		// Initialize the state of the toggle checkbox
		this.updateChecked();
	}

	getOptions() {
		return this.container.querySelectorAll('input[data-role="option"]:checked');
	}

	updateChecked() {
		let checkedInputs = this.getOptions();
		let textDisplay = this.container.querySelector('[data-role="selected-text"]');

		textDisplay.textContent = choice('joona::common.selected', checkedInputs.length, {
			count: checkedInputs.length,
		});

		let allOptionInputs = this.container.querySelectorAll('input[data-role="option"]:enabled');
		let totalOptions = allOptionInputs.length;
		let totalChecked = checkedInputs.length;

		if (!this.toggleCheck) {
			return;
		}

		if (totalChecked === totalOptions && totalOptions > 0) {
			// All options are checked
			this.toggleCheck.checked = true;
			this.toggleCheck.indeterminate = false;
		} else if (totalChecked === 0) {
			// None of the options are checked
			this.toggleCheck.checked = false;
			this.toggleCheck.indeterminate = false;
		} else {
			// Some options are checked
			this.toggleCheck.checked = false;
			this.toggleCheck.indeterminate = true;
		}
	}

	handleToggleChange() {
		if (this.toggleCheck.checked) {
			// Toggle checkbox is now checked, so check all options
			this.setAllOptions(true);
		} else {
			// Toggle checkbox is now unchecked, so uncheck all options
			this.setAllOptions(false);
		}
		// Update the display and the toggle checkbox state
		this.updateChecked();
	}

	setAllOptions(checked) {
		this.container.querySelectorAll('input[data-role="option"]:enabled').forEach((element) => {
			element.checked = checked;
		});
	}
}
