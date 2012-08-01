{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{capture append='userMessages'}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
		{if $success|isset}
			<p class="success">{lang}wsif.entry.image.{@$action}.success{/lang}</p>
		{/if}
	{/capture}

	{include file="entryHeader" activeTabMenuItem='entryImages'}

	<form method="post" enctype="multipart/form-data" action="index.php?form=EntryImage{@$action|ucfirst}{if $action == 'add'}&amp;entryID={@$entryID}{elseif $action == 'edit'}&amp;imageID={@$imageID}{/if}">
		<div class="border tabMenuContent">
			<div class="container-1">
				<h3 class="subHeadline">{lang}wsif.entry.image.{@$action}{/lang}</h3>

				<div class="contentHeader">
					<div class="largeButtons">
						<ul>
							<li><a href="index.php?page=EntryImages&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.images{/lang}"><img src="{icon}entryImageM.png{/icon}" alt="" /> <span>{lang}wsif.entry.images{/lang}</span></a></li>
							{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
						</ul>
					</div>
				</div>

				<fieldset>
					<legend>{lang}wsif.entry.image.information{/lang}</legend>
					{if $action == 'add'}<p class="formFieldDesc">{lang}wsif.entry.image.information.description{/lang}</p>{/if}

					<div class="formElement{if $errorField == 'title'} formError{/if}">
						<div class="formFieldLabel">
							<label for="title">{lang}wsif.entry.image.title{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="title" name="title" value="{$title}" tabindex="{counter name='tabindex'}" />
							{if $errorField == 'title'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						{if $action == 'add'}<p class="formFieldDesc">{lang}wsif.entry.image.title.description{/lang}</p>{/if}
					</div>

					<div class="formElement">
						<div class="formFieldLabel">
							<label for="description">{lang}wsif.entry.image.description{/lang}</label>
						</div>
						<div class="formField">
							<textarea name="description" id="description" rows="10" cols="40" tabindex="{counter name='tabindex'}">{$description}</textarea>
						</div>
					</div>

					{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
				</fieldset>

				{if $action == 'add'}
					<fieldset{if $errorField == 'upload'} class="formError"{/if}>
						<legend>{lang}wsif.entry.image.upload{/lang}</legend>
						<ol id="uploadFields" class="itemList">
							<li>
								<div class="buttons">
									<a href="#delete" title="{lang}wcf.global.button.delete{/lang}" class="hidden"><img src="{icon}deleteS.png{/icon}" longdesc="" alt="" /></a>
								</div>
								<div class="itemListTitle">
									<input type="file" size="50" name="upload[]" tabindex="{counter name='tabindex'}" />
								</div>
							</li>
						</ol>

						{if $errorField == 'upload'}
							<div class="innerError">
								{if $errorType|is_array}
									{foreach from=$errorType item=error}
										<p>
											{if $error.errorType == 'uploadFailed'}{lang}wsif.entry.image.upload.error.uploadFailed{/lang}{/if}
											{if $error.errorType == 'copyFailed'}{lang}wsif.entry.image.upload.error.copyFailed{/lang}{/if}
											{if $error.errorType == 'illegalExtension'}{lang}wsif.entry.image.upload.error.illegalExtension{/lang}{/if}
											{if $error.errorType == 'tooLarge'}{lang}wsif.entry.image.upload.error.tooLarge{/lang}{/if}
											{if $error.errorType == 'badImage'}{lang}wsif.entry.image.upload.error.badImage{/lang}{/if}
											{if $error.errorType == 'tooManyImages'}{lang}wsif.entry.image.upload.error.tooManyImages{/lang}{/if}
										</p>
									{/foreach}
								{elseif $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</div>
						{/if}

						<div class="formFieldDesc">
							<p>{lang}wsif.entry.image.upload.description{/lang}</p>
						</div>

						<script type="text/javascript">
							//<![CDATA[
							var openUploads = {@$freeImages} - 1;
							function addUploadField() {
								if (openUploads > 0) {
									var fileInput = new Element('input', { 'type': 'file', 'name': 'upload[]', 'size': 50, 'tabindex': {counter name='tabindex'} });
									var fileDiv = new Element('div').addClassName('itemListTitle');
									var deleteButton = new Element('a', { 'href': '#delete', 'title': '{lang}wcf.global.button.delete{/lang}' });
									deleteButton.addClassName('hidden');
									var deleteImg = new Element('img', { 'src': '{icon}deleteS.png{/icon}', 'longdesc': '' });
									var buttons = new Element('div').addClassName('buttons').insert(deleteButton.insert(deleteImg));

									$('uploadFields').insert(new Element('li').insert(buttons).insert(fileDiv.insert(fileInput)));
									deleteButton.observe('click', removeUploadField);
									fileInput.observe('change', uploadFieldChanged);
									openUploads--;
								}
							}

							function removeUploadField(evt) {
								var fileInput = evt.findElement().up('li').down('input');
								var emptyField = true;
								var counter = 0;
								$$('#uploadFields input[type=file]').each(function(input) {
									if (input.value == '') {
										emptyField = true;
									}
									counter++;
								});
								if (emptyField && fileInput.value != '' && counter > 1) {
									fileInput.up('li').fade({
										'duration': '0.5', afterFinish: function() { fileInput.up('li').remove(); }
									});
									openUploads++;
								}
								else {
									fileInput.value = '';
								}
								evt.stop();
							}

							function uploadFieldChanged(e) {
								if (!e) e = window.event;

								if (e.target) var inputField = e.target;
								else if (e.srcElement) var inputField = e.srcElement;

								var emptyField = false;
								$$('#uploadFields input[type=file]').each(function(input) {
									if (input.value == '') emptyField = true;
								});

								if (!emptyField && inputField.value != '' && inputField.value != inputField.oldValue) {
									inputField.oldValue = inputField.value;
									addUploadField();
								}
								if (inputField.value == '') {
									$(inputField).up('li').down('a[href*="#delete"]').addClassName('hidden');
								}
								else {
									$(inputField).up('li').down('a[href*="#delete"]').removeClassName('hidden');
								}
							}

							// add button
							document.observe('dom:loaded', function() {
								$$('#uploadFields input[type=file]').invoke('observe', 'change', uploadFieldChanged);
								$$('#uploadFields a[href*="#delete"]').invoke('observe', 'click', removeUploadField);
							});
							//]]>
						</script>
					</fieldset>
				{/if}

				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>

		<div class="formSubmit">
			<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
			<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
			{@SID_INPUT_TAG}
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>