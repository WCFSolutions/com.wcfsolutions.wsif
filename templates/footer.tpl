{if $additionalFooterContents|isset}{@$additionalFooterContents}{/if}
</div>
<div id="footerContainer">
	<div id="footer">
		{include file=footerMenu}
		<div id="footerOptions" class="footerOptions">
			<div class="footerOptionsInner">
				<ul>
					{if $additionalFooterOptions|isset}{@$additionalFooterOptions}{/if}
					
					{if $stylePickerOptions|count > 1}
						<li class="stylePicker{if !SHOW_CLOCK} last{/if}">
							<a id="changeStyle" class="hidden"><img src="{icon}styleOptionsS.png{/icon}" alt="" /> <span>{lang}wsif.global.changeStyle{/lang}</span></a>
							<div class="hidden" id="changeStyleMenu">
								<ul>
									{foreach from=$stylePickerOptions item=style key=styleID}
										<li{if $styleID == $this->style->styleID} class="active"{/if}><a rel="nofollow" href="{if $this->session->requestURI && $this->session->requestMethod == 'GET'}{$this->session->requestURI}{if $this->session->requestURI|strpos:'?'}&amp;{else}?{/if}{else}index.php?{/if}styleID={$styleID}{@SID_ARG_2ND}"><span>{$style}</span></a></li>
									{/foreach}
								</ul>
							</div>
							
							<script type="text/javascript">
								//<![CDATA[
								onloadEvents.push(function() { document.getElementById('changeStyle').className=''; });
								popupMenuList.register('changeStyle');
								//]]>
							</script>
							
							<noscript>
								<form method="get" action="index.php" class="quickJump">
									<div>
										<input type="hidden" name="page" value="Index" />
										<select name="styleID" onchange="if (this.options[this.selectedIndex].value != 0) this.form.submit()">
											<option value="0">{lang}wsif.global.changeStyle{/lang}</option>
											<option value="0">-----------------------</option>
											{htmlOptions options=$stylePickerOptions selected=$this->style->styleID}
										</select>
										{@SID_INPUT_TAG}
										<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
									</div>
								</form>
							</noscript>
						</li>
					{/if}
					{if SHOW_CLOCK}
						<li id="date" class="date last" title="{@TIME_NOW|fulldate} UTC{if $timezone > 0}+{@$timezone}{else if $timezone < 0}{@$timezone}{/if}"><em><img src="{icon}dateS.png{/icon}" alt="" /> <span>{@TIME_NOW|fulldate}</span></em></li>
					{/if}
					<li id="toTopLink" class="last extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
				</ul>
			</div>
		</div>
		<p class="copyright">{lang}wsif.global.copyright{/lang}{if $additionalCopyrightContents|isset}{@$additionalCopyrightContents}{/if}</p>
	</div>
</div>
{if !$this->user->userID && !LOGIN_USE_CAPTCHA}
	<div class="border loginPopup hidden" id="quickLoginBox">
		<form method="post" action="index.php?form=UserLogin" class="container-1">
			<div>
				<input tabindex="1" type="text" class="inputText" id="quickLoginUsername" name="loginUsername" value="{lang}wcf.user.username{/lang}" title="{lang}wcf.user.username{/lang}" />
				<input tabindex="2" type="password" class="inputText" id="quickLoginPassword" name="loginPassword" value="" title="{lang}wcf.user.password{/lang}" />
				{if $this->session->requestMethod == "GET"}<input type="hidden" name="url" value="{$this->session->requestURI}" />{/if}
				{@SID_INPUT_TAG}
				<input tabindex="4" type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
			</div>
			<p><label><input tabindex="3" type="checkbox" id="useCookies" name="useCookies" value="1" /> {lang}wsif.header.login.useCookies{/lang}</label></p>
		</form>
	</div>
{/if}