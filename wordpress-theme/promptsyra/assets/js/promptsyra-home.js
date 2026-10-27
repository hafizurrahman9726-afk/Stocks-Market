/**
 * PromptSyra Scoped Interactive UI Script (v2.0.0)
 *
 * Handles accessible navigation dropdowns, header search drawer, mobile drawer,
 * FAQ accordion controls, and non-destructive copy-prompt button support inside
 * standalone single-post articles.
 */
(function () {
	'use strict';

	var dropdownContainers = document.querySelectorAll('[data-ps-dropdown]');

	function closeAllDropdowns(exceptContainer) {
		dropdownContainers.forEach(function (container) {
			if (container === exceptContainer) {
				return;
			}
			var btn = container.querySelector('.ps-dropdown-trigger');
			var menu = container.querySelector('.ps-dropdown-menu');
			if (btn && menu) {
				btn.setAttribute('aria-expanded', 'false');
				menu.hidden = true;
			}
		});
	}

	dropdownContainers.forEach(function (container) {
		var trigger = container.querySelector('.ps-dropdown-trigger');
		var menu = container.querySelector('.ps-dropdown-menu');
		if (!trigger || !menu) {
			return;
		}

		trigger.addEventListener('click', function (event) {
			event.stopPropagation();
			var expanded = trigger.getAttribute('aria-expanded') === 'true';
			closeAllDropdowns(container);
			trigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			menu.hidden = expanded;
		});
	});

	document.addEventListener('click', function (event) {
		if (!event.target.closest('[data-ps-dropdown]')) {
			closeAllDropdowns(null);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeAllDropdowns(null);
		}
	});

	// Header Search Drawer
	var searchToggle = document.querySelector('[data-ps-search-toggle]');
	var searchDrawer = document.getElementById('ps-header-search-drawer');
	if (searchToggle && searchDrawer) {
		searchToggle.addEventListener('click', function () {
			var isOpen = searchToggle.getAttribute('aria-expanded') === 'true';
			searchToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
			searchDrawer.hidden = isOpen;
			if (!isOpen) {
				var input = searchDrawer.querySelector('input[type="search"]');
				if (input) {
					input.focus();
				}
			}
		});
	}

	// Mobile Navigation Drawer
	var mobileToggle = document.querySelector('[data-ps-mobile-toggle]');
	var mobileDrawer = document.getElementById('ps-mobile-drawer');
	if (mobileToggle && mobileDrawer) {
		mobileToggle.addEventListener('click', function () {
			var isOpen = mobileToggle.getAttribute('aria-expanded') === 'true';
			mobileToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
			mobileDrawer.hidden = isOpen;
		});
	}

	// Accessible FAQ Accordion
	var accordions = document.querySelectorAll('[data-ps-accordion]');
	accordions.forEach(function (accordion) {
		var triggers = accordion.querySelectorAll('.ps-faq-trigger');
		triggers.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var expanded = btn.getAttribute('aria-expanded') === 'true';
				var panelId = btn.getAttribute('aria-controls');
				var panel = panelId ? document.getElementById(panelId) : null;
				var icon = btn.querySelector('.ps-faq-icon');

				btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
				if (panel) {
					panel.hidden = expanded;
				}
				if (icon) {
					icon.textContent = expanded ? '+' : '−';
				}
			});
		});
	});

	// Homepage "Why Choose PromptSyra?" Sample Prompt Copy Button (v2.4.0)
	function copyTextWithFallback(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}
		return new Promise(function (resolve, reject) {
			try {
				var tempArea = document.createElement('textarea');
				tempArea.value = text;
				tempArea.setAttribute('readonly', '');
				tempArea.style.position = 'fixed';
				tempArea.style.opacity = '0';
				document.body.appendChild(tempArea);
				tempArea.select();
				var ok = document.execCommand('copy');
				document.body.removeChild(tempArea);
				if (ok) {
					resolve();
				} else {
					reject(new Error('Copy command unsuccessful'));
				}
			} catch (err) {
				reject(err);
			}
		});
	}

	var sampleCopyButtons = document.querySelectorAll('[data-ps-sample-copy]');
	sampleCopyButtons.forEach(function (sampleBtn) {
		var resetTimer = null;
		sampleBtn.addEventListener('click', function () {
			var promptText = sampleBtn.getAttribute('data-prompt') || '';
			if (!promptText.trim()) {
				return;
			}
			var labelNode = sampleBtn.querySelector('[data-ps-sample-btn-text]');
			var defaultLabel = sampleBtn.getAttribute('data-label-default') || 'Copy Sample Prompt';
			var copiedLabel = sampleBtn.getAttribute('data-label-copied') || 'Copied Sample';
			var specimenWrap = sampleBtn.closest('.ps-prompt-specimen');
			var liveRegion = specimenWrap ? specimenWrap.querySelector('[data-ps-sample-status]') : null;

			copyTextWithFallback(promptText.trim()).then(function () {
				sampleBtn.classList.add('is-copied');
				if (labelNode) {
					labelNode.textContent = copiedLabel;
				}
				if (liveRegion) {
					liveRegion.textContent = 'Sample prompt copied to clipboard.';
				}
				if (resetTimer) {
					clearTimeout(resetTimer);
				}
				resetTimer = setTimeout(function () {
					sampleBtn.classList.remove('is-copied');
					if (labelNode) {
						labelNode.textContent = defaultLabel;
					}
					if (liveRegion) {
						liveRegion.textContent = '';
					}
				}, 2000);
			});
		});
	});

	// Non-destructive Copy-Prompt button support inside standalone single-post articles
	document.addEventListener('click', function (event) {
		var copyBtn = event.target.closest(
			'.ps-single-entry [data-prompt], .ps-single-entry [data-clipboard-text], .ps-single-entry .copy-btn, .ps-single-entry .copy-prompt, .ps-single-entry .copy-prompt-btn'
		);
		if (!copyBtn) {
			return;
		}
		var explicitText =
			copyBtn.getAttribute('data-prompt') ||
			copyBtn.getAttribute('data-clipboard-text') ||
			copyBtn.getAttribute('data-copy');
		var targetText = explicitText;

		if (!targetText) {
			var wrapper = copyBtn.closest('.prompt-box, .wp-block-group, figure, pre, div');
			var codeNode = wrapper ? wrapper.querySelector('pre, code, .prompt-text, p') : null;
			if (codeNode) {
				targetText = codeNode.textContent;
			}
		}

		if (targetText && targetText.trim()) {
			copyTextWithFallback(targetText.trim()).then(function () {
				var originalLabel = copyBtn.textContent;
				copyBtn.textContent = 'Copied!';
				setTimeout(function () {
					copyBtn.textContent = originalLabel;
				}, 1600);
			});
		}
	});
})();
