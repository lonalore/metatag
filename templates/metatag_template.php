<?php

/**
 * @file
 * Templates for "metatag" plugin.
 */

$METATAG_TEMPLATE['PANEL']['OPEN'] = '
<div class="panel panel-default metatag-widget-panel">
';

$METATAG_TEMPLATE['PANEL']['HEADER'] = '
	<div class="panel-heading">
		<h4 class="panel-title">
			{PANEL_HEADING}
		</h4>
	</div>
';

$METATAG_TEMPLATE['PANEL']['BODY'] = '
	<div id="{PANEL_ID}" class="{PANEL_CLASS}">
		<div class="panel-body form-horizontal">
			{PANEL_BODY}
		</div>
	</div>
';

$METATAG_TEMPLATE['PANEL']['FOOTER'] = '
	{PANEL_FOOTER}
';

$METATAG_TEMPLATE['PANEL']['CLOSE'] = '
</div>
';

$METATAG_TEMPLATE['PANEL']['HELP'] = '
<div class="col-sm-12">
	<div class="form-group">
		{PANEL_HELP}
	</div>
</div>
';

$METATAG_TEMPLATE['PANEL']['FIELD'] = '
<div class="form-group form-group-{PANEL_FIELD_ID}">
	<label for="{PANEL_FIELD_ID}" class="control-label col-sm-3">
		{PANEL_FIELD_LABEL}
	</label>
	<div class="col-sm-9">
		{PANEL_FIELD}
		{PANEL_FIELD_HELP}
	</div>
</div>
';

$METATAG_TEMPLATE['TOKEN'] = '
<div class="form-group">
	<p>{TOKEN_HELP}</p>
	{TOKEN_BUTTON}
</div>
';
