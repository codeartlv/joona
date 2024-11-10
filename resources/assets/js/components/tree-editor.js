import { parseJsonLd } from './../helpers';
import Sortable from 'sortablejs';
import Modal from '../components/modal.js';
import ConfirmDialog from '../components/confirm-dialog.js';
import axios from 'axios';

export default class TreeEditor {
	constructor(el, params) {
		this.nodes = parseJsonLd(el.querySelector('[data-role="tree-editor.data"]'));
		this.container = el;
		this.sectionTemplate = el.querySelector('template[data-role="section"]');
		this.nodeTemplate = el.querySelector('template[data-role="node"]');
		this.selectedNode = false;
		this.selectedNodes = {};

		this.params = Object.assign(
			{
				editroute: '',
				delroute: '',
				sortroute: '',
				sortlevels: '',
				selected: 0,
				depth: 0,
				sortable: 0,
				itemoptions: {},
			},
			params
		);

		let sortLevels = String(this.params.sortlevels);

		this.params.sortlevels = sortLevels.length > 0 ? sortLevels.split(',').map(Number) : [];

		this.createNodeList();

		if (this.params.selected) {
			const nodes = this.getPathById(this.params.selected);
			if (nodes && nodes.length) {
				nodes.reverse();
				nodes.forEach((node) => this.selectNode(node));
			}
		}
	}

	getLevel(node) {
		const traverse = (nodes, nodeId, level) => {
			for (let i = 0; i < nodes.length; i++) {
				if (!nodes[i]) continue;

				if (nodes[i].id === nodeId) {
					return level;
				}

				const result = traverse(nodes[i].childs, nodeId, level + 1);
				if (result !== -1) {
					return result;
				}
			}
			return -1;
		};

		return traverse(this.nodes, node.id, 0);
	}

	getNodeChilds(nodeId) {
		if (!nodeId) {
			return this.nodes;
		}

		const traverse = (nodes, nodeId) => {
			for (let i = 0; i < nodes.length; i++) {
				if (nodes[i].id === nodeId) {
					return nodes[i].childs;
				}

				const result = traverse(nodes[i].childs, nodeId);
				if (result !== false) {
					return result;
				}
			}
			return false;
		};

		return traverse(this.nodes, nodeId);
	}

	getNodeParent(nodeId) {
		const traverse = (nodes, nodeId) => {
			for (let i = 0; i < nodes.length; i++) {
				if (nodes[i].id === nodeId) {
					return true;
				}

				const result = traverse(nodes[i].childs, nodeId);
				if (result === true) {
					return nodes[i];
				}
				if (typeof result === 'object') {
					return result;
				}
			}
			return false;
		};

		for (let i = 0; i < this.nodes.length; i++) {
			if (this.nodes[i].id === nodeId) {
				return false;
			}
		}

		return traverse(this.nodes, nodeId);
	}

	getNodeById(nodeId) {
		nodeId = Number(nodeId);

		const traverse = (nodes, nodeId) => {
			for (let i = 0; i < nodes.length; i++) {
				if (nodes[i].id === nodeId) {
					return nodes[i];
				}

				const result = traverse(nodes[i].childs, nodeId);
				if (result !== false) {
					return result;
				}
			}
			return false;
		};

		return traverse(this.nodes, nodeId);
	}

	getPathById(nodeId) {
		const traverse = (nodes, nodeId) => {
			for (let i = 0; i < nodes.length; i++) {
				if (nodes[i].id === nodeId) {
					return [nodes[i]];
				}

				const result = traverse(nodes[i].childs, nodeId);
				if (result !== false) {
					return result.concat(nodes[i]);
				}
			}
			return false;
		};

		return traverse(this.nodes, nodeId);
	}

	getNodeElementById(nodeId) {
		return this.container.querySelector(`[data-role="node"][data-id="${nodeId}"]`);
	}

