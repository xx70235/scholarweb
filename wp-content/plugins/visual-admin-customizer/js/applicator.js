/* global wsVacApplicatorData */
(
	/**
	 * @param {Function} $
	 * @param {Object} data
	 * @param {Object<String, boolean>} data.replacementText
	 */
	function($, data) {
		//Abort if inside the editor frame.
		function isInsideEditorFrame() {
			try {
				return (window.self !== window.top) && (typeof window.top.vacApp !== 'undefined');
			} catch (ex) {
				return false;
			}
		}
		if (isInsideEditorFrame()) {
			return;
		}

		//This should never happen, but lets not crash if it does.
		if (typeof data === 'undefined') {
			return;
		}

		if (typeof data.replacementText === 'object') {
			$.each(data.replacementText, function(selector, newText) {
				$(selector).first().text(newText);
			});
		}

	}

)(jQuery, wsVacApplicatorData);
