/**
 * plugins/generic/openAIREBrokerService/js/workflowEnrichments.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Article-level OpenAIRE enrichments in the OJS 3.5 / 3.6 editorial workflow.
 *
 * OJS 3.5 rebuilt the editorial workflow as a Vue single-page application and
 * removed the old "Template::Workflow::Publication" Smarty hook. We integrate
 * through the documented front-end extension API on "pkp.registry":
 *
 *   - registerComponent()  -> register a global Vue component
 *   - storeExtend()        -> tap into the "workflow" Pinia store via its extender
 *                             (getMenuItems / getPrimaryItems)
 *
 * Placement note: enrichments are read-only and per *article* (submission), so we
 * add a single "OpenAIRE enrichments" entry under the Publication navigation,
 * independent of the per-version publication tabs. Those tabs only exist when the
 * current user is an assigned editor on the submission (permissions.canAccess
 * Publication); the enrichments view does not require that — access is still
 * enforced server-side by OpenAIREBrokerServiceGridHandler.
 */
(function () {
	'use strict';

	if (typeof pkp === 'undefined' || !pkp.registry || !pkp.modules || !pkp.modules.vue) {
		return;
	}

	var MENU_PRIMARY = 'openaireEnrichments'; // our selectedMenuState.primaryMenuItem
	var EDITORIAL_DASHBOARD = 'editorialDashboard';

	function getConfig() {
		return window.OpenAIREBrokerServiceConfig || {};
	}

	/**
	 * Vue component rendered in the workflow primary area. It reuses the existing
	 * read-only enrichments grid by loading its component-router URL (mirroring
	 * ui-library's AjaxModalWrapper: fetch, then inject the returned HTML so the
	 * legacy PKP grid bootstraps itself).
	 */
	pkp.registry.registerComponent('OpenaireBrokerServiceEnrichments', {
		name: 'OpenaireBrokerServiceEnrichments',
		props: {
			submission: {type: Object, required: true},
		},
		data: function () {
			return {hasError: false};
		},
		mounted: function () {
			var $ = window.$ || window.jQuery;
			var base = getConfig().gridUrlBase || '';
			var container = this.$refs.container;
			var self = this;

			if (!$ || !base || !this.submission || !this.submission.id || !container) {
				this.hasError = true;
				return;
			}

			var separator = base.indexOf('?') === -1 ? '?' : '&';
			var url = base + separator + 'submissionId=' + encodeURIComponent(this.submission.id);

			$.get(
				url,
				function (response) {
					var content = response && response.content !== undefined ? response.content : response;
					if (typeof content === 'string') {
						$(container).html(content);
					} else {
						self.hasError = true;
					}
				},
				'json',
			).fail(function () {
				self.hasError = true;
			});
		},
		render: function () {
			var h = pkp.modules.vue.h;
			var children = [h('div', {ref: 'container'})];
			if (this.hasError) {
				children.push(
					h('p', {class: 'pkpNotification pkpNotification--warning'}, getConfig().errorMessage || ''),
				);
			}
			return h('div', {class: 'pkpWorkflow__contentPanel openaireBrokerServiceEnrichments'}, children);
		},
	});

	pkp.registry.storeExtend('workflow', function (piniaContext) {
		var store = piniaContext.store;
		if (!store || !store.extender) {
			return;
		}

		var label = getConfig().label || 'OpenAIRE';

		// 1) Add a single "OpenAIRE enrichments" navigation entry per submission.
		store.extender.extendFn('getMenuItems', function (menuItems, args) {
			try {
				if (!args || args.dashboardPage !== EDITORIAL_DASHBOARD) {
					return menuItems;
				}
				var submission = args.submission;
				if (!submission || !submission.id || !Array.isArray(menuItems)) {
					return menuItems;
				}

				var key = MENU_PRIMARY + '_submission_' + submission.id;
				var entry = {
					key: key,
					label: label,
					state: {
						primaryMenuItem: MENU_PRIMARY,
						submissionId: submission.id,
						title: label,
					},
				};

				// Preferred: attach under the existing "Publication" group, so it sits
				// with the other publication-related entries. This group is always
				// present on the editorial dashboard, even without an assignment.
				var publicationTop = menuItems.find(function (item) {
					return item && item.key === 'publication';
				});

				if (publicationTop && Array.isArray(publicationTop.items)) {
					var existsInPub = publicationTop.items.some(function (it) {
						return it && it.key === key;
					});
					if (!existsInPub) {
						publicationTop.items.push(entry);
					}
				} else {
					// Defensive fallback: expose it as its own top-level entry.
					var existsTop = menuItems.some(function (it) {
						return it && it.key === key;
					});
					if (!existsTop) {
						menuItems.push({
							key: key,
							label: label,
							icon: 'View',
							state: entry.state,
						});
					}
				}
			} catch (e) {
				if (window.console) {
					console.error('[OpenAIRE Broker Service] getMenuItems extension failed', e);
				}
			}
			return menuItems;
		});

		// 2) Render our component when that entry is selected.
		store.extender.extendFn('getPrimaryItems', function (items, args) {
			try {
				var state = args && args.selectedMenuState;
				if (state && state.primaryMenuItem === MENU_PRIMARY && args.submission) {
					return [
						{
							component: 'OpenaireBrokerServiceEnrichments',
							props: {submission: args.submission},
						},
					];
				}
			} catch (e) {
				if (window.console) {
					console.error('[OpenAIRE Broker Service] getPrimaryItems extension failed', e);
				}
			}
			return items;
		});
	});
})();
