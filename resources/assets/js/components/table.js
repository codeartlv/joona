import Sortable from 'sortablejs';

export default class Table {
	constructor(el, params) {
		params = {
			sortable: null,
			...params,
		};

		if (params.sortable) {
			let sortParams = {
				animation: 150,
				delay: 500,
				delayOnTouchOnly: true,
				preventOnFilter: false,
				direction: 'vertical',
				onEnd: (evt) => {
					let items = [];

					el.querySelectorAll('tbody tr').forEach((e) => {
						items.push(e.dataset.id);
					});

					let csrfToken = document
						.querySelector('meta[name="csrf-token"]')
						.getAttribute('content');

					fetch(params.sortable, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': csrfToken,
						},
						body: JSON.stringify({
							ids: items,
						}),
					});
				},
			};

			let handleSelector = '[data-role="drag-handle"]';

			let handles = el.querySelectorAll(handleSelector);

			if (handles.length) {
				sortParams.handle = handleSelector;
			}

			new Sortable(el.querySelector('tbody'), sortParams);
		}
	}
}
