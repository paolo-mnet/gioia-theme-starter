(function ($) {
	/**
	 * Block: Accordion
	 */
	function initializeAccordion($block) {
		$block = !$block.is('div.accordion') ? $block.find('.accordion') : $block;
		$block.on('click', '.accordion__panel [aria-controls]', function (e) {
			e.preventDefault();
			($control = $(this)),
				(panelId = $control.attr('aria-controls')),
				(hasMultiple = $block.data('multiple') || false);

			// check for already open panel
			if (
				!hasMultiple &&
				$('.accordion__panel--in').length &&
				!$('.accordion__panel--in').is($control.parents('.accordion__panel'))
			) {
				($panelToClose = $('.accordion__panel--in')),
					($controlToClose = $panelToClose.find('[aria-controls]')),
					(panelToCloseId = $controlToClose.attr('aria-controls'));
				$controlToClose.attr('aria-expanded', 'false');
				$controlToClose.find('.icon').replaceWith(gioia_icons.plus);
				$('#' + panelToCloseId).attr('aria-hidden', 'true');
				$('#' + panelToCloseId)
					.stop(true)
					.slideUp(300, 'swing', function () {
						$panelToClose.removeClass('accordion__panel--in');
					});
			}

			// update expanded status
			isExpanded = $control.attr('aria-expanded') == 'true';
			$control.attr('aria-expanded', !isExpanded ? 'true' : 'false');
			$control.find('.icon').replaceWith(!isExpanded ? gioia_icons.minus : gioia_icons.plus);

			// update shown/hidden status
			isHidden = $('#' + panelId).attr('aria-hidden') == 'true';
			if (isHidden) {
				$('#' + panelId).attr('aria-hidden', 'false');
				$('#' + panelId)
					.stop(true)
					.slideDown(300, 'swing', function () {
						$control.parents('.accordion__panel').addClass('accordion__panel--in');
					});
			} else {
				$('#' + panelId).attr('aria-hidden', 'true');
				$('#' + panelId)
					.stop(true)
					.slideUp(300, 'swing', function () {
						$control.parents('.accordion__panel').removeClass('accordion__panel--in');
					});
			}
		});
	}

	// Init each block on front-end ready.
	$(document).ready(function () {
		$('.accordion').each(function () {
			initializeAccordion($(this));
		});
	});

	// Init each block on back-end ready (preview mode).
	if (window.acf) {
		window.acf.addAction('render_block_preview/type=accordion', initializeAccordion);
		window.acf.addAction('remount', () => {
			$accordion = $(document).find('div.accordion');
			$accordion.each(function () {
				$(this).off('click', '.accordion__panel [aria-controls]');
				initializeAccordion($(this));
			});
		});
	}
})(jQuery);
