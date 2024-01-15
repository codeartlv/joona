import Handler from './../handler.js';

import AjaxForm from '../components/ajax-form/ajax-form.js';
import PasswordValidator from '../components/password-validator.js';
import Datepicker from '../components/datepicker.js';

export default class Components extends Handler {
	static get pluginName() {
		return 'components';
	}

	form(el, params) {
		return new AjaxForm(el, params);
	}

	passwordValidator(el, params) {
		return new PasswordValidator(el, params);
	}

	calendar(el, params) {
		return new Datepicker(el, params);
	}
}
