export default class BulkTableEditor {
	constructor(element, params) {
		params = {
			tableid: '',
			...params,
		};

		if (!params.tableid) {
			console.error('Table ID required.');
			return;
		}

		this.table = document.querySelector(params.tableid);

		if (!this.table) {
			console.error('Table not found.');
			return;
		}

		this.form = element.querySelector('form');
		this.submitButton = element.querySelector('[data-role="submit"]');
		this.optionSelect = element.querySelector('[data-role="options"]');
		this.toggleCheck = this.table.querySelector('[data-role="toggle-all"]');

		if (!this.toggleCheck) {
			console.error(
				'Master checkbox not found. Please place checkbox with data-role="toggle-all" attribute.'
			);
			return;
		}

		this.submitButton.disabled = true;
		this.optionSelect.disabled = true;

		// Add event listener to the option checkboxes
		this.getOptions().forEach((element) => {
			element.addEventListener('change', () => {
				this.updateChecked();
			});
		});

		this.optionSelect.addEventListener('change', () => {
			let value = this.optionSelect.value;

			this.submitButton.disabled = !(value ? true : false);
		});

		// Event listener for the toggle checkbox
		this.toggleCheck.addEventListener('change', () => {
			this.handleToggleChange();
		});

		this.submitButton.addEventListener('click', () => {
			let checked = [];

			this.getOptions(true).forEach((checkbox) => {
				let field = document.createElement('input');
				field.type = 'hidden';
				field.name = 'id[]';
				field.value = checkbox.value;
				this.form.appendChild(field);
			});

			this.form.submit();
		});

		// Initialize the state of the toggle checkbox
		this.updateChecked();
	}

	getOptions(state) {
		return this.table.querySelectorAll('[data-role="item"]' + (state ? ':checked' : ''));
	}

	updateChecked() {
		let checkedInputs = this.getOptions(true);
		let allOptionInputs = this.getOptions();

		let totalOptions = allOptionInputs.length;
		let totalChecked = checkedInputs.length;

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

		this.optionSelect.disabled = !totalChecked > 0;

		if (!totalChecked) {
			this.submitButton.disabled = true;
		}

		if (this.optionSelect.value && totalChecked > 0) {
			this.submitButton.disabled = false;
		}

		this.table.querySelectorAll('tbody tr').forEach((row) => {
			row.classList.remove('selected');
		});

		checkedInputs.forEach((checkbox) => {
			checkbox.closest('tr').classList.add('selected');
		});
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
		this.table.querySelectorAll('[data-role="item"]').forEach((element) => {
			element.checked = checked;
		});
	}
}
