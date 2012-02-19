{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	{include file='imageViewer'}
	
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{capture append='userMessages'}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
		{if $success|isset}
			<p class="success">{lang}wsif.entry.{@$action}.success{/lang}</p>
		{/if}
	{/capture}
	
	{include file="entryHeader" activeTabMenuItem='entry'}
	
	<form method="post" enctype="multipart/form-data" action="index.php?form=EntryEdit&amp;entryID={@$entryID}">
		<div class="border tabMenuContent">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wsif.entry.edit{/lang}</h3>
				
				{if $preview|isset}
					<div class="message content">
						<div class="messageInner container-1">
							<div class="messageHeader">
								<h4>{lang}wcf.message.preview{/lang}</h4>
							</div>
							<div class="messageBody">
								<div>{@$preview}</div>
							</div>
						</div>
					</div>
				{/if}
				
				{if $entry->isDeletable($category)}
					<fieldset>
						<legend><label for="sure"{if $errorField == 'sure'} class="formError"{/if}><input id="sure" type="checkbox" name="sure" value="1" tabindex="{counter name='tabindex'}" onclick="openList('deletePost')" /> {lang}wsif.entry.delete{/lang}</label></legend>
						<div id="deletePost">
							<div class="formElement {if $errorField == 'sure'} formError{/if}">
								{if $errorField == 'sure'}
									<p class="innerError">{lang}wsif.entry.delete.error{/lang}</p>
								{/if}
							</div>				
							{if !$entry->isDeleted && ENTRY_ENABLE_RECYCLE_BIN}
								<div class="formElement">
									<div class="formFieldLabel">
										<label for="deleteReason">{lang}wsif.entry.delete.reason{/lang}</label>
									</div>
									<div class="formField">
										<textarea name="deleteReason" id="deleteReason" rows="5" cols="40">{$deleteReason}</textarea>
									</div>
								</div>
							{/if}
							<div class="formElement">
								<div class="formField">
									<input type="submit" name="send" value="{lang}wcf.global.button.submit{/lang}" class="hidden" />
									<input type="submit" name="deleteEntry" value="{lang}wcf.global.button.delete{/lang}" tabindex="{counter name='tabindex'}" />
								</div>
							</div>
							<script type="text/javascript">
								//<![CDATA[
								document.observe('dom:loaded', function() { $('deletePost').setStyle({ 'display' : 'none' }); });
								//]]>
							</script>
						</div>
					</fieldset>
				{/if}
				
				{if $entry->isEditable($category)}
					<fieldset>
						<legend>{lang}wsif.entry.information{/lang}</legend>
						
						{if $availableLanguages|count > 1}
							<div class="formElement">
								<div class="formFieldLabel">
									<label for="languageID">{lang}wsif.entry.language{/lang}</label>
								</div>
								<div class="formField">
									<select name="languageID" id="languageID" tabindex="{counter name='tabindex'}">
										{foreach from=$availableLanguages item=availableLanguage}
										<option value="{@$availableLanguage.languageID}"
											{if $availableLanguage.languageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$availableLanguage.languageCode}{/lang}</option>
										{/foreach}
									</select>
								</div>
							</div>
						{/if}
						
						{if $category->getPrefixes() && $category->getPermission('canSetEntryPrefix')}
							<div class="formElement{if $errorField == 'prefixID'} formError{/if}">
								<div class="formFieldLabel">
									<label for="prefix">{lang}wsif.entry.prefix{/lang}</label>
								</div>
								<div class="formField">
									<select name="prefixID" id="prefixID" tabindex="{counter name='tabindex'}">
										<option value="0"></option>
										{foreach from=$category->getPrefixes() item=prefix}
											<option value="{@$prefix->prefixID}"{if $prefixID == $prefix->prefixID} selected="selected"{/if}>{@$prefix->getPrefixName()}</option>
										{/foreach}
									</select>
									{if $errorField == 'prefixID'}
										<p class="innerError">
											{if $errorType == 'invalid'}{lang}wsif.entry.error.prefixID.invalid{/lang}{/if}
										</p>
									{/if}
								</div>
							</div>
						{/if}
						
						<div class="formElement{if $errorField == 'subject'} formError{/if}">
							<div class="formFieldLabel">
								<label for="subject">{lang}wsif.entry.subject{/lang}</label>
							</div>
							<div class="formField">
								<input type="text" class="inputText" name="subject" id="subject" value="{$subject}" tabindex="{counter name='tabindex'}" />
								{if $errorField == 'subject'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
						
						<div class="formElement{if $errorField == 'teaser'} formError{/if}">
							<div class="formFieldLabel">
								<label for="teaser">{lang}wsif.entry.teaser{/lang}</label>
							</div>
							<div class="formField">
								<textarea id="teaser" name="teaser" rows="5" cols="40">{@$teaser}</textarea>
								{if $errorField == 'teaser'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'tooLong'}{lang}wsif.entry.teaser.error.tooLong{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
						
						{if MODULE_TAGGING && ENTRY_ENABLE_TAGS && $category->getPermission('canSetEntryTags')}{include file='tagAddBit'}{/if}
						
						{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
					</fieldset>
				
					<fieldset>
						<legend>{lang}wsif.entry.text{/lang}</legend>
						
						<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">	
							<div class="formFieldLabel">
								<label for="text">{lang}wsif.entry.text{/lang}</label>
							</div>
							
							<div class="formField">				
								<textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
								{if $errorField == 'text'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'tooLong'}{lang}wcf.message.error.tooLong{/lang}{/if}
										{if $errorType == 'censoredWordsFound'}{lang}wcf.message.error.censoredWordsFound{/lang}{/if}
									</p>
								{/if}
							</div>					
						</div>
						
						{include file='messageFormTabs'}	
					</fieldset>
				{/if}
				
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>