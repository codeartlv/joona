import BootstrapTooltip from 'bootstrap/js/dist/tooltip';

export default class CopyText {
	constructor(element, params) {
		params = {
			container: null,
			...params,
		};

		const container = params.container ? document.querySelector(params.container) : element;

		const tooltip = BootstrapTooltip.getOrCreateInstance(container, {
			trigger: 'manual',
			title: trans('joona::common.copied'),
		});

		element.addEventListener('click', () => {
			const input = document.createElement('textarea');
			input.value = params.text;

			document.body.appendChild(input);
			input.select();
			document.execCommand('Copy');
			input.remove();

			tooltip.show();

			setTimeout(function () {
				tooltip.hide();
			}, 1000);
		});
	}
}
