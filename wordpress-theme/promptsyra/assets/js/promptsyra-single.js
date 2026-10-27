/**
 * PromptSyra Single-Post Interactive Script (v2.2.0)
 *
 * Handles:
 * 1. Independent View Prompt / Collapse Prompt toggles (.ps-btn-view-prompt & legacy .az-btn-view-prompt).
 * 2. Clean prompt-only Copy Prompt clipboard copying with Clipboard API + textarea execCommand fallback.
 * 3. Collapsible native Table of Contents toggle ([data-ps-toc-toggle]).
 * 4. Accessible validation feedback & genuine loading state on the native WordPress comment form (.ps-comment-form).
 */
(function () {
	'use strict';

	function copyCleanTextToClipboard(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}

		return new Promise(function (resolve, reject) {
			var textArea = document.createElement('textarea');
			textArea.value = text;
			textArea.setAttribute('readonly', '');
			textArea.style.position = 'fixed';
			textArea.style.top = '-9999px';
			textArea.style.left = '-9999px';
			textArea.style.opacity = '0';
			document.body.appendChild(textArea);
			textArea.focus();
			textArea.select();

			try {
				var ok = document.execCommand('copy');
				document.body.removeChild(textArea);
				if (ok) {
					resolve();
				} else {
					reject(new Error('Copy command returned false'));
				}
			} catch (err) {
				document.body.removeChild(textArea);
				reject(err);
			}
		});
	}

	function clearFieldError(field) {
		field.removeAttribute('aria-invalid');
		var group = field.closest('.ps-comment-field-group');
		if (!group) return;
		var existingErr = group.querySelector('.ps-comment-field-error');
		if (existingErr) {
			existingErr.remove();
		}
	}

	function showFieldError(field, message) {
		clearFieldError(field);
		field.setAttribute('aria-invalid', 'true');
		var group = field.closest('.ps-comment-field-group');
		if (!group) return;
		var errP = document.createElement('p');
		errP.className = 'ps-comment-field-error';
		errP.setAttribute('role', 'alert');
		errP.textContent = message;
		group.appendChild(errP);
	}

	function initSinglePostEnhancements() {
		// 1. Mark short prompt previews so fade overlay hides automatically
		var wrappers = document.querySelectorAll('.ps-prompt-content-wrapper, .az-prompt-content-wrapper');
		wrappers.forEach(function (wrapper) {
			if (wrapper.scrollHeight <= 104) {
				wrapper.classList.add('is-short');
			}
		});

		// 2. Progressive Accessible Validation & Submit State for Native WordPress Comment Form
		var commentForm = document.querySelector('form.ps-comment-form');
		if (commentForm) {
			commentForm.setAttribute('novalidate', 'novalidate');

			var trackedFields = commentForm.querySelectorAll('.ps-comment-input, .ps-comment-textarea');
			trackedFields.forEach(function (field) {
				field.addEventListener('input', function () {
					if (field.getAttribute('aria-invalid') === 'true') {
						clearFieldError(field);
					}
				});
			});

			commentForm.addEventListener('submit', function (event) {
				var firstInvalid = null;

				var commentEl = commentForm.querySelector('#comment');
				if (commentEl && commentEl.hasAttribute('required') && !commentEl.value.trim()) {
					showFieldError(commentEl, 'Please enter your comment before submitting.');
					firstInvalid = firstInvalid || commentEl;
				} else if (commentEl) {
					clearFieldError(commentEl);
				}

				var authorEl = commentForm.querySelector('#author');
				if (authorEl && authorEl.hasAttribute('required') && !authorEl.value.trim()) {
					showFieldError(authorEl, 'Please enter your name.');
					firstInvalid = firstInvalid || authorEl;
				} else if (authorEl) {
					clearFieldError(authorEl);
				}

				var emailEl = commentForm.querySelector('#email');
				if (emailEl) {
					var emailVal = emailEl.value.trim();
					if (emailEl.hasAttribute('required') && !emailVal) {
						showFieldError(emailEl, 'Please enter your email address.');
						firstInvalid = firstInvalid || emailEl;
					} else if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
						showFieldError(emailEl, 'Please enter a valid email address (for example, name@example.com).');
						firstInvalid = firstInvalid || emailEl;
					} else {
						clearFieldError(emailEl);
					}
				}

				var urlEl = commentForm.querySelector('#url');
				if (urlEl) {
					var urlVal = urlEl.value.trim();
					if (urlVal && !/^https?:\/\/.+/i.test(urlVal)) {
						showFieldError(urlEl, 'Please enter a valid URL starting with https:// or http:// (or leave optional field blank).');
						firstInvalid = firstInvalid || urlEl;
					} else {
						clearFieldError(urlEl);
					}
				}

				if (firstInvalid) {
					event.preventDefault();
					firstInvalid.focus();
					return;
				}

				// Form is valid: allow native POST to wp-comments-post.php and show genuine loading state
				var submitBtn = commentForm.querySelector('.ps-comment-submit-btn');
				if (submitBtn) {
					var labelSpan = submitBtn.querySelector('.ps-submit-btn-label');
					if (labelSpan && !labelSpan.getAttribute('data-orig-text')) {
						labelSpan.setAttribute('data-orig-text', labelSpan.textContent || 'Post Comment');
					}
					submitBtn.classList.add('is-submitting');
					submitBtn.setAttribute('aria-busy', 'true');
					if (labelSpan) {
						labelSpan.textContent = 'Posting Comment...';
					}
					// Defer disabling until after browser serializes form fields (preserves name="submit" in POST)
					setTimeout(function () {
						submitBtn.disabled = true;
					}, 0);
				}
			});

			window.addEventListener('pageshow', function () {
				var submitBtn = commentForm.querySelector('.ps-comment-submit-btn');
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.classList.remove('is-submitting');
					submitBtn.removeAttribute('aria-busy');
					var labelSpan = submitBtn.querySelector('.ps-submit-btn-label');
					if (labelSpan && labelSpan.getAttribute('data-orig-text')) {
						labelSpan.textContent = labelSpan.getAttribute('data-orig-text');
					}
				}
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initSinglePostEnhancements);
	} else {
		initSinglePostEnhancements();
	}

	document.addEventListener('click', function (event) {
		var target = event.target;
		if (!target || typeof target.closest !== 'function') {
			return;
		}

		// 1. View Prompt / Collapse Prompt Toggle
		var viewBtn = target.closest('.ps-btn-view-prompt, .az-btn-view-prompt');
		if (viewBtn) {
			event.preventDefault();
			var card = viewBtn.closest('.ps-ai-prompt-box, .az-ai-prompt-box');
			if (!card) {
				return;
			}

			var wrapper = card.querySelector('.ps-prompt-content-wrapper, .az-prompt-content-wrapper');
			var textSpan = viewBtn.querySelector('.ps-view-text, .az-view-text');
			var eyeIcon = viewBtn.querySelector('.ps-icon-eye, .az-icon-eye');
			var eyeOffIcon = viewBtn.querySelector('.ps-icon-eye-off, .az-icon-eye-off');

			if (!wrapper) {
				return;
			}

			var isExpanded =
				wrapper.classList.contains('is-expanded') || wrapper.classList.contains('az-expanded');

			if (isExpanded) {
				wrapper.classList.remove('is-expanded', 'az-expanded');
				viewBtn.setAttribute('aria-expanded', 'false');
				if (textSpan) textSpan.textContent = 'View Prompt';
				if (eyeIcon) eyeIcon.style.display = 'inline-block';
				if (eyeOffIcon) eyeOffIcon.style.display = 'none';
			} else {
				wrapper.classList.add('is-expanded', 'az-expanded');
				viewBtn.setAttribute('aria-expanded', 'true');
				if (textSpan) textSpan.textContent = 'Collapse Prompt';
				if (eyeIcon) eyeIcon.style.display = 'none';
				if (eyeOffIcon) eyeOffIcon.style.display = 'inline-block';
			}
			return;
		}

		// 2. Copy Prompt Button (Copies ONLY the clean prompt string, never button labels)
		var copyBtn = target.closest('.ps-btn-copy-prompt, .az-btn-copy-prompt');
		if (copyBtn) {
			event.preventDefault();
			var promptText = copyBtn.getAttribute('data-prompt') || '';

			if (!promptText) {
				var parentCard = copyBtn.closest('.ps-ai-prompt-box, .az-ai-prompt-box');
				var previewEl = parentCard
					? parentCard.querySelector('.ps-prompt-text-preview, .az-prompt-text-preview')
					: null;
				if (previewEl) {
					promptText = (previewEl.textContent || '').trim();
				}
			}

			if (!promptText) {
				return;
			}

			copyCleanTextToClipboard(promptText)
				.then(function () {
					var labelSpan = copyBtn.querySelector('.ps-btn-text, .az-btn-text');
					var copyIcon = copyBtn.querySelector('.ps-icon-copy, .az-icon-copy');
					var checkIcon = copyBtn.querySelector('.ps-icon-check, .az-icon-check');
					var originalLabel = labelSpan ? labelSpan.getAttribute('data-orig-label') || labelSpan.textContent : 'Copy Prompt';

					if (labelSpan && !labelSpan.getAttribute('data-orig-label')) {
						labelSpan.setAttribute('data-orig-label', originalLabel || 'Copy Prompt');
					}

					copyBtn.classList.add('is-copied', 'az-copied');
					if (labelSpan) labelSpan.textContent = 'Copied!';
					if (copyIcon) copyIcon.style.display = 'none';
					if (checkIcon) checkIcon.style.display = 'inline-block';

					if (copyBtn._psResetTimer) {
						clearTimeout(copyBtn._psResetTimer);
					}

					copyBtn._psResetTimer = setTimeout(function () {
						copyBtn.classList.remove('is-copied', 'az-copied');
						if (labelSpan) {
							labelSpan.textContent = labelSpan.getAttribute('data-orig-label') || 'Copy Prompt';
						}
						if (copyIcon) copyIcon.style.display = 'inline-block';
						if (checkIcon) checkIcon.style.display = 'none';
					}, 2400);
				})
				.catch(function () {
					var labelSpan = copyBtn.querySelector('.ps-btn-text, .az-btn-text');
					if (labelSpan) {
						labelSpan.textContent = 'Select text to copy';
						setTimeout(function () {
							labelSpan.textContent = 'Copy Prompt';
						}, 2400);
					}
				});
			return;
		}

		// 3. Native Table of Contents Show / Hide Toggle
		var tocBtn = target.closest('[data-ps-toc-toggle]');
		if (tocBtn) {
			event.preventDefault();
			var tocBox = tocBtn.closest('[data-ps-toc]');
			if (!tocBox) return;
			var list = tocBox.querySelector('[data-ps-toc-list]');
			var label = tocBtn.querySelector('[data-ps-toc-toggle-label]');
			if (!list) return;

			var collapsed = list.classList.toggle('is-collapsed');
			tocBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
			if (label) {
				label.textContent = collapsed ? 'Show' : 'Hide';
			}
		}
	});
})();
