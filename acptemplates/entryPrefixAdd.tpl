{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIF_DIR}icon/entryPrefix{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsif.acp.entry.prefix.{@$action}{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wsif.acp.entry.prefix.{@$action}.success{/lang}</p>	
{/if}

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=EntryPrefixList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WSIF_DIR}icon/entryPrefixM.png" alt="" title="{lang}wsif.acp.entry.prefix.view{/lang}" /> <span>{lang}wsif.acp.entry.prefix.view{/lang}</span></a></li></ul>
	</div>
</div>
<form method="post" action="index.php?form=EntryPrefix{@$action|ucfirst}">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wsif.acp.entry.prefix.prefixType{/lang}</legend>
						
				<div class="formGroup">
					<div class="formGroupLabel">
						{lang}wsif.acp.entry.prefix.prefixType{/lang}
					</div>
					<div class="formGroupField">
						<fieldset>
							<legend>{lang}wsif.acp.entry.prefix.prefixType{/lang}</legend>
							<div class="formField"{if $errorField == 'prefixType'} formError{/if}>
								<ul class="formOptions">
									<li><label><input onclick="openList('categories', { setVisible: false })" type="radio" name="prefixType" value="0" {if $prefixType == 0}checked="checked" {/if} /> {lang}wsif.acp.entry.prefix.prefixType.0{/lang}</label></li>
									<li><label><input onclick="openList('categories', { setVisible: true })" type="radio" name="prefixType" value="1" {if $prefixType == 1}checked="checked" {/if} /> {lang}wsif.acp.entry.prefix.prefixType.1{/lang}</label></li>
								</ul>
								{if $errorField == 'prefixType'}
									<p class="innerError">
										{if $errorType == 'invalid'}{lang}wsif.acp.entry.prefix.prefixType.invalid{/lang}{/if}
									</p>
								{/if}
							</div>
						</fieldset>
					</div>
				</div>
						
				{if $prefixType != 1}
					<script type="text/javascript">
						//<![CDATA[
						onloadEvents.push(function() { document.getElementById('categories').style.display = 'none'; });
						//]]>
					</script>
				{/if}
				<div id="categories" class="formElement{if $errorField == 'assignedCategories'} formError{/if}">
					<div class="formFieldLabel">
						<label>{lang}wsif.acp.entry.prefix.assignedCategories{/lang}</label>
					</div>
					<div class="formField longSelect">
						<select name="categoryIDs[]" id="categoryIDs" multiple="multiple" size="10">
							{htmloptions options=$categoryOptions selected=$categoryIDs disableEncoding=true}
						</select>
						{if $errorField == 'assignedCategories'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc">
						<p>{lang}wsif.acp.entry.prefix.assignedCategories.description{/lang}</p>
						<p>{lang}wcf.global.multiSelect{/lang}</p>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lang}wsif.acp.entry.prefix.general{/lang}</legend>
				
				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wsif.acp.entry.prefix.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=EntryPrefixEdit&amp;prefixID={@$prefixID}&amp;languageID='+this.value+'&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wsif.acp.entry.prefix.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}
				
				<div class="formElement{if $errorField == 'prefixName'} formError{/if}" id="titleDiv">
					<div class="formFieldLabel">
						<label for="prefixName">{lang}wsif.acp.entry.prefix.prefixName{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="prefixName" id="prefixName" value="{$prefixName}" />
						{if $errorField == 'prefixName'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="prefixNameHelpMessage">
						<p>{lang}wsif.acp.entry.prefix.prefixName.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('prefixName');
				//]]></script>
				
				<div class="formElement{if $errorField == 'prefixMarking'} formError{/if}" id="titleDiv">
					<div class="formFieldLabel">
						<label for="prefixName">{lang}wsif.acp.entry.prefix.prefixMarking{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" name="prefixMarking" id="prefixMarking" value="{$prefixMarking}" />
						{if $errorField == 'prefixMarking'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="prefixMarkingHelpMessage">
						<p>{lang}wsif.acp.entry.prefix.prefixMarking.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('prefixMarking');
				//]]></script>
				
				<div class="formElement" id="showOrderDiv">
					<div class="formFieldLabel">
						<label for="showOrder">{lang}wsif.acp.entry.prefix.showOrder{/lang}</label>
					</div>
					<div class="formField">	
						<input type="text" class="inputText" name="showOrder" id="showOrder" value="{$showOrder}" />
					</div>
					<div class="formFieldDesc hidden" id="showOrderHelpMessage">
						{lang}wsif.acp.entry.prefix.showOrder.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('showOrder');
				//]]></script>
			</fieldset>
			
			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>

	<div class="formSubmit">
		<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		{if $prefixID|isset}<input type="hidden" name="prefixID" value="{@$prefixID}" />{/if}
 	</div>
</form>

{include file='footer'}