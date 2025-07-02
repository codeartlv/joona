import AirDatepicker from 'air-datepicker';
import localeEn from 'air-datepicker/locale/en';
import moment from 'moment';

export default class Datepicker {
	events = {
		select: [],
	};

	constructor(element, params) {
		this.container = element;
		this.input = element.querySelector('input[data-role="datepicker"]');
		this.clearDate = element.querySelector('[data-role="clear"]');
		this.valueField = element.querySelector('input[data-role="value"]');
		this.eventListeners = new Map();

		this.params = {
			name: 'date',
			range: false,
			dateSeparator: ' / ',
			position: 'bottom left',
			format: 'dd.MM.yyyy',
			timeFormat: 'HH:mm',
			timepicker: false,
			mindate: '',
			firstDay: 1,
			...params,
		};

		this.params.timepicker =
			this.params.timepicker === 'true' || this.params.timepicker === true;

		this.init();
	}

	on(event, callback) {
		if (!this.eventListeners.has(event)) {
			this.eventListeners.set(event, []);
		}

		this.eventListeners.get(event).push(callback);
	}

	trigger(event, ...args) {
		const listeners = this.eventListeners.get(event);

		if (listeners && listeners.length) {
			listeners.forEach((listener) => listener(...args));
		}
	}

	clear() {
		this.instance.clear();
	}

	get() {
		return this.params.range ? this.instance.selectedDates : this.instance.selectedDates[0];
	}

	set(date) {
		this.clear();

		if (!date) {
			return;
		}

		this.instance.selectDate(date);
	}

	init() {
		let locales = {
			en: localeEn,
			lv: {
				days: [
					'Svētdiena',
					'Pirmdiena',
					'Otrdiena',
					'Trešdiena',
					'Ceturtdiena',
					'Piektdiena',
					'Sestdiena',
				],
				daysShort: ['Sve', 'Pr', 'Otr', 'Tre', 'Cet', 'Pt', 'Ses'],
				daysMin: ['Sv', 'Pr', 'Ot', 'Tr', 'Ct', 'Pt', 'Se'],
				months: [
					'Janvāris',
					'Februāris',
					'Marts',
					'Aprīlis',
					'Maijs',
					'Jūnijs',
					'Jūlijs',
					'Augusts',
					'Septembris',
					'Oktobris',
					'Novembris',
					'Decembris',
				],
				monthsShort: [
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'Mai',
					'Jūn',
					'Jūl',
					'Aug',
					'Sep',
					'Okt',
					'Nov',
					'Dec',
				],
				today: 'Šodien',
				clear: 'Notīrīt',
				dateFormat: 'dd.mm.yyyy',
				timeFormat: 'hh:mm aa',
				firstDay: 1,
			},
		};

		const settings = {
			locale: window.locale in locales ? locales[window.locale] : locales['en'],
			range: this.params.range,
			autoClose: true,
			position: this.params.position,
			multipleDatesSeparator: this.params.dateSeparator,
			dateFormat: this.params.format,
			timeFormat: this.params.timeFormat,
			timepicker: this.params.timepicker,
			startDate: new Date(new Date().setHours(0, 0, 0, 0)),
			altFieldDateFormat: 'yyyy-MM-dd' + (this.params.timepicker ? ' HH:mm:00' : ''),
			altField: this.valueField,
			onSelect: ({ date }) => {
				this.trigger('select', date);
				this.container.classList.toggle('has-date', date);
			},
		};

		if (this.params.mindate) {
			settings.minDate = new Date(this.params.mindate);
		}

		this.instance = new AirDatepicker(this.input, settings);

		var value = this.valueField.value;

		if (value) {
			if (settings.range) {
				var parts = value.split(settings.multipleDatesSeparator);
				var from, to;

				if (parts[0]) {
					from = moment(
						parts[0],
						settings.altFieldDateFormat.toUpperCase().replaceAll('I', 'm')
					);
				}

				if (parts[1]) {
					to = moment(
						parts[1],
						settings.altFieldDateFormat.toUpperCase().replaceAll('I', 'm')
					);
				}

				if (from && from.isValid() && to && to.isValid()) {
					this.instance.selectDate([from.toDate(), to.toDate()]);
				} else if (from && from.isValid()) {
					this.instance.selectDate([from.toDate()]);
				} else if (to && to.isValid()) {
					this.instance.selectDate([to.toDate()]);
				}
			} else {
				var format = this.params.timepicker ? 'YYYY-MM-DD LTS' : 'YYYY-MM-DD';
				var date = moment(value, format);

				if (date.isValid()) {
					this.instance.selectDate(date.toDate());
				}
			}
		}

		if (this.clearDate) {
			this.clearDate.addEventListener('click', () => {
				this.clear();
			});
		}
	}
}
