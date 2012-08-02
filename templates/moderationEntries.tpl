{include file="documentHeader"}
<head>
	<title>{lang}wcf.moderation.{@$action}{/lang} {if $pageNo > 1}- {lang}wcf.page.pageNo{/lang} {/if}- {lang}wcf.moderation{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{include file='header' sandbox=false}

<div id="main">

	{include file="moderationCPHeader"}

	<div class="border tabMenuContent">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.moderation.{@$action}{/lang}{if $items > 0} <span>({#$items}){/if}</span></h3>

			{if $permissions.canHandleEntry}
				<script type="text/javascript">
					//<![CDATA[
					var language = new Object();
					//]]>
				</script>
				{include file='entryInlineEdit' pageType=$action}
			{/if}

			{if $entries|count == 0}
				<p>{lang}wsif.moderation.{@$action}.noEntries{/lang}</p>
			{else}
				<div class="contentHeader">
					{pages print=true assign=pagesOutput link="index.php?page=$pageName&pageNo=%d"|concat:SID_ARG_2ND_NOT_ENCODED}

					<div class="optionButtons">
						<ul>
							<li><a><label><input name="entryMarkAll" type="checkbox" /> <span>{lang}wsif.moderation.entries.markAll{/lang}</span></label></a></li>
						</ul>
					</div>
				</div>

				<div class="entryList">
					{cycle values='container-1,container-2' name='className' print=false advance=false}
					{assign var='messageNumber' value=$items-$startIndex+1}
					{foreach from=$entries item=entry}
						{assign var="entryID" value=$entry->entryID}
						<div class="message" id="entryRow{@$entry->entryID}">
							<div class="messageInner {cycle name='className'}">
								<div class="entryImage">
									<a href="index.php?page=Entry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}">
										{if $entry->defaultImageID}
											<img src="index.php?page=EntryImageShow&amp;imageID={@$entry->getImage()->imageID}{if $entry->getImage()->hasThumbnail}&amp;thumbnail=1{/if}{@SID_ARG_2ND}" alt="{$entry->getImage()->title}" />
											{else}
											<img src="images/noThumbnail.png" alt="" />
										{/if}
									</a>
								</div>
								<div class="entryDetails">
									<div class="messageHeader">
										<p class="messageCount">
											{@$entry->getLanguageIcon()}
											<a href="index.php?page=Entry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.permalink{/lang}" class="messageNumber">{#$messageNumber}</a>
											{if $permissions.canMarkEntry}
												<span class="messageMarkCheckBox">
													<input id="entryMark{@$entry->entryID}" type="checkbox" />
												</span>
											{/if}
										</p>
										<div class="containerIcon">
											{if $permissions.canHandleEntry}
												{cycle name='className' print=false}
												<script type="text/javascript">
													//<![CDATA[
													entryData.set({@$entry->entryID}, {
														'isMarked': {@$entry->isMarked()},
														'isDeleted': {@$entry->isDeleted},
														'isDisabled': {@$entry->isDisabled},
														'prefixID': {@$entry->prefixID}
													});
													//]]>
												</script>
											{/if}
											<img id="entryEdit{@$entry->entryID}" src="{icon}{@$entry->getIconName()}M.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h3 id="entryTitle{@$entry->entryID}" class="subject">
												<span id="entryPrefix{@$entry->entryID}" class="prefix"><strong>{if $entry->prefixID}{@$entry->getPrefix()->getStyledPrefix()}{/if}</strong></span>
												<a href="index.php?page=Entry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}">{$entry->subject}</a>
											</h3>
											<p class="light smallFont">{lang}wsif.entry.by{/lang} {if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}">{$entry->username}</a>{else}{$entry->username}{/if} ({@$entry->time|time})</p>
											{if ENTRY_ENABLE_RATING && $entry->getCategory()->enableRating == -1 ||  $entry->getCategory()->enableRating}<p class="rating">{@$entry->getRatingOutput()}</p>{/if}
										</div>
									</div>

									<div class="messageBody">
										<div id="entryMessagePreview{@$entry->entryID}">
											{$entry->teaser}
										</div>
									</div>

									<div class="editNote smallFont light">
										{if $entry->publishingTime}<p>{lang}wsif.entry.publishingTime{/lang}: {@$entry->publishingTime|time}{/if}
										<p>{lang}wsif.entry.downloads{/lang}: {#$entry->downloads}</p>
										{if MODULE_COMMENT && $entry->enableComments}<p>{lang}wsif.entry.comments{/lang}: {#$entry->comments}</p>{/if}
										<p>{lang}wsif.entry.views{/lang}: {#$entry->views}</p>
									</div>

									{if $entry->isDeleted}
										<p class="deleteNote smallFont light">{lang}wsif.entry.deleteNote{/lang}</p>
									{/if}

									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												{if $additionalSmallButtons.$entryID|isset}{@$additionalSmallButtons.$entryID}{/if}
											</ul>
										</div>
									</div>
									<hr />
								</div>
							</div>
						</div>
						{assign var='messageNumber' value=$messageNumber-1}
					{/foreach}
				</div>

				<div class="contentFooter">
					{@$pagesOutput}

					<div id="entryEditMarked" class="optionButtons"></div>
				</div>
			{/if}
		</div>
	</div>

</div>

{include file='footer' sandbox=false}

</body>
</html>