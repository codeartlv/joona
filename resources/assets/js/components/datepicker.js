import AirDatepicker from 'air-datepicker';
import localeEn from 'air-datepicker/locale/en';
import moment from 'moment';

export default class Datepicker {
	constructor(element, params) {
		this.input = element.querySelector('input[data-role="control"]');
		this.valueField = element.querySelector('input[data-role="value"]');

		this.params = {
			name: 'date',
			range: false,
			dateSeparator: ' / ',
			position: 'bottom center',
			format: 'dd.MM.yyyy',
			timepicker: false,
			pastDate: true,
			firstDay: 1,
			...params,
		};

		this.init();
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
		};

		if (!this.params.pastDate) {
			settings.minDate = new Date();
		}

		this.instance = new AirDatepicker(this.input, settings);

		var value = this.valueField.value;

		if (value) {
			if (settings.range) {
				var parts = value.split(settings.multipleDatesSeparator);

				if (parts.length > 1) {
					var from = moment(
						parts[0],
						settings.altFieldDateFormat.toUpperCase().replaceAll('I', 'm')
					);
					var to = moment(
						parts[1],
						settings.altFieldDateFormat.toUpperCase().replaceAll('I', 'm')
					);

					if (from.isValid() && to.isValid()) {
						this.instance.selectDate([from.toDate(), to.toDate()]);
					}
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
