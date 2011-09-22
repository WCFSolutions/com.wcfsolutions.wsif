{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIF_DIR}icon/category{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsif.acp.category.{@$action}{/lang}</h2>
		{if $categoryID|isset}<p>{lang}{$category->title}{/lang}</p>{/if}
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{if $action == 'add'}{lang}wsif.acp.category.add.success{/lang}{else}{lang}wsif.acp.category.edit.success{/lang}{/if}</p>	
{/if}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WSIF_DIR}acp/js/CategoryPermissionList.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var language = new Object();
	language['wsif.acp.category.permissions.permissionsFor'] = '{staticlang}wsif.acp.category.permissions.permissionsFor{/staticlang}';
	language['wsif.acp.category.permissions.fullControl'] = '{lang}wsif.acp.category.permissions.fullControl{/lang}';
	{foreach from=$moderatorSettings item=moderatorSetting}
		language['wsif.acp.category.permissions.{@$moderatorSetting}'] = '{lang}wsif.acp.category.permissions.{@$moderatorSetting}{/lang}';
	{/foreach}
	{foreach from=$permissionSettings item=permissionSetting}
		language['wsif.acp.category.permissions.{@$permissionSetting}'] = '{lang}wsif.acp.category.permissions.{@$permissionSetting}{/lang}';
	{/foreach}
	
	var permissions = new Hash();
	{assign var=i value=0}		
	{foreach from=$permissions item=permission}
		var settings = new Hash();
		settings.set('fullControl', -1);
		
		{foreach from=$permission.settings key=setting item=value}
			{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
				settings.set('{@$setting}', {@$value});
			{/if}
		{/foreach}
		
		permissions.set({@$i}, {
			'name': '{@$permission.name|encodeJS}',
			'type': '{@$permission.type}',
			'id': '{@$permission.id}',
			'settings': settings
		});

		{assign var=i value=$i+1}
	{/foreach}
	
	var moderators = new Hash();
	{assign var=i value=0}
	{foreach from=$moderators item=moderator}
		var settings = new Hash();
		settings.set('fullControl', -1);
		
		{foreach from=$moderator.settings key=setting item=value}
			{if $setting != 'name' && $setting != 'type' && $setting != 'id'}
				settings.set('{@$setting}', {@$value});
			{/if}
		{/foreach}
		
		moderators.set({@$i}, {
			'name': '{@$moderator.name|encodeJS}',
			'type': '{@$moderator.type}',
			'id': '{@$moderator.id}',
			'settings': settings
		});
		
		{assign var=i value=$i+1}
	{/foreach}
	
	var permissionSettings = new Array({implode from=$permissionSettings item=permissionSetting}'{@$permissionSetting}'{/implode});
	var moderatorSettings = new Array({implode from=$moderatorSettings item=moderatorSetting}'{@$moderatorSetting}'{/implode});
	
	// category type
	function setCategoryType(newType) {
		switch (newType) {
			case 0:
				showOptions('filter', 'style', 'settings');
				hideOptions('externalURLDiv');
				break;
			case 1:
				showOptions('style');
				hideOptions('externalURLDiv', 'filter', 'settings');
				break;
			case 2:
				showOptions('externalURLDiv');
				hideOptions('filter', 'style', 'settings');
				break;
		}
	}
	
	document.observe("dom:loaded", function() {
		setCategoryType({@$categoryType});
		
		// user/group permissions
		var permissionList = new CategoryPermissionList('permission', permissions, permissionSettings);
		
		// moderators
		var moderatorPermissionList = new CategoryPermissionList('moderator', moderators, moderatorSettings);
		
		// add onsubmit event
		$('categoryAddForm').onsubmit = function() { 
			if (suggestion.selectedIndex != -1) return false;
			if (permissionList.inputHasFocus || moderatorPermissionList.inputHasFocus) return false;
			permissionList.submit(this); moderatorPermissionList.submit(this);
		};

	});
	//]]>
</script>

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=CategoryList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wsif.acp.menu.link.content.category.view{/lang}"><img src="{@RELATIVE_WSIF_DIR}icon/categoryM.png" alt="" /> <span>{lang}wsif.acp.menu.link.content.category.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=Category{@$action|ucfirst}" id="categoryAddForm">
	<div class="border content">
		<div class="container-1">
			{if $categoryID|isset && $categoryQuickJumpOptions|count > 1}
				<fieldset>
					<legend>{lang}wsif.acp.category.edit{/lang}</legend>
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="categoryChange">{lang}wsif.acp.category.edit{/lang}</label>
						</div>
						<div class="formField">
							<select id="categoryChange" onchange="document.location.href=fixURL('index.php?form=CategoryEdit&amp;categoryID='+this.options[this.selectedIndex].value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}')">
								{htmloptions options=$categoryQuickJumpOptions selected=$categoryID disableEncoding=true}
							</select>
						</div>
					</div>
				</fieldset>
			{/if}
				
			<fieldset>
				<legend>{lang}wsif.acp.category.categoryType{/lang}</legend>
				<div class="formElement{if $errorField == 'categoryType'} formError{/if}">
					<ul class="formOptions">
						<li><label><input onclick="if (IS_SAFARI) setCategoryType(0)" onfocus="setCategoryType(0)" type="radio" name="categoryType" value="0" {if $categoryType == 0}checked="checked" {/if}/> {lang}wsif.acp.category.categoryType.0{/lang}</label></li>
						<li><label><input onclick="if (IS_SAFARI) setCategoryType(1)" onfocus="setCategoryType(1)" type="radio" name="categoryType" value="1" {if $categoryType == 1}checked="checked" {/if}/> {lang}wsif.acp.category.categoryType.1{/lang}</label></li>
						<li><label><input onclick="if (IS_SAFARI) setCategoryType(2)" onfocus="setCategoryType(2)" type="radio" name="categoryType" value="2" {if $categoryType == 2}checked="checked" {/if}/> {lang}wsif.acp.category.categoryType.2{/lang}</label></li>
					</ul>
					{if $errorField == 'categoryType'}
						<p class="innerError">
							{if $errorType == 'invalid'}{lang}wsif.acp.category.error.categoryType.invalid{/lang}{/if}
						</p>
					{/if}
				</div>
			</fieldset>

			<fieldset>
				<legend>{lang}wsif.acp.category.general{/lang}</legend>
					
				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wsif.acp.category.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=CategoryEdit&amp;categoryID={@$categoryID}&amp;languageID='+this.value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wsif.acp.category.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}
					
				<div class="formElement{if $errorField == 'title'} formError{/if}">
					<div class="formFieldLabel">
						<label for="title">{lang}wsif.acp.category.title{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="title" name="title" value="{$title}" />
						{if $errorField == 'title'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
			
				<div id="descriptionDiv" class="formElement">
					<div class="formFieldLabel">
						<label for="description">{lang}wsif.acp.category.description{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="description" name="description" cols="40" rows="10">{$description}</textarea>
						<label><input type="checkbox" name="allowDescriptionHtml" value="1" {if $allowDescriptionHtml}checked="checked" {/if}/> {lang}wsif.acp.category.allowDescriptionHtml{/lang}</label>
					</div>
				</div>
					
				<div id="externalURLDiv" class="formElement{if $errorField == 'externalURL'} formError{/if}">
					<div class="formFieldLabel">
						<label for="externalURL">{lang}wsif.acp.category.externalURL{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="externalURL" name="externalURL" value="{$externalURL}" />
						{if $errorField == 'externalURL'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
					
				{if $additionalGeneralFields|isset}{@$additionalGeneralFields}{/if}
			</fieldset>
				
			<fieldset>
				<legend>{lang}wsif.acp.category.classification{/lang}</legend>
				
				{if $categoryOptions|count > 0}
					<div class="formElement{if $errorField == 'parentID'} formError{/if}" id="parentIDDiv">
						<div class="formFieldLabel">
							<label for="parentID">{lang}wsif.acp.category.parentID{/lang}</label>
						</div>
						<div class="formField">
							<select name="parentID" id="parentID">
								<option value="0"></option>
								{htmlOptions options=$categoryOptions disableEncoding=true selected=$parentID}
							</select>
							{if $errorField == 'parentID'}
								<p class="innerError">
									{if $errorType == 'invalid'}{lang}wsif.acp.category.error.parentID.invalid{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="parentIDHelpMessage">
							<p>{lang}wsif.acp.category.parentID.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('parentID');
					//]]></script>
				{/if}
			
				<div class="formElement{if $errorField == 'position'} formError{/if}" id="positionDiv">
					<div class="formFieldLabel">
						<label for="position">{lang}wsif.acp.category.position{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="position" name="position" value="{@$position}" />
						{if $errorField == 'position'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="positionHelpMessage">
						<p>{lang}wsif.acp.category.position.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('position');
				//]]></script>
					
				{if $additionalPositionFields|isset}{@$additionalPositionFields}{/if}
			</fieldset>
				
			<fieldset id="settings">
				<legend>{lang}wsif.acp.category.settings{/lang}</legend>
					
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="enableRating">{lang}wsif.acp.category.rating{/lang}</label>
					</div>
					<div class="formField">
						<select name="enableRating" id="enableRating">
							<option value="-1"></option>
							<option value="1"{if $enableRating == 1} selected="selected"{/if}>{lang}wsif.acp.category.rating.enable{/lang}</option>
							<option value="0"{if $enableRating == 0} selected="selected"{/if}>{lang}wsif.acp.category.rating.disable{/lang}</option>
						</select>
					</div>
				</div>
					
				{if $additionalSettings|isset}{@$additionalSettings}{/if}
			</fieldset>
				
			<fieldset id="filter">
				<legend>{lang}wsif.acp.category.filter{/lang}</legend>
				
				<div class="formElement{if $errorField == 'daysPrune'} formError{/if}">
					<div class="formFieldLabel">
						<label for="daysPrune">{lang}wsif.acp.category.daysPrune{/lang}</label>
					</div>
					<div class="formField">
						<select name="daysPrune" id="daysPrune">
							<option value="0"></option>
							<option value="1"{if $daysPrune == 1} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.1{/lang}</option>
							<option value="3"{if $daysPrune == 3} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.3{/lang}</option>
							<option value="7"{if $daysPrune == 7} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.7{/lang}</option>
							<option value="14"{if $daysPrune == 14} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.14{/lang}</option>
							<option value="30"{if $daysPrune == 30} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.30{/lang}</option>
							<option value="60"{if $daysPrune == 60} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.60{/lang}</option>
							<option value="100"{if $daysPrune == 100} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.100{/lang}</option>
							<option value="365"{if $daysPrune == 365} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.365{/lang}</option>
							<option value="1000"{if $daysPrune == 1000} selected="selected"{/if}>{lang}wsif.category.entries.filterByDate.1000{/lang}</option>
						</select>
						{if $errorField == 'daysPrune'}
							<p class="innerError">
								{if $errorType == 'invalid'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
					
				<div class="formElement{if $errorField == 'sortField'} formError{/if}">
					<div class="formFieldLabel">
						<label for="sortField">{lang}wsif.acp.category.sortField{/lang}</label>
					</div>
					<div class="formField">
						<select name="sortField" id="sortField">
							<option value=""></option>
							<option value="subject"{if $sortField == 'subject'} selected="selected"{/if}>{lang}wsif.entry.subject{/lang}</option>
							<option value="username"{if $sortField == 'username'} selected="selected"{/if}>{lang}wsif.entry.username{/lang}</option>
							<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}wsif.entry.time{/lang}</option>
							<option value="ratingResult"{if $sortField == 'ratingResult'} selected="selected"{/if}>{lang}wsif.entry.rating{/lang}</option>
							<option value="downloads"{if $sortField == 'downloads'} selected="selected"{/if}>{lang}wsif.entry.downloads{/lang}</option>
							<option value="views"{if $sortField == 'views'} selected="selected"{/if}>{lang}wsif.entry.views{/lang}</option>
						</select>
						{if $errorField == 'sortField'}
							<p class="innerError">
								{if $errorType == 'invalid'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
					
				<div class="formElement{if $errorField == 'sortOrder'} formError{/if}">
					<div class="formFieldLabel">
						<label for="sortOrder">{lang}wsif.acp.category.sortOrder{/lang}</label>
					</div>
					<div class="formField">
						<select name="sortOrder" id="sortOrder">
							<option value=""></option>
							<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
							<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
						</select>
						{if $errorField == 'sortOrder'}
							<p class="innerError">
								{if $errorType == 'invalid'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
					
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="entriesPerPage">{lang}wsif.acp.category.entriesPerPage{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="entriesPerPage" name="entriesPerPage" value="{@$entriesPerPage}" />
					</div>
				</div>
					
				{if $additionalFilterFields|isset}{@$additionalFilterFields}{/if}
			</fieldset>
				
			<fieldset id="style">
				<legend>{lang}wsif.acp.category.style{/lang}</legend>

				{if $availableStyles|count > 1}
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="styleID">{lang}wsif.acp.category.styleID{/lang}</label>
						</div>
						<div class="formField">
							<select name="styleID" id="styleID">
								<option value="0"></option>
								{htmlOptions options=$availableStyles selected=$styleID}
							</select>
							<label><input type="checkbox" name="enforceStyle" value="1" {if $enforceStyle}checked="checked" {/if}/> {lang}wsif.acp.category.enforceStyle{/lang}</label>
						</div>
					</div>
				{/if}
				
				<div class="formElement" id="iconDiv">
					<div class="formFieldLabel">
						<label for="icon">{lang}wsif.acp.category.icon{/lang}</label>
					</div>
					<div class="formField">	
						<input type="text" class="inputText" id="icon" name="icon" value="{$icon}" />
					</div>
					<div class="formFieldDesc hidden" id="imageHelpMessage">
						<p>{lang}wsif.acp.category.icon.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					inlineHelp.register('icon');
					//]]>
				</script>
				
				{if $additionalStyleFields|isset}{@$additionalStyleFields}{/if}
			</fieldset>
				
			<fieldset id="permissions">
				<legend>{lang}wsif.acp.category.permissions{/lang}</legend>
					
				<div class="formElement">
					<div class="formFieldLabel" id="permissionTitle">
						{lang}wsif.acp.category.permissions.title{/lang}
					</div>
					<div class="formField"><div id="permission" class="accessRights"></div></div>
				</div>
				<div class="formElement">
					<div class="formField">	
						<input id="permissionAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
						<script type="text/javascript">
							//<![CDATA[
							suggestion.setSource('index.php?page=CategoryPermissionsObjectsSuggest{@SID_ARG_2ND_NOT_ENCODED}');
							suggestion.enableIcon(true);
							suggestion.init('permissionAddInput');
							//]]>
						</script>
						<input id="permissionAddButton" type="button" value="{lang}wsif.acp.category.permissions.add{/lang}" />
					</div>
				</div>
					
				<div class="formElement" style="display: none;">
					<div class="formFieldLabel">
						<div id="permissionSettingsTitle" class="accessRightsTitle"></div>
					</div>
					<div class="formField">
						<div id="permissionHeader" class="accessRightsHeader">
							<span class="deny">{lang}wsif.acp.category.permissions.deny{/lang}</span>
							<span class="allow">{lang}wsif.acp.category.permissions.allow{/lang}</span>
						</div>
						<div id="permissionSettings" class="accessRights"></div>
					</div>
				</div>
				
				{if $additionalPermissionFields|isset}{@$additionalPermissionFields}{/if}
			</fieldset>
				
			<fieldset id="moderators">
				<legend>{lang}wsif.acp.category.moderators{/lang}</legend>
					
				<div class="formElement">
					<div class="formFieldLabel" id="moderatorTitle">
						{lang}wsif.acp.category.permissions.title{/lang}
					</div>
					<div class="formField"><div id="moderator" class="accessRights"></div></div>
				</div>
				<div class="formElement">
					<div class="formField">	
						<input id="moderatorAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
						<script type="text/javascript">
							//<![CDATA[
							suggestion.init('moderatorAddInput');
							//]]>
						</script>
						<input id="moderatorAddButton" type="button" value="{lang}wsif.acp.category.permissions.add{/lang}" />
					</div>
				</div>
					
				<div class="formElement" style="display: none;">
					<div class="formFieldLabel">
						<div id="moderatorSettingsTitle" class="accessRightsTitle"></div>
					</div>
					<div class="formField">
						<div id="moderatorHeader" class="accessRightsHeader">
							<span class="deny">{lang}wsif.acp.category.permissions.deny{/lang}</span>
							<span class="allow">{lang}wsif.acp.category.permissions.allow{/lang}</span>
						</div>
						<div id="moderatorSettings" class="accessRights"></div>
					</div>
				</div>
				
				{if $additionalModeratorFields|isset}{@$additionalModeratorFields}{/if}
			</fieldset>
			
			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>
		
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $categoryID|isset}<input type="hidden" name="categoryID" value="{@$categoryID}" />{/if}
 	</div>
</form>

{include file='footer'}