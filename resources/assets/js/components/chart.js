import { parseJsonLd } from './../helpers';
import Chart from 'chart.js/auto';

export default class ChartComponent {
	constructor(el, settings) {
		settings = {
			...settings,
		};

		const data = parseJsonLd(el.querySelector('[data-role="chart.data"]'));
		const canvas = el.querySelector('canvas[data-role="chart.canvas"]');

		this.chart = new Chart(canvas, data);
	}
}
