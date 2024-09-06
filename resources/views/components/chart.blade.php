<div data-bind="components.chart" {{ $attributes->merge(['class' => 'component-chart']) }}>
	<canvas data-role="chart.canvas"></canvas>
	<script data-role="chart.data" type="application/ld+json">@json($data)</script>
</div>
