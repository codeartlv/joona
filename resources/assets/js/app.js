/*
|--------------------------------------------------------------------------
| GLOBAL IMPORTS
|--------------------------------------------------------------------------
*/

/*
| Javascript
|--------------------------------------------------------------------------
*/
import 'bootstrap';

import Runtime from './runtime.js';
import Components from './handlers/components.js';
import Admin from './handlers/admin.js';

/*
| CSS
|--------------------------------------------------------------------------
*/
import './../scss/app.scss';

let dataUrl = typeof DATA_URL !== 'undefined' ? DATA_URL : '/data.json';

/*
|--------------------------------------------------------------------------
| GLOBAL
|--------------------------------------------------------------------------
*/

window.Runtime = new Runtime();

function initializeRuntime(url) {
	return fetch(dataUrl)
		.then((response) => {
			if (!response.ok) {
				throw new Error(`Network response for ${url} failed.`);
			}

			return response.json();
		})
		.catch((error) => {
			console.error('Failed to fetch data:', error);
		});
}

function initializeDocument() {
	window.addEventListener('resize', () => {
		// Add viewport height variable to document. This property allows to
		// precisely set viewport height, accounting for virtual keyboards on
		// mobile devices.

		var vh = window.innerHeight * 0.01;
		document.documentElement.style.setProperty('--vh', `${vh}px`);

		// Save window scrollbar width to be used in CSS.
		var scrollWidth = Math.ceil(
			(window.innerWidth - document.documentElement.clientWidth) / 2
		);
		document.documentElement.style.setProperty(
			'--body-scroll-width',
			`${scrollWidth}px`
		);
	});

	window.dispatchEvent(new Event('resize'));

	window.addEventListener('scroll', () => {
		// Save true window scroll position to be used in CSS.
		document.documentElement.style.setProperty(
			'--body-scroll-position',
			`${window.scrollY}px`
		);
	});
	window.dispatchEvent(new Event('scroll'));

	window.Runtime.addHandlers(Components, Admin);
	window.Runtime.init(document.body);
}

initializeRuntime().then((data) => {
	data = {
		routes: [],
		translations: [],
		...data,
	};

	window.Runtime.addRoutes(data.routes);
	window.Runtime.addTranslations(data.translations);

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initializeDocument);
	} else {
		initializeDocument();
	}
});
