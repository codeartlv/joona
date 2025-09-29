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
				.toLocaleLowerCase();

		const q = normalize(keyword).trim();

		const inputs = this.container.querySelectorAll('input[data-role="option"]');
		inputs.forEach((input) => {
			const label =
				input.closest('label') ||
				(input.id
					? this.container.querySelector(
							`label[for="${
								typeof CSS !== 'undefined' && CSS.escape
									? CSS.escape(input.id)
									: input.id
							}"]`
					  )
					: null);

			if (!label) return;

			const match = !q || normalize(label.textContent).includes(q);

			// Toggle visibility on the wrapper <li.form-multiselect__option>
			const item =
				label.closest('li.form-multiselect__option') ||
				input.closest('li.form-multiselect__option');

			if (item) {
				item.classList.toggle('d-none', !match);
			} else {
				// Fallback if no <li> wrapper exists
				label.style.display = match ? '' : 'none';
			}
		});
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
