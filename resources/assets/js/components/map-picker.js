import L from 'leaflet';

export default class MapPicker {
	constructor(container, options = {}) {
		options = {
			center: '40,0',
			zoom: 5,
			precision: 6,
			...options,
		};

		options.center = options.center.split(',').map((coord) => parseFloat(coord));

		if (options.center.length !== 2 || options.center.some(isNaN)) {
			throw new Error('Invalid center coordinates provided');
		}

		this.options = options;
		this.container = container;
		this.marker = null;

		this.mapElement = document.createElement('div');
		this.container.appendChild(this.mapElement);
		this.valueField = this.container.querySelector('input[data-role="value"]');

		const mapOptions = {
			zoom: options.zoom,
			zoomControl: false,
			attributionControl: false,
			center: L.latLng(options.center),
		};

		this.map = L.map(this.mapElement, mapOptions).addControl(
			L.control.zoom({ position: 'bottomright' })
		);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '© OpenStreetMap contributors',
		}).addTo(this.map);

		this.map.on('click', this.onMapClick.bind(this));

		const initialValue = this.valueField.value;

		if (initialValue) {
			const initialCoords = initialValue.split(',').map((coord) => parseFloat(coord));

			if (initialCoords.length === 2 && !initialCoords.some(isNaN)) {
				const initialLatLng = L.latLng(initialCoords);
				this.onMapClick({ latlng: initialLatLng });
				this.map.setView(initialLatLng);
			}
		}
	}

	onMapClick(e) {
		const customIcon = L.icon({
			iconUrl: '/vendor/joona/images/map/marker-icon.png',
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [-3, -76],
		});

		if (this.marker === null) {
			this.marker = L.marker(e.latlng, {
				draggable: true,
				icon: customIcon,
			}).addTo(this.map);

			this.marker.on('drag', this.updateLocation.bind(this));
		} else {
			this.marker.setLatLng(e.latlng);
		}

		this.updateLocation();
	}

	updateLocation() {
		if (this.marker) {
			const roundedLocation = this.roundLocation(this.marker.getLatLng());
			this.valueField.value = `${roundedLocation.lat},${roundedLocation.lng}`;
		}
	}

	roundLocation(loc) {
		return loc
			? L.latLng(
					parseFloat(loc.lat).toFixed(this.options.precision),
					parseFloat(loc.lng).toFixed(this.options.precision)
			  )
			: loc;
	}
}
