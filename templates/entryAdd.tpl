{include file="documentHeader"}
<head>
	<title>{lang}wsif.entry.add{/lang} - {@$category->getTitle()} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}

	{include file='imageViewer'}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=Category&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{@$parentCategory->getTitle()}</span></a> &raquo;</li>
		{/foreach}
		<li><a href="index.php?page=Category&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$category->getIconName()}S.png{/icon}" alt="" /> <span>{@$category->getTitle()}</span></a> &raquo;</li>
	</ul>

	<div class="mainHeadline">
		<img src="{icon}entryAddL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wsif.entry.add{/lang}</h2>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}

	{if $preview|isset}
		<div class="border messagePreview">
			<div class="containerHead">
				<h3>{lang}wcf.message.preview{/lang}</h3>
			</div>
			<div class="message content">
				<div class="messageInner container-1">
					{if $subject}
						<h4>{$subject}</h4>
					{/if}
					<div class="messageBody">
						<div>{@$preview}</div>
					</div>
				</div>
			</div>
		</div>
	{/if}

	<form enctype="multipart/form-data" method="post" action="index.php?form=EntryAdd&amp;categoryID={@$category->categoryID}">
		<div class="border content">
			<div class="container-1">
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
										<option value="{@$availableLanguage.languageID}"{if $availableLanguage.languageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$availableLanguage.languageCode}{/lang}</option>
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

					{if !$this->user->userID}
						<div class="formElement{if $errorField == 'username'} formError{/if}">
							<div class="formFieldLabel">
								<label for="username">{lang}wcf.user.username{/lang}</label>
							</div>
							<div class="formField">
								<input type="text" class="inputText" name="username" id="username" value="{$username}" tabindex="{counter name='tabindex'}" />
								{if $errorField == 'username'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
										{if $errorType == 'notAvailable'}{lang}wcf.user.error.username.notUnique{/lang}{/if}
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
							<textarea id="teaser" name="teaser" rows="5" cols="40" tabindex="{counter name='tabindex'}">{@$teaser}</textarea>
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

				{if $category->getModeratorPermission('canEnableEntry')}
					<fieldset>
						<legend>{lang}wsif.entry.publishing{/lang}</legend>

						{if $action == 'add'}
							<div class="formElement">
								<div class="formField">
									<label><input type="checkbox" name="disableEntry" id="disableEntry" value="1" {if $disableEntry == 1}checked="checked" {/if}/> {lang}wsif.entry.disableEntry{/lang}</label>
								</div>
								<div class="formFieldDesc">
									<p>{lang}wsif.entry.disableEntry.description{/lang}</p>
								</div>
							</div>
						{/if}

						<div class="formGroup{if $errorField == 'publishingTime'} formError{/if}" id="publishingTimeDiv">
							<div class="formGroupLabel">
								<label>{lang}wsif.entry.publishingTime{/lang}</label>
							</div>
							<div class="formGroupField">
								<fieldset>
									<legend><label>{lang}wsif.entry.publishingTime{/lang}</label></legend>

									<div class="formField">
										<div class="floatedElement">
											<label for="publishingTimeDay">{lang}wcf.global.date.day{/lang}</label>
											{htmlOptions options=$dayOptions selected=$publishingTimeDay id=publishingTimeDay name=publishingTimeDay}
										</div>

										<div class="floatedElement">
											<label for="publishingTimeMonth">{lang}wcf.global.date.month{/lang}</label>
											{htmlOptions options=$monthOptions selected=$publishingTimeMonth id=publishingTimeMonth name=publishingTimeMonth}
										</div>

										<div class="floatedElement">
											<label for="publishingTimeYear">{lang}wcf.global.date.year{/lang}</label>
											<input id="publishingTimeYear" class="inputText fourDigitInput" type="text" name="publishingTimeYear" value="{@$publishingTimeYear}" maxlength="4" />
										</div>

										<div class="floatedElement">
											<label for="publishingTimeHour">{lang}wcf.global.date.hour{/lang}</label>
											{htmlOptions options=$hourOptions selected=$publishingTimeHour id=publishingTimeHour name=publishingTimeHour}
										</div>

										<div class="floatedElement">
											<a id="publishingTimeButton"><img src="{@RELATIVE_WCF_DIR}icon/datePickerOptionsM.png" alt="" /></a>
											<div id="publishingTimeCalendar" class="inlineCalendar"></div>
											<script type="text/javascript">
												//<![CDATA[
												calendar.init('publishingTime');
												//]]>
											</script>
										</div>

										{if $errorField == 'publishingTime'}
											<p class="floatedElement innerError">
												{if $errorType == 'invalid'}{lang}wsif.entry.publishingTime.error.invalid{/lang}{/if}
											</p>
										{/if}
									</div>
									<div class="formFieldDesc">
										<p>{lang}wsif.entry.publishingTime.description{/lang}</p>
									</div>
								</fieldset>
							</div>
						</div>
					</fieldset>
				{/if}

				<fieldset>
					<legend>{lang}wsif.entry.image.default{/lang}</legend>

					{if $imageID}
						{lang}wsif.entry.image.default.add.success{/lang}
					{else}
						<div class="formFieldDesc">
							<p>{lang}wsif.entry.image.default.add.description{/lang}</p>
						</div>

						<fieldset{if $errorField == 'upload'} class="formError"{/if}>
							<legend>{lang}wsif.entry.image.default.upload{/lang}</legend>
							<input type="file" size="50" name="imageUpload" tabindex="{counter name='tabindex'}" />

							{if $errorField == 'imageUpload'}
								<div class="innerError">
									{if $errorType|is_array}
										{assign var=filename value=$errorType.filename}
										{if $errorType.errorType == 'uploadFailed'}{lang}wsif.entry.image.upload.error.uploadFailed{/lang}{/if}
										{if $errorType.errorType == 'copyFailed'}{lang}wsif.entry.image.upload.error.copyFailed{/lang}{/if}
										{if $errorType.errorType == 'illegalExtension'}{lang}wsif.entry.image.upload.error.illegalExtension{/lang}{/if}
										{if $errorType.errorType == 'tooLarge'}{lang}wsif.entry.image.upload.error.tooLarge{/lang}{/if}
										{if $errorType.errorType == 'badImage'}{lang}wsif.entry.image.upload.error.badImage{/lang}{/if}
										{if $errorType.errorType == 'tooManyImages'}{lang}wsif.entry.image.upload.error.tooManyImages{/lang}{/if}
									{/if}
								</div>
							{/if}

							<div class="formFieldDesc">
								<p>{lang}wsif.entry.image.default.upload.description{/lang}</p>
							</div>
						</fieldset>
					{/if}
				</fieldset>

				<fieldset>
					<legend>{lang}wsif.entry.file.default{/lang}</legend>

					{if $fileID}
						{lang}wsif.entry.file.default.add.success{/lang}
					{else}
						<div class="formFieldDesc">
							<p>{lang}wsif.entry.file.default.add.description{/lang}</p>
						</div>

						<script type="text/javascript">
							//<![CDATA[
							function setFileType(newType) {
								switch (newType) {
									case 0:
										$('uploadDiv').show();
										$('externalURLDiv').hide();
										break;
									case 1:
										$('uploadDiv').hide();
										$('externalURLDiv').show();
										break;
									}
								}
								onloadEvents.push(function() { setFileType({@$fileType}); });
							//]]>
						</script>

						<fieldset>
							<legend>{lang}wsif.entry.file.default.fileType{/lang}</legend>

							<div class="formGroup{if $errorField == 'fileType'} formError{/if}">
								<div class="formGroupLabel">
									{lang}wsif.entry.file.default.fileType{/lang}
								</div>
								<div class="formGroupField">
									<fieldset>
										<legend>{lang}wsif.entry.file.default.fileType{/lang}</legend>
										<div class="formField">
											<ul class="formOptions">
												<li><label><input onclick="if (IS_SAFARI) setFileType(0)" onfocus="setFileType(0)" type="radio" name="fileType" value="0" {if $fileType == 0}checked="checked" {/if}tabindex="{counter name='tabindex'}" /> {lang}wsif.entry.file.fileType.0{/lang}</label></li>
												<li><label><input onclick="if (IS_SAFARI) setFileType(1)" onfocus="setFileType(1)" type="radio" name="fileType" value="1" {if $fileType == 1}checked="checked" {/if}tabindex="{counter name='tabindex'}" /> {lang}wsif.entry.file.fileType.1{/lang}</label></li>
											</ul>
										</div>
									</fieldset>
									{if $errorField == 'fileType'}
										<p class="innerError">
											{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										</p>
									{/if}
								</div>
							</div>
						</fieldset>

						<fieldset{if $errorField == 'fileUpload'} class="formError"{/if} id="uploadDiv">
							<legend>{lang}wsif.entry.file.default.upload{/lang}</legend>
							<input type="file" size="50" name="fileUpload" tabindex="{counter name='tabindex'}" />

							{if $errorField == 'fileUpload'}
								<div class="innerError">
									{if $errorType|is_array}
										{assign var=filename value=$errorType.filename}
										{if $errorType.errorType == 'uploadFailed'}{lang}wsif.entry.file.upload.error.uploadFailed{/lang}{/if}
										{if $errorType.errorType == 'copyFailed'}{lang}wsif.entry.file.upload.error.copyFailed{/lang}{/if}
										{if $errorType.errorType == 'illegalExtension'}{lang}wsif.entry.file.upload.error.illegalExtension{/lang}{/if}
										{if $errorType.errorType == 'tooLarge'}{lang}wsif.entry.file.upload.error.tooLarge{/lang}{/if}
										{if $errorType.errorType == 'tooManyFiles'}{lang}wsif.entry.file.upload.error.tooManyFiles{/lang}{/if}
									{elseif $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</div>
							{/if}

							<div class="formFieldDesc">
								<p>{lang}wsif.entry.file.default.upload.description{/lang}</p>
							</div>
						</fieldset>

						<fieldset{if $errorField == 'externalURL'} class="formError"{/if} id="externalURLDiv">
							<legend>{lang}wsif.entry.file.externalURL{/lang}</legend>
							<div class="formElement{if $errorField == 'externalURL'} formError{/if}">
								<div class="formFieldLabel">
									<label for="externalURL">{lang}wsif.entry.file.externalURL{/lang}</label>
								</div>
								<div class="formField">
									<input type="text" class="inputText" id="externalURL" name="externalURL" value="{$externalURL}" tabindex="{counter name='tabindex'}" />
									{if $errorField == 'externalURL'}
										<p class="innerError">
											{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
											{if $errorType == 'illegalURL'}{lang}wsif.entry.file.externalURL.error.illegalURL{/lang}{/if}
										</p>
									{/if}
								</div>
							</div>
						</fieldset>
					{/if}
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

					{capture append=additionalSettings}
						<div class="formField">
							<label><input type="checkbox" name="enableComments" value="1" {if $enableComments == 1}checked="checked" {/if}/> {lang}wsif.entry.enableComments{/lang}</label>
						</div>
						<div class="formFieldDesc">
							<p>{lang}wsif.entry.enableComments.description{/lang}</p>
						</div>
					{/capture}
					{include file='messageFormTabs'}
				</fieldset>

				{include file='captcha'}
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>

		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
			<input type="hidden" name="imageID" value="{@$imageID}" />
			<input type="hidden" name="fileID" value="{@$fileID}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>