	getSectionByNode(node) {
		const level = this.getLevel(node);
		return this.getSectionByLevel(level);
	}

	getSectionByLevel(level) {
		const section = this.container.querySelector(
			`section[data-level="${level}"][data-role="level"]`
		);
		return section || false;
	}

	createSection(level) {
		const section = this.sectionTemplate.content.cloneNode(true);
		this.container.appendChild(section);

		const sections = this.container.querySelectorAll('section');
		const newSection = sections[sections.length - 1];
		newSection.setAttribute('data-level', level);

		return newSection;
	}

	editNode(id, parentId, level) {
		const modal = new Modal('node-edit');

		modal.open(
			route(this.params.editroute, {
				id,
				parent_id: parentId,
				level,
			})
		);
	}

	deleteNode(id) {
		id = Number(id);

		let confirmDialog = new ConfirmDialog(
			trans('joona::common.confirm'),
			trans('joona::common.delete_node_confirm'),
			[
				{
					caption: trans('joona::common.cancel'),
					role: 'secondary',
					callback: () => {
						return false;
					},
				},
				{
					caption: trans('joona::common.ok'),
					role: 'primary',
					callback: () => {
						const node = this.getNodeById(id);
						const level = this.getLevel(node);

						if (this.selectedNodes[level] && this.selectedNodes[level] === id) {
							this.closeLevelsAfter(level);
						}

						const nodeEl = this.getNodeElementById(id);
						nodeEl.remove();

						const traverse = (nodes, nodeId) => {
							for (let i = 0; i < nodes.length; i++) {
								if (nodes[i].id === nodeId) {
									nodes.splice(i, 1);
									return true;
								}
								traverse(nodes[i].childs, nodeId);
							}
							return false;
						};

						traverse(this.nodes, id);

						axios.post(
							route(this.params.delroute, { id: id }),
							`id=${encodeURIComponent(id)}`,
							{
								headers: {
									'Content-Type': 'application/x-www-form-urlencoded',
								},
							}
						);

						return true;
					},
				},
			]
		);

		confirmDialog.open();
	}

	closeLevelsAfter(level) {
		const sections = this.container.querySelectorAll('section[data-role="level"]');

		sections.forEach((s) => {
			const lvl = parseInt(s.getAttribute('data-level'), 10);

			if (lvl > level) {
				delete this.selectedNodes[lvl];
				s.remove();
			}
		});
	}

	selectNode(node) {
		const section = this.getSectionByNode(node);
		const nav = section.querySelector('[data-role="nodes"]');

		this.selectedNodes[section.getAttribute('data-level')] = node.id;

		const nodeEl = nav.querySelector(`[data-role="node"][data-id="${node.id}"]`);
		if (nodeEl.classList.contains('active')) return;

		nav.querySelectorAll('[data-role="node"]').forEach((n) => n.classList.remove('active'));
		nodeEl.classList.add('active');

		this.selectedNode = node;
		section.classList.add('node-selected');

		const newSection = this.createNodeList(node);

		if (newSection) {
			newSection.classList.remove('node-selected');
			const lvl = parseInt(newSection.getAttribute('data-level'), 10);
			this.closeLevelsAfter(lvl);
		}
	}

