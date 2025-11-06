import $ from 'jquery';
import 'devbridge-autocomplete/dist/jquery.autocomplete';

export default class Autocomplete {
	events = {
		select: [],
		change: [],
	};

	selected = null;

	constructor(element, data) {
		this.element = $(element);
		this.eventListeners = new Map();

		data = $.extend(
			{
				route: '',
				input: '',
			},
			data
		);

		this.searchField = this.element.find('input[type="text"]');
		this.clearToggle = this.element.find('[data-role="clear"]');

		this.dataField = this.element.find('input[type="hidden"]');

		this.dataProxy = data.proxy;
		this.id = Number(this.dataField.val());

		if (!this.dataField.length) {
			console.error('Data field required to store autocomplete result (data-input).');
			return;
		}

		this.searchField.data('instance', this);

		this.searchField.autocomplete({
			noCache: true,
			minChars: 1,
			showNoSuggestionNotice: true,
			noSuggestionNotice: trans('joona::common.no_results_found'),
			params: {
				proxy: this.dataProxy,
			},
			serviceUrl: route(data.route),
			deferRequestBy: 200,
			beforeRender: function (container, suggestions) {
				for (var i = 0; i < suggestions.length; i++) {
					var item = suggestions[i];

					var el = container.find('.autocomplete-suggestion:eq(' + i + ')');
					el.wrapInner($('<div />'));

					if (typeof item.image != 'undefined' && item.image) {
						var img = $('<span />', { class: 'autocomplete-image' });
						img.css({
							'background-image': "url('" + item.image + "')",
						});

						el.prepend(img);
					}
				}
			},
			onSelect: $.proxy(this.choose, this),
			transformResult: function (response) {
				var results = JSON.parse(response);

				return {
					query: '',
					suggestions: results.suggestions,
				};
			},
		});

		this.clearToggle.on('click', () => {
			this.clearBtn();
		});

		if (this.id > 0) {
			var url = route(data.route, { id: this.id });
			$.getJSON(url, (resp) => {
				if (resp.suggestions) {
					this.choose(resp.suggestions[0]);
				}
			});
		} else {
			this.clear();
		}

		this.change();
	}

	clear() {
		this.selected = null;

		if (!this.dataField.val()) {
			this.trigger('change', null);
			return;
		}

		this.dataField.val('');
		this.searchField.val('').prop('readonly', false).trigger('itemremove');
		this.clearToggle.prop('disabled', true);
		this.id = 0;

		this.change();
	}

	change() {
		this.element.toggleClass('selected', this.id > 0 || this.id.length > 0);
		this.trigger('change', null);
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

	choose(suggestion) {
		this.id = suggestion.data.id;
		this.dataField.val(this.id);

		this.searchField
			.prop('readonly', true)
			.val(suggestion.value)
			.trigger('itemselect', suggestion);

		if (this.clearToggle.length) {
			this.clearToggle.prop('disabled', false);
		}

		this.selected = suggestion;
		this.trigger('select', suggestion);
		this.change();
	}

	focus() {
		this.searchField.focus();
	}

	clearBtn() {
		this.clear();
		this.focus();
	}

	value() {
		return this.selected;
	}
}
