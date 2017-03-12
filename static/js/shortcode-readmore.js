/* 
	JavaScript functionality for the [readmore] shortcode
*/

(function(root) {

	function ReadMore(element) {
		// don't do anything on IE10 and below
		if (!element.dataset || !element.classList) {
			return;
		}
		var more_label = element.dataset.readmoreMore;
		var less_label = element.dataset.readmoreLess;

		if (element.parentNode.dataset && element.parentNode.dataset.readmoreParent !== undefined) {
			var more_wrapper = document.createElement('div');
			more_wrapper.classList.add('shortcode-readmore__wrapper');

			var SAFETY_HATCH = 100, count = 0, sibling;
			while (element.nextElementSibling && count++ < SAFETY_HATCH) {
				sibling = element.nextElementSibling;
				element.parentNode.removeChild(sibling);
				more_wrapper.appendChild(sibling);
			}

			element.parentNode.insertBefore(more_wrapper, element.nextElementSibling);
			more_wrapper.classList.add('shortcode-readmore__wrapper--hidden');

			var toggleBtn = document.createElement('a');
			toggleBtn.setAttribute('href', '#');
			toggleBtn.textContent = more_label;

			element.replaceChild(toggleBtn, element.firstElementChild);

			toggleBtn.addEventListener('click', function(e) {
				more_wrapper.classList.toggle('shortcode-readmore__wrapper--hidden');
				toggleBtn.textContent = more_wrapper.classList.contains('shortcode-readmore__wrapper--hidden') ?
					more_label : less_label;
				e.preventDefault();
			}, false);

		} else {
			console.warn('readmore shortcode: could not find appropriate parent of element', element);
		}
	}

	[].slice.call(document.querySelectorAll('[data-readmore]')).forEach(ReadMore);

})(window);