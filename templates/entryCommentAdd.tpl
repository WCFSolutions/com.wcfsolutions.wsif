{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}

	{include file='imageViewer'}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var INLINE_IMAGE_MAX_WIDTH = {@INLINE_IMAGE_MAX_WIDTH};
		//]]>
	</script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ImageResizer.class.js"></script>
	{if $canUseBBCodes}{include file="wysiwyg"}{/if}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{capture append='additionalMessages'}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
	{/capture}

	{include file="entryHeader" activeTabMenuItem='entryComments'}

	<form method="post" enctype="multipart/form-data" action="index.php?form=EntryComment{@$action|ucfirst}{if $action == 'add'}&amp;entryID={@$entryID}{elseif $action == 'edit'}&amp;commentID={@$commentID}{/if}">
		<div class="border tabMenuContent">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wsif.entry.comment.{@$action}{/lang}</h3>

				<div class="contentHeader">
					<div class="largeButtons">
						<ul>
							<li><a href="index.php?page=EntryComments&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.comments{/lang}"><img src="{icon}entryCommentM.png{/icon}" alt="" /> <span>{lang}wsif.entry.comments{/lang}</span></a></li>
							{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
						</ul>
					</div>
				</div>

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

				<fieldset>
					<legend>{lang}wsif.entry.comment.information{/lang}</legend>

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
							<label for="subject">{lang}wsif.entry.comment.subject{/lang}</label>
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

					{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
				</fieldset>

				<fieldset>
					<legend>{lang}wsif.entry.comment.message{/lang}</legend>

					<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
						<div class="formFieldLabel">
							<label for="text">{lang}wsif.entry.comment.message{/lang}</label>
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

				{include file='captcha'}
				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>

		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="submit" name="preview" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
			<input type="hidden" name="idHash" value="{$idHash}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>