{if !$this->user->userID && !LOGIN_USE_CAPTCHA}
	{counter name='tabindex' start=4 print=false}
{else}
	{counter name='tabindex' start=0 print=false}
{/if}
<div id="headerContainer">
	<a id="top"></a>
	<div id="userPanel" class="userPanel">
		<div class="userPanelInner">
			<p style="display: none;" id="userAvatar">
				{if $this->user->userID && $this->user->getAvatar()}{@$this->user->getAvatar()}{else}<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" />{/if}
			</p>
			<p id="userNote">
				{if $this->user->userID != 0}{lang}wsif.header.userNote.user{/lang}{else}{lang}wsif.header.userNote.guest{/lang}{/if}
			</p>
			<div id="userMenu">
				<ul>
					{if $this->user->userID != 0}
						<li id="userMenuLogout"><a href="index.php?action=UserLogout&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}logoutS.png{/icon}" alt="" /> <span>{lang}wsif.header.userMenu.logout{/lang}</span></a></li>
						<li id="userMenuProfileEdit"><a href="index.php?form=UserProfileEdit{@SID_ARG_2ND}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsif.header.userMenu.profile{/lang}</span></a></li>

						{if $additionalUserMenuItems|isset}{@$additionalUserMenuItems}{/if}

						{if $this->user->isModerator()}
							<li id="userMenuModeration"{if $this->user->getOutstandingModerations()} class="new"{/if}><a href="index.php?page=ModerationCPOverview{@SID_ARG_2ND}"><img src="{icon}moderationcpS.png{/icon}" alt="" /> <span>{lang}wsif.header.userMenu.moderation{/lang}{if $this->user->getOutstandingModerations()} ({#$this->user->getOutstandingModerations()}){/if}</span></a></li>
						{/if}
						{if $this->user->getPermission('admin.general.canUseAcp')}
							<li id="userMenuACP"><a href="acp/index.php?packageID={@PACKAGE_ID}"><img src="{icon}acpS.png{/icon}" alt="" /> <span>{lang}wsif.header.userMenu.acp{/lang}</span></a></li>
						{/if}
					{else}
						<li id="userMenuLogin"><a href="index.php?form=UserLogin{@SID_ARG_2ND}" id="loginButton"><img src="{icon}loginS.png{/icon}" alt="" id="loginButtonImage" /> <span>{lang}wsif.header.userMenu.login{/lang}</span></a></li>

						{if !REGISTER_DISABLED}<li id="userMenuRegistration"><a href="index.php?page=Register{@SID_ARG_2ND}"><img src="{icon}registerS.png{/icon}" alt="" /> <span>{lang}wsif.header.userMenu.register{/lang}</span></a></li>{/if}

						{if $additionalUserMenuItems|isset}{@$additionalUserMenuItems}{/if}

						{if $this->language->countAvailableLanguages() > 1}
							<li id="userMenuLanguage" class="languagePicker"><a id="changeLanguage" class="hidden"><img src="{icon}language{@$this->language->getLanguageCode()|ucfirst}S.png{/icon}" alt="" /> <span>{lang}wsif.header.userMenu.changeLanguage{/lang}</span></a>
								<div class="hidden" id="changeLanguageMenu">
									<ul>
										{foreach from=$this->language->getAvailableLanguageCodes() item=guestLanguageCode key=guestLanguageID}
											<li{if $guestLanguageID == $this->language->getLanguageID()} class="active"{/if}><a rel="nofollow" href="{if $this->session->requestURI && $this->session->requestMethod == 'GET'}{$this->session->requestURI}{if $this->session->requestURI|strpos:'?'}&amp;{else}?{/if}{else}index.php?{/if}l={$guestLanguageID}{@SID_ARG_2ND}"><img src="{icon}language{@$guestLanguageCode|ucfirst}S.png{/icon}" alt="" /> <span>{lang}wcf.global.language.{@$guestLanguageCode}{/lang}</span></a></li>
										{/foreach}
									</ul>
								</div>
								<script type="text/javascript">
									//<![CDATA[
									onloadEvents.push(function() { document.getElementById('changeLanguage').className=''; });
									popupMenuList.register('changeLanguage');
									//]]>
								</script>
								<noscript>
									<form method="get" action="index.php">
										<div>
											<label><img src="{icon}language{@$this->language->getLanguageCode()|ucfirst}S.png{/icon}" alt="" />
												<select name="l" onchange="this.form.submit()">
													{htmloptions options=$this->language->getLanguages() selected=$this->language->getLanguageID() disableEncoding=true}
												</select>
											</label>
											{@SID_INPUT_TAG}
											<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
										</div>
									</form>
								</noscript>
							</li>
						{/if}
					{/if}
				</ul>
			</div>
		</div>
	</div>

	{if !$this->user->userID && !LOGIN_USE_CAPTCHA}
		<script type="text/javascript">
			//<![CDATA[
			document.observe("dom:loaded", function() {
				var loginFormVisible = false;

				var loginBox = $('quickLoginBox');
				var loginButton = $('loginButton');

				if (loginButton && loginBox) {
					function showLoginForm(evt) {
						if (loginBox.hasClassName('hidden')) {
							loginBox.setStyle('display: none');
							loginBox.removeClassName('hidden');
						}
						loginBox.setStyle('left: ' + loginButton.cumulativeOffset()[0] + 'px; top: ' + (loginButton.cumulativeOffset()[1] + loginButton.getHeight() + 5) + 'px;');
						if (loginBox.visible()) {
							new Effect.Parallel([
								new Effect.BlindUp(loginBox),
								new Effect.Fade(loginBox)
							], { duration: 0.3 });
							loginFormVisible = false;
						}
						else {
							new Effect.Parallel([
								new Effect.BlindDown(loginBox),
								new Effect.Appear(loginBox)
							], { duration: 0.3 });
							loginFormVisible = true;
						}
						evt.stop();
					}

					loginButton.observe('click', showLoginForm);
					loginButton.observe('dblclick', function() { document.location.href = fixURL('index.php?form=UserLogin{@SID_ARG_2ND_NOT_ENCODED}'); });

					document.getElementById('quickLoginUsername').onfocus = function() { if (this.value == '{lang}wcf.user.username{/lang}') this.value=''; };
					document.getElementById('quickLoginUsername').onblur = function() { if (this.value == '') this.value = '{lang}wcf.user.username{/lang}'; };
					$('loginButtonImage').src = $('loginButtonImage').src.gsub('loginS.png', 'loginOptionsS.png');
				}
			});
			//]]>
		</script>
	{/if}

	<div id="header">

		{* --- quick search controls ---
		 * $searchScript=search script; default=index.php?form=Search
		 * $searchFieldName=name of the search input field; default=q
		 * $searchFieldValue=default value of the search input field; default=content of $query
		 * $searchFieldTitle=title of search input field; default=language variable wsif.header.search.query
		 * $searchFieldOptions=special search options for popup menu; default=empty
		 * $searchExtendedLink=link to extended search form; default=index.php?form=Search{@SID_ARG_2ND}
		 * $searchHiddenFields=optional hidden fields; default=empty
		 * $searchShowExtendedLink=set to false to disable extended search link; default=true
		 *}

		{if !$searchScript|isset}{assign var='searchScript' value='index.php?form=Search'}{/if}
		{if !$searchFieldName|isset}{assign var='searchFieldName' value='q'}{/if}
		{if !$searchFieldValue|isset && $query|isset}{assign var='searchFieldValue' value=$query}{/if}
		{if !$searchFieldTitle|isset}{assign var='searchFieldTitle' value='{lang}wsif.header.search.query{/lang}'}{/if}
		{if !$searchFieldOptions|isset}
			{capture assign=searchFieldOptions}
			{/capture}
		{/if}
		{if !$searchExtendedLink|isset}{assign var='searchExtendedLink' value='index.php?form=Search'|concat:SID_ARG_2ND}{/if}
		{if !$searchShowExtendedLink|isset}{assign var='searchShowExtendedLink' value=true}{/if}

		<div id="search">
			<form method="post" action="{@$searchScript}">

				<div class="searchContainer">
					<input type="text" tabindex="{counter name='tabindex'}" id="searchInput" class="inputText" name="{@$searchFieldName}" value="{if !$searchFieldValue|empty}{$searchFieldValue}{else}{@$searchFieldTitle}{/if}" />
					<input type="image" tabindex="{counter name='tabindex'}" id="searchSubmit" class="searchSubmit inputImage" src="{icon}searchSubmitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
					{@SID_INPUT_TAG}
					{if $searchHiddenFields|isset}{@$searchHiddenFields}{else}<input type="hidden" name="types[]" value="entry" />{/if}

					<script type="text/javascript">
						//<![CDATA[
						document.getElementById('searchInput').setAttribute('autocomplete', 'off');
						document.getElementById('searchInput').onfocus = function() { if (this.value == '{@$searchFieldTitle}') this.value=''; };
						document.getElementById('searchInput').onblur = function() { if (this.value == '') this.value = '{@$searchFieldTitle}'; };
						document.getElementById('searchSubmit').ondblclick = function() { window.location = 'index.php?form=Search{@SID_ARG_2ND_NOT_ENCODED}'; };
						{if $searchFieldOptions || $searchShowExtendedLink}
							popupMenuList.register("searchInput");
							document.getElementById('searchInput').className += " searchOptions";
						{/if}
						//]]>
					</script>
					{if $searchFieldOptions || $searchShowExtendedLink}
						<div class="searchInputMenu">
							<div class="hidden" id="searchInputMenu">
								<div class="pageMenu smallFont">
									<ul>
										{@$searchFieldOptions}
										{if $searchShowExtendedLink}<li><a href="{@$searchExtendedLink}{if !$searchFieldValue|empty}&amp;defaultQuery={$searchFieldValue|rawurlencode}{/if}">{lang}wsif.header.search.extended{/lang}</a></li>{/if}
									</ul>
								</div>
							</div>
						</div>
					{/if}

					{if $searchShowExtendedLink}
						<noscript>
							<p><a href="{@$searchExtendedLink}">{lang}wsif.header.search.extended{/lang}</a></p>
						</noscript>
					{/if}
				</div>
			</form>
		</div>
		<div id="logo">
			<div class="logoInner">
				<h1 class="pageTitle"><a href="index.php?page=Index{@SID_ARG_2ND}">{lang}{PAGE_TITLE}{/lang}</a></h1>
				{if $this->getStyle()->getVariable('page.logo.image')}
					<a href="index.php?page=Index{@SID_ARG_2ND}" class="pageLogo">
						<img src="{$this->getStyle()->getVariable('page.logo.image')}" title="{lang}{PAGE_TITLE}{/lang}" alt="" />
					</a>
				{elseif $this->getStyle()->getVariable('page.logo.image.application.use') == 1}
					<a href="index.php?page=Index{@SID_ARG_2ND}" class="pageLogo">
						<img src="{@RELATIVE_WSIF_DIR}images/wsif-header-logo.png" title="{lang}{PAGE_TITLE}{/lang}" alt="" />
					</a>
				{/if}
			</div>
		</div>
	</div>

	{include file=headerMenu}

{* user messages system*}
{capture append=userMessages}
	{if $this->user->userID}

		{if $this->user->activationCode && REGISTER_ACTIVATION_METHOD == 1}<p class="warning">{lang}wcf.user.register.needsActivation{/lang}</p>{/if}

		{if $this->session->isNew}<p class="info">{lang}wsif.header.welcomeBack{/lang}</p>{/if}

		{if $this->user->numberOfInvitations && $this->user->getInvitations()|count}
			<div class="info deletable" id="invitationContainer">
				<a href="index.php?action=WhiteListNotificationDisable&amp;ajax=1&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="close deleteButton"><img src="{icon}closeS.png{/icon}" alt="" title="{lang}wcf.user.whitelist.notification.cancel{/lang}" longdesc="" /></a>
				<p>{lang}wcf.user.whitelist.notification{/lang}</p>
				<ul class="itemList">
					{foreach from=$this->user->getInvitations() item=member}
						<li class="deletable">
							<div class="buttons">
								<a href="index.php?form=WhiteListEdit&amp;accept={@$member->userID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.user.whitelist.accept{/lang}"><img src="{icon}checkS.png{/icon}" alt="{lang}wcf.user.whitelist.accept{/lang}" longdesc="{lang}wcf.user.whitelist.accept.sure{/lang}" /></a>
								<a href="index.php?form=WhiteListEdit&amp;decline={@$member->userID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" class="deleteButton" title="{lang}wcf.user.whitelist.decline{/lang}"><img src="{icon}deleteS.png{/icon}" alt="{lang}wcf.user.whitelist.decline{/lang}" longdesc="{lang}wcf.user.whitelist.decline.sure{/lang}" /></a>
							</div>
							<p class="itemListTitle"><a href="index.php?page=User&amp;userID={@$member->userID}{@SID_ARG_2ND}">{$member->username}</a></p>
						</li>
					{/foreach}
				</ul>
			</div>
			<script type="text/javascript">
				//<![CDATA[
				document.observe('wcf:inlineDelete', function() {
					if ($('invitationContainer') && !$('invitationContainer').down('li')) {
						inlineDelete($('invitationContainer').down('.close'));
					}
				});
				//]]>
			</script>
		{/if}

	{elseif !$this->session->spiderID}

		{if $this->session->isNew}<p class="info">{lang}wcf.user.register.welcome{/lang}</p>{/if}

	{/if}
	{if OFFLINE == 1 && $this->user->getPermission('user.filebase.canViewFilebaseOffline')}
		<div class="warning">
			{lang}wsif.global.offline{/lang}
			<p>{if OFFLINE_MESSAGE_ALLOW_HTML}{@OFFLINE_MESSAGE}{else}{@OFFLINE_MESSAGE|htmlspecialchars|nl2br}{/if}</p>
		</div>
	{/if}
{/capture}
</div>
<div id="mainContainer">
{if $additionalHeaderContents|isset}{@$additionalHeaderContents}{/if}