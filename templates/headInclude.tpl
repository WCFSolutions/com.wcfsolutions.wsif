<meta http-equiv="content-type" content="text/html; charset={@CHARSET}" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta name="description" content="{META_DESCRIPTION}" />
<meta name="keywords" content="{META_KEYWORDS}" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
{if !$allowSpidersToIndexThisPage|isset}<meta name="robots" content="noindex,nofollow" />{/if}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/3rdParty/protoaculous.1.8.2.min.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/default.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/PopupMenuList.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/AjaxRequest.class.js"></script>

<!-- wsif styles -->
<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WSIF_DIR}style/wsif{if PAGE_DIRECTION == 'rtl'}-rtl{/if}.css" />
<style type="text/css">
	.entryThumbnail img, .entryImageList li.floatedElement, .entryImage img {
		border-color: {@$this->getStyle()->getVariable('divider.color')};
	}
	.entryThumbnail img:hover, .entryImageList li.floatedElement:hover, .entryImage img:hover {
		border-color: {@$this->getStyle()->getVariable('container.border.outer.color')};
	}
</style>

{if $specialStyles|isset}
	<!-- special styles -->
	{@$specialStyles}
{/if}

<!-- dynamic styles -->
<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/style-{@$this->getStyle()->styleID}{if PAGE_DIRECTION == 'rtl'}-rtl{/if}.css" />

<!-- print styles -->
<link rel="stylesheet" type="text/css" media="print" href="{@RELATIVE_WCF_DIR}style/extra/print{if PAGE_DIRECTION == 'rtl'}-rtl{/if}.css" />

<!-- opera styles -->
<script type="text/javascript">
	//<![CDATA[
	if (Prototype.Browser.Opera) {
		document.write('<style type="text/css">	* { unicode-bidi: normal; } .columnContainer { border: 0; } </style>');
	}
	//]]>
</script>

<script type="text/javascript">
	//<![CDATA[
	var SID_ARG_2ND	= '{@SID_ARG_2ND_NOT_ENCODED}';
	var SECURITY_TOKEN = '{@SECURITY_TOKEN}';
	var RELATIVE_WCF_DIR = '{@RELATIVE_WCF_DIR}';
	var RELATIVE_WSIF_DIR = '{@RELATIVE_WSIF_DIR}';
	var LANG_DELETE_CONFIRM = '{lang}wcf.global.button.delete.confirm{/lang}';
	//]]>
</script>

<!-- hack styles -->
<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/extra/ie6-fix{if PAGE_DIRECTION == 'rtl'}-rtl{/if}.css" />
	<style type="text/css">
		{if !$this->getStyle()->getVariable('page.width')}
			{if $this->getStyle()->getVariable('page.frame.general')}
			#headerContainer, #footerContainer, #mainContainer { /* note: non-standard style-declaration */
			{else}
			#header, #footer, #main, #mainMenu, #userPanel { /* note: non-standard style-declaration */
			{/if}
				_width: expression(((document.body.clientWidth/screen.width)) < 0.7 ? "{$this->getStyle()->getVariable('page.width.min')}":"{$this->getStyle()->getVariable('page.width.max')}" );
			}
		{/if}
		{if $this->getStyle()->getVariable('user.MSIEFixes.IE6.use')}{@$this->getStyle()->getVariable('user.MSIEFixes.IE6.use')}{/if}
	</style>
<![endif]-->

<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/extra/ie7-fix{if PAGE_DIRECTION == 'rtl'}-rtl{/if}.css" />
	<script type="text/javascript">
		//<![CDATA[
		document.observe('dom:loaded', function() {
			if (location.hash) {
				var columnContainer = null;
				var columnContainerHeight = 0;
				$$('.columnContainer > .column').each(function(column) {
					if (columnContainer != column.up()) {
						columnContainer = column.up();
						columnContainerHeight = columnContainer.getHeight();
					}
					columnContainer.addClassName('columnContainerJS');
					column.setStyle({ 'height': columnContainerHeight + 'px' });
					columnContainer.up().setStyle({ 'height': columnContainerHeight + 1 + 'px' });
					column.removeClassName('column').addClassName('columnJS');
				});
			}
			$$('.layout-3 .second').each(function(column) {
				column.insert('<div style="float: right; font-size: 0">&nbsp;</div>');
			});
		});
		//]]>
	</script>
	{if $this->getStyle()->getVariable('user.MSIEFixes.IE7.use')}
	<style type="text/css">
		{@$this->getStyle()->getVariable('user.MSIEFixes.IE7.use')}
	</style>
	{/if}
<![endif]-->

<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WCF_DIR}style/extra/ie8-fix{if PAGE_DIRECTION == 'rtl'}-rtl{/if}.css" />
<![endif]-->

{if $this->getStyle()->getVariable('global.favicon')}<link rel="shortcut icon" href="{@RELATIVE_WCF_DIR}icon/favicon/favicon{$this->getStyle()->getVariable('global.favicon')|ucfirst}.ico" type="image/x-icon" />{/if}

{if $executeCronjobs}
	<script type="text/javascript">
		//<![CDATA[
		var ajaxRequest = new AjaxRequest();
		ajaxRequest.openGet('index.php?action=CronjobsExec'+SID_ARG_2ND);
		//]]>
	</script>
{/if}