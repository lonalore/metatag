var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};
	
	e107.settings.metatag = e107.settings.metatag || {
			'token': {
				'modal_title': '',
				'modal_help': '',
				'global_tokens': {
					'title': '',
					'tokens': []
				},
				'entity_tokens': {
					'title': '',
					'tokens': []
				}
			}
		};

	/**
	 * Behavior to initialize a button, which opens a modal with available
	 * tokens.
	 *
	 * @type {{attach: e107.behaviors.metatagTokenButton.attach}}
	 */
	e107.behaviors.metatagTokenButton = {
		attach: function (context, settings)
		{
			$(context).find('#token-button').once('metatag-token-button').each(function ()
			{
				$(this).click(function ()
				{
					e107.callbacks.metatagOpenTokenPopup();
					return false;
				});
			});
		}
	};

	/**
	 * Behavior to initialize action buttons.
	 *
	 * @type {{attach: e107.behaviors.metatagActionButton.attach}}
	 */
	e107.behaviors.metatagActionButton = {
		attach: function (context, settings)
		{
			$(context).find('.action.revert, .action.cache').once('metatag-action-button').each(function ()
			{
				$(this).click(function ()
				{
					var $this = $(this);

					var $modalTitle = $('<h4></h4>');
					var $modalContent = $('<div></div>');
					var $modalFooter = $('<div></div>');

					$modalTitle.html($this.data('confirm-title'));
					$modalContent.html($this.data('confirm-message'));

					var $buttonNo = $('<button></button>');
					$buttonNo.attr('type', 'button');
					$buttonNo.attr('data-dismiss', 'modal');
					$buttonNo.html($this.data('confirm-no'));
					$buttonNo.addClass('btn btn-success');

					var $buttonYes = $('<button></button>');
					$buttonYes.attr('type', 'button');
					$buttonYes.html($this.data('confirm-yes'));
					// Ajax API related attributes.
					$buttonYes.addClass('btn btn-danger e-ajax');
					$buttonYes.attr('data-event', 'click');
					$buttonYes.attr('data-src', $this.data('confirm-url'));
					$buttonYes.attr('data-ajax-type', 'POST');

					$modalFooter.append($buttonYes);
					$modalFooter.append($buttonNo);

					e107.callbacks.metatagShowModal($modalTitle, $modalContent, $modalFooter);
					return false;
				});
			});
		}
	};

	/**
	 * Opens a modal with available tokens.
	 */
	e107.callbacks.metatagOpenTokenPopup = function ()
	{
		var $modalTitle = $('<h4></h4>');
		var $modalContent = $('<div></div>');
		var $help = $('<p></p>');
		var $table;
		var $tableBody;
		var $groupTitle;

		$modalContent.addClass('token-container');

		$modalTitle.html(e107.settings.metatag.token.modal_title);
		$help.html(e107.settings.metatag.token.modal_help);
		$modalContent.append($help);

		if(e107.settings.metatag.token.global_tokens.tokens.length > 0)
		{
			$groupTitle = $('<h5></h5>');
			$table = $('<table></table>');
			$tableBody = $('<tbody></tbody>');

			$table.addClass('table table-striped token-table');

			$groupTitle.html(e107.settings.metatag.token.global_tokens.title);
			$modalContent.append($groupTitle);

			$.each(e107.settings.metatag.token.global_tokens.tokens, function ()
			{
				var info = this;

				var $row = $('<tr></tr>');
				var $cell1 = $('<td></td>');
				var $cell2 = $('<td></td>');

				$cell1.addClass('token-name');
				$cell1.html('{' + info.token + '}');

				$cell2.addClass('token-info');
				$cell2.html(info.help);

				$row.append($cell1);
				$row.append($cell2);
				$tableBody.append($row);
			});
			$table.append($tableBody);
			$modalContent.append($table);
		}

		// Entity specific tokens.
		if(e107.settings.metatag.token.entity_tokens.tokens.length > 0)
		{
			$groupTitle = $('<h5></h5>');
			$table = $('<table></table>');
			$tableBody = $('<tbody></tbody>');

			$table.addClass('table table-striped token-table');

			$groupTitle.html(e107.settings.metatag.token.entity_tokens.title);
			$modalContent.append($groupTitle);

			$.each(e107.settings.metatag.token.entity_tokens.tokens, function ()
			{
				var info = this;

				var $row = $('<tr></tr>');
				var $cell1 = $('<td></td>');
				var $cell2 = $('<td></td>');

				$cell1.addClass('token-name');
				$cell1.html('{' + info.token + '}');

				$cell2.addClass('token-info');
				$cell2.html(info.help);

				$row.append($cell1);
				$row.append($cell2);
				$tableBody.append($row);
			});
			$table.append($tableBody);
			$modalContent.append($table);
		}

		e107.callbacks.metatagShowModal($modalTitle, $modalContent);
	};

	/**
	 * Helper function to open bootstrap modal with title and content.
	 *
	 * @param title
	 *  Modal title.
	 * @param content
	 *  Modal content.
	 *  @param footer
	 *  Modal footer.
	 */
	e107.callbacks.metatagShowModal = function (title, content, footer)
	{
		var $modal = $('#uiModal');
		$modal.find('.modal-title').html(title);
		$modal.find('.modal-body').html(content);

		if(footer)
		{
			$modal.find('.modal-footer').html(footer);
		}
		else
		{
			$modal.find('.modal-footer').html("");
		}

		$modal.on('shown.bs.modal', function ()
		{
			e107.callbacks.metatagResizeModal();
		});

		$modal.modal('show');

		e107.attachBehaviors($modal);
	};

	/**
	 * Helper function to resize Bootstrap Modal.
	 */
	e107.callbacks.metatagResizeModal = function ()
	{
		var $window = $(window);
		var $modal = $('#uiModal');
		var $modalHeader = $modal.find('.modal-header');
		var $modalBody = $modal.find('.modal-body');
		var $modalFooter = $modal.find('.modal-footer');
		var $modalContent = $modal.find('.modal-content');

		var windowHeight = $window.outerHeight(true);
		var modalHeaderHeight = $modalHeader.outerHeight(true);
		var modalFooterHeight = $modalFooter.outerHeight(true);
		var modalMaxHeight = windowHeight * 0.8;
		var modalBodyMaxHeight = modalMaxHeight - modalHeaderHeight - modalFooterHeight;

		$modalContent.css('max-height', modalMaxHeight + 'px');
		$modalBody.css('max-height', modalBodyMaxHeight + 'px');
		$modalBody.css('overflow-y', 'auto');
	};

})(jQuery);
