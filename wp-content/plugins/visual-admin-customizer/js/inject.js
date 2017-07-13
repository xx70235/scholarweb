window.vacInjected = {};

(function() {
	var CssSelectorGenerator, root,
		indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

	CssSelectorGenerator = (function() {
		CssSelectorGenerator.prototype.default_options = {
			selectors: ['id', 'class', 'tag', 'nthchild']
		};

		function CssSelectorGenerator(options) {
			if (options == null) {
				options = {};
			}
			this.options = {};
			this.setOptions(this.default_options);
			this.setOptions(options);
		}

		CssSelectorGenerator.prototype.setOptions = function(options) {
			var key, results, val;
			if (options == null) {
				options = {};
			}
			results = [];
			for (key in options) {
				val = options[key];
				if (this.default_options.hasOwnProperty(key)) {
					results.push(this.options[key] = val);
				} else {
					results.push(void 0);
				}
			}
			return results;
		};

		CssSelectorGenerator.prototype.isElement = function(element) {
			return !!((element != null ? element.nodeType : void 0) === 1);
		};

		CssSelectorGenerator.prototype.getParents = function(element) {
			var current_element, result;
			result = [];
			if (this.isElement(element)) {
				current_element = element;
				while (this.isElement(current_element)) {
					result.push(current_element);
					current_element = current_element.parentNode;
				}
			}
			return result;
		};

		CssSelectorGenerator.prototype.getTagSelector = function(element) {
			return this.sanitizeItem(element.tagName.toLowerCase());
		};

		CssSelectorGenerator.prototype.sanitizeItem = function(item) {
			var characters;
			characters = (item.split('')).map(function(character) {
				if (character === ':') {
					return "\\" + (':'.charCodeAt(0).toString(16).toUpperCase()) + " ";
				} else if (/[ !"#$%&'()*+,.\/;<=>?@\[\\\]^`{|}~]/.test(character)) {
					return "\\" + character;
				} else {
					return escape(character).replace(/\%/g, '\\');
				}
			});
			return characters.join('');
		};

		CssSelectorGenerator.prototype.getIdSelector = function(element) {
			var id, sanitized_id;
			id = element.getAttribute('id');
			if ((id != null) && (id !== '') && !(/\s/.exec(id)) && !(/^\d/.exec(id))) {
				sanitized_id = "#" + (this.sanitizeItem(id));
				if (element.ownerDocument.querySelectorAll(sanitized_id).length === 1) {
					return sanitized_id;
				}
			}
			return null;
		};

		CssSelectorGenerator.prototype.getClassSelectors = function(element) {
			var class_string, item, result;
			result = [];
			class_string = element.getAttribute('class');
			if (class_string != null) {
				class_string = class_string.replace(/\s+/g, ' ');
				class_string = class_string.replace(/^\s|\s$/g, '');
				if (class_string !== '') {
					result = (function() {
						var k, len, ref, results;
						ref = class_string.split(/\s+/);
						results = [];
						for (k = 0, len = ref.length; k < len; k++) {
							item = ref[k];
							results.push("." + (this.sanitizeItem(item)));
						}
						return results;
					}).call(this);
				}
			}
			return result;
		};

		CssSelectorGenerator.prototype.getAttributeSelectors = function(element) {
			var attribute, blacklist, k, len, ref, ref1, result;
			result = [];
			blacklist = ['id', 'class'];
			ref = element.attributes;
			for (k = 0, len = ref.length; k < len; k++) {
				attribute = ref[k];
				if (ref1 = attribute.nodeName, indexOf.call(blacklist, ref1) < 0) {
					result.push("[" + attribute.nodeName + "=" + attribute.nodeValue + "]");
				}
			}
			return result;
		};

		CssSelectorGenerator.prototype.getNthChildSelector = function(element) {
			var counter, k, len, parent_element, sibling, siblings;
			parent_element = element.parentNode;
			if (parent_element != null) {
				counter = 0;
				siblings = parent_element.childNodes;
				for (k = 0, len = siblings.length; k < len; k++) {
					sibling = siblings[k];
					if (this.isElement(sibling)) {
						counter++;
						if (sibling === element) {
							return ":nth-child(" + counter + ")";
						}
					}
				}
			}
			return null;
		};

		CssSelectorGenerator.prototype.testSelector = function(element, selector) {
			var is_unique, result;
			is_unique = false;
			if ((selector != null) && selector !== '') {
				result = element.ownerDocument.querySelectorAll(selector);
				if (result.length === 1 && result[0] === element) {
					is_unique = true;
				}
			}
			return is_unique;
		};

		CssSelectorGenerator.prototype.getAllSelectors = function(element) {
			var result;
			result = {
				t: null,
				i: null,
				c: null,
				a: null,
				n: null
			};
			if (indexOf.call(this.options.selectors, 'tag') >= 0) {
				result.t = this.getTagSelector(element);
			}
			if (indexOf.call(this.options.selectors, 'id') >= 0) {
				result.i = this.getIdSelector(element);
			}
			if (indexOf.call(this.options.selectors, 'class') >= 0) {
				result.c = this.getClassSelectors(element);
			}
			if (indexOf.call(this.options.selectors, 'attribute') >= 0) {
				result.a = this.getAttributeSelectors(element);
			}
			if (indexOf.call(this.options.selectors, 'nthchild') >= 0) {
				result.n = this.getNthChildSelector(element);
			}
			return result;
		};

		CssSelectorGenerator.prototype.testUniqueness = function(element, selector) {
			var found_elements, parent;
			parent = element.parentNode;
			found_elements = parent.querySelectorAll(selector);
			return found_elements.length === 1 && found_elements[0] === element;
		};

		CssSelectorGenerator.prototype.testCombinations = function(element, items, tag) {
			var item, k, l, len, len1, ref, ref1;
			ref = this.getCombinations(items);
			for (k = 0, len = ref.length; k < len; k++) {
				item = ref[k];
				if (this.testUniqueness(element, item)) {
					return item;
				}
			}
			if (tag != null) {
				ref1 = items.map(function(item) {
					return tag + item;
				});
				for (l = 0, len1 = ref1.length; l < len1; l++) {
					item = ref1[l];
					if (this.testUniqueness(element, item)) {
						return item;
					}
				}
			}
			return null;
		};

		CssSelectorGenerator.prototype.getUniqueSelector = function(element) {
			var found_selector, k, len, ref, selector_type, selectors;
			selectors = this.getAllSelectors(element);
			ref = this.options.selectors;
			for (k = 0, len = ref.length; k < len; k++) {
				selector_type = ref[k];
				switch (selector_type) {
					case 'id':
						if (selectors.i != null) {
							return selectors.i;
						}
						break;
					case 'tag':
						if (selectors.t != null) {
							if (this.testUniqueness(element, selectors.t)) {
								return selectors.t;
							}
						}
						break;
					case 'class':
						if ((selectors.c != null) && selectors.c.length !== 0) {
							found_selector = this.testCombinations(element, selectors.c, selectors.t);
							if (found_selector) {
								return found_selector;
							}
						}
						break;
					case 'attribute':
						if ((selectors.a != null) && selectors.a.length !== 0) {
							found_selector = this.testCombinations(element, selectors.a, selectors.t);
							if (found_selector) {
								return found_selector;
							}
						}
						break;
					case 'nthchild':
						if (selectors.n != null) {
							return selectors.n;
						}
				}
			}
			return '*';
		};

		CssSelectorGenerator.prototype.getSelector = function(element) {
			var all_selectors, item, k, l, len, len1, parents, result, selector, selectors;
			all_selectors = [];
			parents = this.getParents(element);
			for (k = 0, len = parents.length; k < len; k++) {
				item = parents[k];
				selector = this.getUniqueSelector(item);
				if (selector != null) {
					all_selectors.push(selector);
				}
			}
			selectors = [];
			for (l = 0, len1 = all_selectors.length; l < len1; l++) {
				item = all_selectors[l];
				selectors.unshift(item);
				result = selectors.join(' > ');
				if (this.testSelector(element, result)) {
					return result;
				}
			}
			return null;
		};

		CssSelectorGenerator.prototype.getCombinations = function(items) {
			var i, j, k, l, ref, ref1, result;
			if (items == null) {
				items = [];
			}
			result = [[]];
			for (i = k = 0, ref = items.length - 1; 0 <= ref ? k <= ref : k >= ref; i = 0 <= ref ? ++k : --k) {
				for (j = l = 0, ref1 = result.length - 1; 0 <= ref1 ? l <= ref1 : l >= ref1; j = 0 <= ref1 ? ++l : --l) {
					result.push(result[j].concat(items[i]));
				}
			}
			result.shift();
			result = result.sort(function(a, b) {
				return a.length - b.length;
			});
			result = result.map(function(item) {
				return item.join('');
			});
			return result;
		};

		return CssSelectorGenerator;

	})();

	if (typeof define !== "undefined" && define !== null ? define.amd : void 0) {
		define([], function() {
			return CssSelectorGenerator;
		});
	} else {
		root = typeof exports !== "undefined" && exports !== null ? exports : this;
		root.CssSelectorGenerator = CssSelectorGenerator;
	}

}).call(window);

function VACElement(element, selector) {
	if (!(element instanceof jQuery)) {
		element = jQuery(element);
	}

	this.jq = element;

	this._cachedSelector = selector || null;
	this._parentElementCache = null;
}

VACElement.prototype.getShortPath = function() {
	var $element = this.jq;

	var path = $element.prop('tagName').toLowerCase();
	if (path === 'html') {
		return path;
	}

	if ($element.attr('id')) {
		path = path + '#' + $element.attr('id');
	}

	if ($element.prop('className')) {
		var classList = $element.prop('className').split(/\s+/).filter(function(name) { return (name !== ''); });
		path = path + '.' + classList.join('.');
	}

	return path;
};

VACElement.prototype.getSelector = function() {
	if (this._cachedSelector === null) {
		this._cachedSelector = vacSelectorGenerator.getUniqueSelector(this.jq);
	}
	return this._cachedSelector;
};

/**
 * @returns {VACElement[]}
 */
VACElement.prototype.getParents = function() {
	if (this._parentElementCache !== null) {
		return this._parentElementCache;
	}

	this._parentElementCache = [];
	var $parents = this.jq.parents();
	for (var i = 0; i < $parents.length; i++) {
		this._parentElementCache.push(new VACElement($parents[i]));
	}

	return this._parentElementCache;
};

/**
 * Check if the element contains only a single text node and nothing else.
 *
 * @returns {boolean}
 */
VACElement.prototype.containsOnlyOneTextNode = function() {
	var tagName = this.jq.get(0).tagName;
	if (tagName === 'iframe' || tagName === 'img' || tagName === 'input') {
		return false;
	}

	var contents = this.jq.contents();
	if (contents.length !== 1) {
		return false;
	}

	//Node types: https://developer.mozilla.org/en-US/docs/Web/API/Node/nodeType
	return contents.get(0).nodeType === 3;
};

vacSelectorGenerator = {
	advancedGenerator: new CssSelectorGenerator(),

	/**
	 *
	 * @param {jQuery} element
	 * @return {string|null}
	 */
	getUniqueSelector: function(element) {
		var tag = element.prop('tagName').toLowerCase();
		if (tag === 'html' || tag === 'body' || tag === 'head') {
			return tag;
		}

		return this.advancedGenerator.getSelector(element.get(0));
	}
};

if (typeof jQuery === 'undefined') {
	alert('Error: jQuery is not defined. Save changes and refresh the page.');
} else {
	jQuery(function($) {
		var vacInjected = window.vacInjected;
		var highlightedElement = null,
			/**
			 * @type {VACElement|null}
			 */
			selectedElement = null,
			$body = $('body');

		vacInjected.mode = 'navigate';
		vacInjected.isSelecting = false;

		vacInjected.switchToEditMode = function() {
			this.mode = 'edit';
			this.enableSelection();

			this.selectElement(null);
		};

		vacInjected.switchToNavigationMode = function () {
			this.mode = 'navigate';
			this.disableSelection();

			this.selectElement(null);
		};

		vacInjected.enableSelection = function(enabled) {
			if (typeof enabled === 'undefined') {
				enabled = true;
			}
			this.isSelecting = enabled;
			$body.toggleClass('vac-is-selecting', this.isSelecting);
		};

		vacInjected.disableSelection = function() {
			this.enableSelection(false);
		};

		//Create the highlight borders.
		var $outlineBorderLeft = $('<div>', {'class' : 'vac-outline-border vac-outline-border-left'}).appendTo('body'),
			$outlineBorderRight = $('<div>', {'class' : 'vac-outline-border vac-outline-border-right'}).appendTo('body'),
			$outlineBorderTop = $('<div>', {'class' : 'vac-outline-border vac-outline-border-top'}).appendTo('body'),
			$outlineBorderBottom = $('<div>', {'class' : 'vac-outline-border vac-outline-border-bottom'}).appendTo('body'),
			$outlineBorders = $outlineBorderLeft.add($outlineBorderBottom).add($outlineBorderRight).add($outlineBorderTop);

		//Create the semi-transparent overlays that block out the rest of the page when you select an jq.
		var $fogPaneLeft = $('<div class="vac-fog vac-fog-left">').appendTo($body),
			$fogPaneRight = $('<div class="vac-fog vac-fog-right">').appendTo($body),
			$fogPaneTop = $('<div class="vac-fog vac-fog-top">').appendTo($body),
			$fogPaneBottom = $('<div class="vac-fog vac-fog-bottom">').appendTo($body),
			$fogElemCover = $('<div class="vac-fog vac-fog-transparent">').appendTo($body),
			$fogPanes = $fogPaneLeft.add($fogPaneRight).add($fogPaneTop).add($fogPaneBottom).add($fogElemCover);

		$outlineBorders.hide();
		$fogPanes.hide();

		//Highlight elements on hover.
		$body.on('mouseover', function(event) {
			if (!vacInjected.isSelecting) {
				return;
			}

			var element = document.elementFromPoint(event.clientX, event.clientY);
			if (!element) {
				return;
			}

			//Ignore elements that were added by this plugin.
			if (vacInjected.isEditorElement(element)) {
				return;
			}

			//Optimization: Don't (re-)highlight the same jq.
			if (!highlightedElement || !highlightedElement.jq.is(element)) {
				vacInjected.highlightElement(element);
			}
		});

		vacInjected.highlightElement = function(element) {
			if (element === null) {
				//noinspection JSUnusedAssignment
				highlightedElement = null;
				$outlineBorders.hide();
				$fogPanes.hide();
				return;
			}

			if (!(element instanceof VACElement)) {
				element = new VACElement($(element));
			}
			highlightedElement = element;

			vacInjected.showPathTooltip(highlightedElement);

			//On some pages <body> is relatively positioned and is pushed down (via padding on the <html> element)
			//to make space for the Toolbar. We want to position the highlights and overlays relative to the whole
			//document, so we need to move them up by the same amount.
			var bodyOffsetTop = 0;
			if ($body.css('position') !== 'static') {
				bodyOffsetTop = $body.offset().top || 0;
			}

			//Reposition the selection borders.
			$outlineBorders.show();
			var selectionBorderThickness = $outlineBorderLeft.outerWidth();
			var rect = highlightedElement.jq.get(0).getBoundingClientRect();

			$outlineBorderLeft.css({
				'left': rect.left + window.scrollX - selectionBorderThickness,
				'top' : rect.top + window.scrollY,
				'height' : rect.height
			});

			$outlineBorderRight.css({
				'left': rect.right + window.scrollX,
				'top' : rect.top + window.scrollY,
				'height' : rect.height
			});

			$outlineBorderTop.css({
				'left': rect.left + window.scrollX - selectionBorderThickness,
				'top' : rect.top + window.scrollY - selectionBorderThickness,
				'width' : rect.width + selectionBorderThickness * 2
			});

			$outlineBorderBottom.css({
				'left': rect.left + window.scrollX - selectionBorderThickness,
				'top' : rect.bottom + window.scrollY,
				'width' : rect.width + selectionBorderThickness * 2
			});

			$outlineBorders.css('margin-top', -bodyOffsetTop);

			//Position the fog panes around the selection.
			if ($fogPanes.is(':visible')) {
				$fogPaneTop.css({
					'height': rect.top + window.scrollY
				});

				$fogPaneBottom.css({
					'top': rect.bottom + window.scrollY,
					'height': $(document).height() - rect.bottom
				});

				$fogPaneLeft.css({
					'top': rect.top + window.scrollY,
					'height': rect.height,
					'width': rect.left + window.scrollX
				});

				$fogPaneRight.css({
					'left': rect.right + window.scrollX,
					'top': rect.top + window.scrollY,
					'height': rect.height,
					'width': $fogPaneTop.width() - rect.right
				});

				//This invisible pane covers the selected jq and intercepts clicks.
				$fogElemCover.css({
					'left': rect.left + window.scrollX,
					'top': rect.top + window.scrollY,
					'width': rect.width,
					'height': rect.height
				});

				$fogPanes.css('margin-top', -bodyOffsetTop);
			}
		};

		vacInjected.highlightSelectedElement = function() {
			vacInjected.highlightElement(selectedElement);
		};

		//Select elements on mouse-down. Using "mousedown" instead of "click" is good because it's triggered
		//first, which decreases the chances that something on the page will react before our script.
		function onMouseDown(event) {
			if (!vacInjected.isSelecting || vacInjected.isEditorElement(event.target)) {
				return;
			}

			var $element = $(event.target);

			vacInjected.selectElement($element);
			vacInjected.disableSelection();

			event.preventDefault();
			event.stopImmediatePropagation();
		}
		//Note that the listener uses the capture phase.
		document.body.addEventListener('mousedown', onMouseDown, true);

		//Deselect the current jq when the user clicks on a fog pane.
		$fogPanes.on('click', function() {
			vacInjected.selectElement(null);
		});

		vacInjected.selectElement = function($element) {
			if (selectedElement && selectedElement.jq.is($element)) {
				return false;
			}

			$fogPanes.toggle($element !== null);

			if ($element === null) {
				vacInjected.highlightElement(null);

				//noinspection JSUnusedAssignment
				selectedElement = null;
				vacInjected.enableSelection(vacInjected.mode === 'edit');
				vacInjected.hidePathTooltip();

				parent.vacEditor.onDeselected();
				return false;
			}

			selectedElement = new VACElement($($element));
			vacInjected.highlightElement(selectedElement);
			//console.log('Selected element: ', selectedElement);

			parent.vacEditor.onSelected(selectedElement);

			return true;
		};

		/**
		 * @returns {VACElement|null}
		 */
		vacInjected.getSelectedElement = function() {
			return selectedElement;
		};

		vacInjected.deselect = function() {
			this.selectElement(null);
		};

		vacInjected.getElement = function(selector) {
			var $element = $(selector).first();
			if ($element.length < 1) {
				return null;
			}
			return new VACElement($element, selector);
		};

		var vacPrefix = /(?:^|\s)vac-/;
		vacInjected.isEditorElement = function(element) {
			if (element instanceof jQuery) {
				element = element.get(0);
			}
			return vacPrefix.test(element.className);
		};

		var $pathTooltip = $('<div>', {'class' : 'vac-path-tooltip'}).appendTo($body);
		$pathTooltip
			.append('<div class="vac-tooltip-tip"></div>')
			.append('<div class="vac-tooltip-text">Not initialised</div>');


		vacInjected.showPathTooltip = function($forElement) {
			$pathTooltip.css('display', '');
			$pathTooltip.find('.vac-tooltip-text').text($forElement.getShortPath());

			$pathTooltip.position({
				my: 'left top+10',
				at: 'left bottom',
				of: $forElement.jq,
				collision: 'flipfit'
			});
		};

		vacInjected.hidePathTooltip = function() {
			$pathTooltip.css('display', 'none');
		};

		/**
		 * @returns {ClientRect|null}
		 */
		vacInjected.getSelectionRect = function() {
			if (!selectedElement) {
				if (console.warn) {
					console.warn("Can't get the size of the selected jq because nothing is selected!");
				}
				return null;
			}

			return selectedElement.jq.get(0).getBoundingClientRect();
		};

		//Notify the editor when the frame starts loading a new page.
		window.addEventListener('beforeunload', function() {
			if (parent && parent.vacEditor) {
				parent.vacEditor.onBeforeFrameUnload();
			}
		});

		//Suppress events that are used to hide dropdowns, pop-up menus, tooltips and similar components.
		//This helps prevent an annoying behaviour where selecting a menu item menu makes the menu disappear.
		function suppressOutEvents(event) {
			if ((vacInjected.mode !== 'edit') || (selectedElement === null) || (vacInjected.isEditorElement(event.target))) {
				return;
			}

			event.stopImmediatePropagation();
		}
		document.body.addEventListener('mouseout',   suppressOutEvents, true);
		document.body.addEventListener('mouseleave', suppressOutEvents, true);
		document.body.addEventListener('focusout',   suppressOutEvents, true);

		$(window).scroll(function() {
			if (parent && parent.vacEditor) {
				parent.vacEditor.onChildScroll();
			}
		});

		//Remove customization CSS if it already exists. We want to start with the original, unmodified page.
		$('#vac-css-customizations').remove();
		$('#vac-user-css').remove();

		//Notify the editor that the injected code has finished initialising.
		parent.vacEditor.onInjectedScriptReady();
	});
}




