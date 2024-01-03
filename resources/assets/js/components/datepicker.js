import AirDatepicker from 'air-datepicker';
import localeEn from 'air-datepicker/locale/en';
import moment from 'moment';

export default class Datepicker {
	events = {
		select: [],
	};

	constructor(element, params) {
		this.input = element.querySelector('input[data-role="datepicker"]');
		this.valueField = element.querySelector('input[data-role="value"]');

		this.params = {
			name: 'date',
			range: false,
			dateSeparator: ' / ',
			position: 'bottom left',
			format: 'dd.MM.yyyy',
			timepicker: false,
			mindate: '',
			firstDay: 1,
			...params,
		};

		this.params.timepicker =
			this.params.timepicker === 'true' || this.params.timepicker === true;

		this.init();
	}

	bindEvent(event, callback) {
		this.events[event].push(callback);
	}

	dispatchEvent(event, ...data) {
		if (this.events[event] && this.events[event].length) {
			for (let fn of this.events[event]) {
				fn.apply(this, data);
			}
		}
	}

	clear() {
		this.instance.clear();
	}

	set(date) {
		this.clear();
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
			timepicker: this.params.timepicker,
			altFieldDateFormat: 'yyyy-MM-dd' + (this.params.timepicker ? ' hh:ii:00' : ''),
			altField: this.valueField,
			onSelect: (date, formattedDate, datepicker) => {
				this.dispatchEvent('select', date.date);
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
				var format = settings.altFieldDateFormat.toUpperCase().replaceAll('I', 'm');
				var date = moment(value, format);

				if (date.isValid()) {
					this.instance.selectDate(date.toDate());
				}
			}
		}
	}
}
