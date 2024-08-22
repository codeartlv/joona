import Sortable from 'sortablejs';
import { parseJsonLd, createElementFromHTML } from './../helpers';
import Modal from '@joona/js/components/modal';
import Swiper from 'swiper';
import { Navigation, Pagination, EffectFade } from 'swiper/modules';

export default class Gallery {
	constructor(el, params) {
		this.element = el;
		this.images = parseJsonLd(el.querySelector('[data-role="gallery.images"]')).map((item) => ({
			...item,
			loaded: false,
		}));

		this.options = {
			sortable: false,
			loop: false,
			...params,
		};

		this.element.addEventListener('click', (event) => {
			const trigger = event.target.closest('[data-trigger="gallery.open"]');
			if (trigger) {
				this.open(trigger.dataset.index);
			}
		});

		if (this.options.sortable) {
			this.initSortable();
		}
	}

	initSortable() {
		new Sortable(this.element, {
			animation: 150,
			delay: 500,
			delayOnTouchOnly: true,
			preventOnFilter: false,
		});
	}

	open(index) {
		this.index = index;

		const modalHtml = `
		<div class="modal modal-gallery fade">
			<div class="modal-dialog modal-dialog-centered modal-dialog-gallery">
				<div class="modal-content">
					<div class="modal-header">
						<div data-role="gallery.image-title"></div>
						<button class="btn-close fullscreen-button" data-bs-dismiss="modal">
							<i class="material-symbols-outlined">close</i>
						</button>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>`;

		this.modalElement = createElementFromHTML(modalHtml);
		document.body.appendChild(this.modalElement);

		const modal = new Modal('gallery');
		modal.open(this.modalElement).then(() => {
			this.initGallery();
		});
	}

	onImageChange(newIndex) {
		let image = this.images[newIndex];

		let titleDisplay = this.modalElement.querySelector('[data-role="gallery.image-title"]');
		titleDisplay.innerHTML = image.title ? image.title : '';
	}

	initGallery() {
		const galleryHtml = `
		<div class="swiper swiper-fullscreen">
			<div class="swiper-wrapper"></div>
			<div class="swiper-button-prev">
				<i class="material-symbols-outlined fullscreen-button">arrow_back</i>
			</div>
  			<div class="swiper-button-next">
				<i class="material-symbols-outlined fullscreen-button">arrow_forward</i>
			</div>
		</div>`;

		this.carousel = createElementFromHTML(galleryHtml);
		this.imageContainer = this.carousel.querySelector('.swiper-wrapper');
		this.modalElement.querySelector('.modal-body').appendChild(this.carousel);

		this.images.forEach((image) => {
			const itemHtml = `
				<div class="swiper-slide">
					<div class="swiper-slide-content">
						<img src="${image.image}" loading="lazy" />
						<div class="swiper-lazy-preloader"></div>
					</div>
				</div>`;
			this.imageContainer.appendChild(createElementFromHTML(itemHtml));
		});

		new Swiper(this.carousel, {
			modules: [Navigation, Pagination, EffectFade],
			loop: this.options.loop,
			effect: 'fade',
			crossFade: true,
			on: {
				init: (e) => {
					this.onImageChange(e.activeIndex);
				},
				slideChange: (e) => {
					this.onImageChange(e.activeIndex);
				},
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
		});
	}
}
