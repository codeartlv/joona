import BootstrapTooltip from 'bootstrap/js/dist/tooltip';

export default class CopyText {
	constructor(element, params) {
		const tooltip = BootstrapTooltip.getOrCreateInstance(element, {
			trigger: 'click',
			title: trans('joona::common.copied'),
		});

		element.addEventListener('click', () => {
			const input = document.createElement('textarea');
			input.value = params.text;

			document.body.appendChild(input);
			input.select();
			document.execCommand('Copy');
			input.remove();

			setTimeout(function () {
				tooltip.hide();
			}, 1000);
		});
	}
}