	createNodeList(node) {
		let childs;
		let level;

		if (typeof node === 'object') {
			childs = node.childs;
			level = this.getLevel(node) + 1;
		} else {
			childs = this.nodes;
			level = 0;
		}

		if (this.params.depth > 0 && level + 1 > this.params.depth) {
			return;
		}

		let section = this.getSectionByLevel(level);

		if (!section) {
			section = this.createSection(level);
		}

		const nav = section.querySelector('[data-role="nodes"]');
		nav.innerHTML = '';
		nav.setAttribute('data-parent', node ? node.id : '');

		for (let i = 0; i < childs.length; i++) {
			const currentNode = childs[i];

			const nodeEl = this.nodeTemplate.content.cloneNode(true);
			nav.appendChild(nodeEl);

			const newNodeEl = nav.lastElementChild;
			newNodeEl.setAttribute('data-id', currentNode.id);
			newNodeEl.setAttribute('data-role', 'node');

			if (currentNode.class.length) {
				newNodeEl.classList.add(...currentNode.class);
			}

			newNodeEl.dataset.id = currentNode.id;
			newNodeEl.dataset.role = 'node';

			newNodeEl.querySelectorAll('[data-field]').forEach((item) => {
				const fieldName = item.dataset.field;
				const isAttr = item.dataset.attr;

				let value = '';

				if (currentNode[fieldName]) {
					value = currentNode[fieldName];
				} else if (currentNode.data[fieldName]) {
					value = currentNode.data[fieldName];
				}

				if (isAttr) {
					item.setAttribute(isAttr, value);
				} else {
					item.textContent = value;
				}
			});

			const editButton = newNodeEl.querySelector('[data-action="edit-node"]');
			if (editButton) {
				editButton.addEventListener('click', (e) => {
					e.stopPropagation();
					const nodeId = e.currentTarget.closest('[data-role="node"]').dataset.id;
					this.editNode(nodeId, -1, level);
				});
			}

			const deleteButton = newNodeEl.querySelector('[data-action="delete-node"]');
			if (deleteButton) {
				deleteButton.addEventListener('click', (e) => {
					e.stopPropagation();
					const nodeId = e.currentTarget.closest('[data-role="node"]').dataset.id;
					this.deleteNode(nodeId);
				});
			}

			if (this.params.itemoptions) {
				for (const o in this.params.itemoptions) {
					const opt = this.params.itemoptions[o];
					const btn = newNodeEl.querySelector(`[data-action="${o}"]`);

					if (!btn) continue;

					btn.classList.toggle('d-none', !opt.visible(currentNode));

					btn.addEventListener('click', (e) => {
						e.stopPropagation();
						const nodeId = e.currentTarget.closest('[data-role="node"]').dataset.id;
						opt.click(nodeId);
					});
				}
			}

			if (
				this.params.sortable &&
				(this.params.sortlevels.length == 0 || this.params.sortlevels.includes(level))
			) {
				const em = document.createElement('em');
				em.setAttribute('data-role', 'sort-handle');
				em.className = 'tree-editor__drag';

				const iElement = document.createElement('i');
				iElement.className = 'material-symbols-outlined';
				iElement.textContent = 'drag_handle';

				em.appendChild(iElement);
				newNodeEl.insertBefore(em, newNodeEl.firstChild);
			}

			newNodeEl.addEventListener('click', () => this.selectNode(currentNode));
		}

		if (this.params.sortable) {
			new Sortable(nav, {
				handle: '[data-role="sort-handle"]',
				onEnd: () => {
					const ids = Array.from(nav.querySelectorAll('[data-role="node"]')).map(
						(n) => n.dataset.id
					);

					const parent = Number(nav.getAttribute('data-parent'));

					if (this.params.sortroute) {
						axios.post(route(this.params.sortroute), { categories: ids });
					}

					const childList = this.getNodeChilds(parent);

					if (childList !== false) {
						const order = {};

						ids.forEach((id, idx) => {
							order[id] = idx;
						});

						childList.sort((a, b) => order[a.id] - order[b.id]);
					}
				},
			});
		}

		const addNodeButton = section.querySelector('[data-action="add-node"]');
		if (addNodeButton) {
			addNodeButton.addEventListener('click', (e) => {
				e.stopPropagation();

				const lvl = parseInt(
					e.currentTarget.closest('section').getAttribute('data-level'),
					10
				);

				let parentId = 0;

				if (lvl > 0) {
					parentId = this.selectedNodes[lvl - 1];
				}

				this.editNode(0, parentId, lvl);
			});
		}

		return section;
	}
}
