export function addSpinner(context, color) {
	context.insertAdjacentHTML(
		'beforeend',
		`
		<div class="spinner">
			<div class="spinner-border text-${color}" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
	`
	);
}

export function createElementFromHTML(htmlString) {
	const tempContainer = document.createElement('div');
	tempContainer.innerHTML = htmlString.trim();
	return tempContainer.firstElementChild;
}

export function removeSpinner(context) {
	context.querySelectorAll('.spinner').forEach((e) => {
		e.remove();
	});
}

export function setButtonLoading(buttonElement) {
	const buttonCaption = buttonElement.innerHTML;

	buttonElement.setAttribute('disabled', 'true');

	buttonElement.innerHTML = `
		<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
		<span class="visually-hidden" role="status">${buttonCaption}</span>
		&nbsp;
	`;

	buttonElement.dataset._title = buttonCaption;
}

export function unsetButtonLoading(buttonElement) {
	let buttonCaption = buttonElement.dataset._title || '';

	buttonElement.removeAttribute('disabled');
	buttonElement.innerHTML = buttonCaption;

	delete buttonElement.dataset._title;
}

export function parseJsonLd(element) {
	var jsonText = element.textContent;
	var data = [];

	try {
		data = JSON.parse(jsonText);
	} catch (errorText) {
		data = {};
	}

	return data;
}

export function parseRoute(arg, params) {
	// Initialize parameters object
	params = params || {};

	// Use a regular expression to extract the route and the parameters part
	const routePattern = /^([^\[]+)/;
	const paramsPattern = /\[([^\]]+)\]/;

	const routeMatch = arg.match(routePattern);
	const paramsMatch = arg.match(paramsPattern);

	// Extract route
	let route = routeMatch ? routeMatch[0] : '';

	// Extract and process parameters string if it exists
	if (paramsMatch) {
		let paramsString = paramsMatch[1];
		let paramsArray = paramsString.split(',');

		paramsArray.forEach((param) => {
			let [key, value] = param.split('=');
			params[key] = value;
		});
	}

	return window.route(route, params);
}
