export default class MultiSelect {
	constructor(container, options = {}) {
		this.container = container;
		this.searchInput = this.container.querySelector('[data-role="search-keyword"]');
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

		if (this.searchInput) {
			const onSearch = () => this.filterOptions(this.searchInput.value);

			this.searchInput.addEventListener('input', onSearch);

			this.searchInput.addEventListener('paste', (e) => {
				const pasted = (e.clipboardData || window.clipboardData)?.getData('text') ?? '';
				// apply immediately using the pasted text
				this.filterOptions(pasted);
				// then once the input's value updates
				setTimeout(onSearch, 0);
			});

			// initial filter (no-op if empty)
			this.filterOptions(this.searchInput.value);
		}
	}

	getOptions() {
		return this.container.querySelectorAll('input[data-role="option"]:checked');
	}

	filterOptions(keyword) {
		const normalize = (s) =>
			String(s || '')
				.normalize('NFD')
				.replace(/[\u0300-\u036f]/g, '')
				.toLowerCase();

		const q = normalize(keyword).trim();

		this.container.querySelectorAll('li.form-multiselect__option').forEach((li) => {
			const label = li.querySelector('.form-check-label');
			const labelText = label ? normalize(label.textContent) : '';
			const match = !q || labelText.includes(q);
			li.classList.toggle('d-none', !match);
		});

		this.container.querySelectorAll('li.form-multiselect__group').forEach((li) => {
			const label = li.querySelector('.form-check-label');
			const labelText = label ? normalize(label.textContent) : '';
			const match = !q || labelText.includes(q);
			li.classList.toggle('d-none', !match);
		});

		this.container.querySelectorAll('li.form-multiselect__group-label').forEach((li) => {
			const labelText = normalize(li.textContent);
			const match = !q || labelText.includes(q);
			li.classList.toggle('d-none', !match);
		});
	}

	updateChecked() {
		let checkedInputs = this.getOptions();
		let textDisplay = this.container.querySelector('[data-role="selected-text"]');

		if (typeof choice === 'function') {
			textDisplay.textContent = choice('joona::common.selected', checkedInputs.length, {
				count: checkedInputs.length,
			});
		}

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

		this.updateChecked();
	}

	setAllOptions(checked) {
		this.container.querySelectorAll('input[data-role="option"]:enabled').forEach((element) => {
			element.checked = checked;
			element.dispatchEvent(new Event('change'));
		});
	}
}

