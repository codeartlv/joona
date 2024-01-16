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

export function removeSpinner(context) {
	context.querySelectorAll('.spinner').forEach((e) => {
		e.remove();
	});
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

export function parseUrl(url, params) {
	const matches = url.match(/{(.*?)}/g);
	const usedParams = new Set();

	if (matches) {
		matches.forEach((match) => {
			const key = match.slice(1, -1); // Removes the curly braces

			if (params[key] !== undefined && params[key] !== null) {
				url = url.replace(match, encodeURIComponent(params[key]));
				usedParams.add(key);
			}
		});
	}

	const getParams = Object.keys(params)
		.filter((key) => !usedParams.has(key))
		.map((key) => `${key}=${encodeURIComponent(params[key])}`);

	if (getParams.length) {
		url += '?' + getParams.join('&');
	}

	return url;
}
