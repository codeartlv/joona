import 'material-symbols';
import 'bootstrap';
import Runtime from './runtime.js';

document.addEventListener(
	'focusin',
	(e) => {
		// If a modal is open, we stop the event from bubbling up to the Offcanvas focus trap
		if (window.JoonaModalInstance) {
			e.stopImmediatePropagation();
		}
	},
	{ capture: true },
);

export default window.Joona = new Runtime();
