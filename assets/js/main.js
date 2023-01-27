function viewport() {
	var w = window,
		d = document,
		e = d.documentElement,
		g = d.getElementsByTagName('body')[0],
		x = w.innerWidth || e.clientWidth || g.clientWidth,
		y = w.innerHeight || e.clientHeight || g.clientHeight;
	return { width: x, height: y };
}

function isInViewport(node) {
	var rect = node.getBoundingClientRect();
	return (
		(rect.height > 0 || rect.width > 0) &&
		rect.bottom >= 0 &&
		rect.right >= 0 &&
		rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
		rect.left <= (window.innerWidth || document.documentElement.clientWidth)
	);
}

function getRootVar(prop) {
	return getComputedStyle(document.documentElement).getPropertyValue(prop);
}

function getColorRGB(str) {
	var matches = str ? str.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(,\s*(\d+))?\)$/) : [];
	if (matches.length > 1) {
		return [parseInt(matches[1]), parseInt(matches[2]), parseInt(matches[3])];
	}
	return [255, 255, 255];
}

function getColorContrast(rgb) {
	return Math.round((parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000);
}

function uploadLabelPropHandler() {
	var i18nUploadLabel = '';
	if (typeof campani_utils !== 'undefined' && campani_utils.hasOwnProperty('upload')) {
		i18nUploadLabel = campani_utils.upload;
	}
	return document.documentElement.style.setProperty('--wpcf7-upload-label', `"${i18nUploadLabel}"`);
}

function scrollbarWidthPropHandler() {
	var scrollbarW = parseFloat(window.innerWidth - document.body.clientWidth);
	return document.documentElement.style.setProperty('--scrollbar-width', `${scrollbarW}px`);
}

(function ($) {
	// Handle CSS props to store DOM elements' info.
	uploadLabelPropHandler();
	$(window).on('load resize', scrollbarWidthPropHandler);

	// Handle file input(s) to have custom behaviour.
	$(document).on('change', '.wpcf7-custom-upload input[type="file"]', function (e) {
		var path = e.target.value;
		var pathArr = path.split('\\');
		var fileName = pathArr[pathArr.length - 1];
		$(e.target).parent().next().html(fileName);
	});
})(jQuery);
