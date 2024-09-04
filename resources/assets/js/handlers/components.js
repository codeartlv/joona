import Handler from './../handler.js';

import AjaxForm from '../components/ajax-form.js';
import PasswordValidator from '../components/password-validator.js';
import Datepicker from '../components/datepicker.js';
import Uploader from '../components/uploader.js';
import Autocomplete from '../components/autocomplete.js';
import MapPicker from '../components/map-picker.js';
import MultiSelect from '../components/multi-select.js';
import Gallery from '../components/gallery.js';
import CopyText from '../components/copy-text.js';

export default class Components extends Handler {
	static get pluginName() {
		return 'components';
	}

	form(el, params) {
		return new AjaxForm(el, params);
	}

	autocomplete(el, params) {
		return new Autocomplete(el, params);
	}

	passwordValidator(el, params) {
		return new PasswordValidator(el, params);
	}

	uploader(el, params) {
		return new Uploader(el, params);
	}

	datepicker(el, params) {
		return new Datepicker(el, params);
	}

	mapPicker(el, params) {
		return new MapPicker(el, params);
	}

	multiSelect(el, params) {
		return new MultiSelect(el, params);
	}

	gallery(el, params) {
		return new Gallery(el, params);
	}

	copyText(el, params) {
		return new CopyText(el, params);
	}
}
