var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

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
	 * Opens a modal with available tokens.
	 */
	e107.callbacks.metatagOpenTokenPopup = function ()
	{

	};

})(jQuery);
