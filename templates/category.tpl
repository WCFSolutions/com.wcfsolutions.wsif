{include file="documentHeader"}
<head xmlns="http://www.w3.org/1999/html">
	<title>{@$category->getTitle()} {if $pageNo > 1}- {lang}wcf.page.pageNo{/lang} {/if}- {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<link rel="alternate" type="application/rss+xml" href="index.php?page=EntriesFeed&amp;format=rss2&amp;categoryID={@$categoryID}" title="{lang}wsif.category.feed{/lang} (RSS2)" />
	<link rel="alternate" type="application/atom+xml" href="index.php?page=EntriesFeed&amp;format=atom&amp;categoryID={@$categoryID}" title="{lang}wsif.category.feed{/lang} (Atom)" />
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{* --- quick search controls --- *}
{assign var='searchFieldTitle' value='{lang}wsif.category.search.query{/lang}'}
{capture assign=searchHiddenFields}
	<input type="hidden" name="categoryIDs[]" value="{@$categoryID}" />
	<input type="hidden" name="types[]" value="entry" />
{/capture}
{* --- end --- *}
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
		{foreach from=$category->getParentCategories() item=parentCategory}
			<li><a href="index.php?page=Category&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{@$parentCategory->getTitle()}</span></a> &raquo;</li>
		{/foreach}
	</ul>

	<div class="mainHeadline">
		<img src="{icon}{@$category->getIconName()}L.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2><a href="index.php?page=Category&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{@$category->getTitle()}</a></h2>
			<p>{@$category->getFormattedDescription()}</p>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	{include file="categoryList"}

	{if $category->isCategory()}
		<div class="border content">
			<div class="container-1">

				{if $permissions.canHandleEntry}
					<script type="text/javascript">
						//<![CDATA[
						var language = new Object();
						//]]>
					</script>
					{include file='entryInlineEdit' pageType=category}
				{/if}

				{if $entries|count > 0}
					<div class="contentBox">
						<h3 class="subHeadline">{if $tagID}{lang}wsif.category.entries.tagged{/lang}{else}{lang}wsif.category.entries{/lang}{/if} <span>({#$items})</span></h3>

						<div class="contentHeader">
							{assign var=multiplePagesLink value="index.php?page=Category&categoryID=$categoryID&pageNo=%d"}
							{if $sortField != $defaultSortField}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&sortField=':$sortField}{/if}
							{if $sortOrder != $defaultSortOrder}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&sortOrder=':$sortOrder}{/if}
							{if $daysPrune != $defaultDaysPrune}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&daysPrune=':$daysPrune}{/if}
							{if $prefixID != -1}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&prefix=':$prefixID}{/if}
							{if $languageID}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&languageID=':$languageID}{/if}
							{if $tagID}{assign var=multiplePagesLink value=$multiplePagesLink|concat:'&tagID=':$tagID}{/if}
							{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}
							{if $entries|count && $permissions.canHandleEntry}
								<div class="optionButtons">
									<ul>
										<li><a><label><input name="entryMarkAll" type="checkbox" /> <span>{lang}wsif.category.entries.markAll{/lang}</span></label></a></li>
									</ul>
								</div>
							{/if}
							{if $tagID || $languageID || $prefixID != -1 || $daysPrune != 1000 || $category->canAddEntry() || $additionalLargeButtons|isset}
								<div class="largeButtons">
									<ul>
										{if $tagID || $languageID || $prefixID != -1 || $daysPrune != 1000}<li><a href="index.php?page=Category&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsif.category.allEntries{/lang}"><img src="{icon}entryM.png{/icon}" alt="" /> <span>{lang}wsif.category.allEntries{/lang}</span></a></li>{/if}
										{if $category->canAddEntry()}<li><a href="index.php?form=EntryAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.add{/lang}"><img src="{icon}entryAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.add{/lang}</span></a></li>{/if}
										{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
									</ul>
								</div>
							{/if}
						</div>
							<div class="entryList">
								{cycle values='container-1,container-2' name='className' print=false advance=false}
								{if $sortOrder == 'DESC'}{assign var='messageNumber' value=$items-$startIndex+1}{else}{assign var='messageNumber' value=$startIndex}{/if}
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
													</div>
												</div>

												<div class="messageBody">
													<div id="entryMessagePreview{@$entry->entryID}">
														{$entry->teaser}
													</div>
												</div>

												{if $tags.$entryID|isset}
													<div class="editNote smallFont light">
														<p>{lang}wsif.entry.tags{/lang}: {implode from=$tags[$entryID] item=entryTag}<a href="index.php?page=Category&amp;categoryID={@$categoryID}&amp;tagID={@$entryTag->getID()}{@SID_ARG_2ND}">{$entryTag->getName()}</a>{/implode}</p>
													</div>
												{/if}

												<div class="editNote smallFont light">
													{if $entry->publishingTime}<p>{lang}wsif.entry.publishingTime{/lang}: {@$entry->publishingTime|time}{/if}
													<p>{lang}wsif.entry.downloads{/lang}: {#$entry->downloads}</p>
													{if MODULE_COMMENT && $entry->enableComments}<p>{lang}wsif.entry.comments{/lang}: {#$entry->comments}</p>{/if}
													<p>{lang}wsif.entry.views{/lang}: {#$entry->views}</p>
													{if $additionalInformationFields.$entryID|isset}{@$additionalInformationFields.$entryID}{/if}
												</div>

												{if $entry->isDeleted}
													<p class="deleteNote smallFont light">{lang}wsif.entry.deleteNote{/lang}</p>
												{/if}

												<div class="messageFooter">
													<div class="smallButtons">
														<ul>
															<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
															{if $category->getPermission('canDownloadEntryFile')}<li><a href="index.php?page=EntryFileDownload&amp;fileID={@$entry->defaultFileID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.download{/lang}"><img src="{icon}entryFileDownloadS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.download{/lang}</span></a></li>{/if}
															{if $additionalSmallButtons.$entryID|isset}{@$additionalSmallButtons.$entryID}{/if}
														</ul>
													</div>
												</div>
												<hr />
											</div>
										</div>
									</div>
									{if $sortOrder == 'DESC'}
										{assign var='messageNumber' value=$messageNumber-1}
									{else}
										{assign var='messageNumber' value=$messageNumber+1}
									{/if}
								{/foreach}
							</div>
						<div class="contentFooter">
							{@$pagesOutput}

							<div id="entryEditMarked" class="optionButtons"></div>

							{if $tagID || $languageID || $prefixID != -1 || $daysPrune != 1000 || $category->canAddEntry() || $additionalLargeButtons|isset}
								<div class="largeButtons">
									<ul>
										{if $tagID || $languageID || $prefixID != -1 || $daysPrune != 1000}<li><a href="index.php?page=Category&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsif.category.allEntries{/lang}"><img src="{icon}entryM.png{/icon}" alt="" /> <span>{lang}wsif.category.allEntries{/lang}</span></a></li>{/if}
										{if $category->canAddEntry()}<li><a href="index.php?form=EntryAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.add{/lang}"><img src="{icon}entryAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.add{/lang}</span></a></li>{/if}
										{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
									</ul>
								</div>
							{/if}
						</div>
					</div>
				{else}
					<h3 class="subHeadline">{lang}wsif.category.entries{/lang}</h3>
					<p>{lang}wsif.category.noEntries{/lang}</p>

					<div id="entryEditMarked" class="optionButtons"></div>

					{if $tagID || $languageID || $prefixID != -1 || $daysPrune != 1000 || $category->canAddEntry() || $additionalLargeButtons|isset}
						<div class="largeButtons">
							<ul>
								{if $tagID || $languageID || $prefixID != -1 || $daysPrune != 1000}<li><a href="index.php?page=Category&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsif.category.allEntries{/lang}"><img src="{icon}entryM.png{/icon}" alt="" /> <span>{lang}wsif.category.allEntries{/lang}</span></a></li>{/if}
								{if $category->canAddEntry()}<li><a href="index.php?form=EntryAdd&amp;categoryID={@$categoryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.add{/lang}"><img src="{icon}entryAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.add{/lang}</span></a></li>{/if}
								{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
							</ul>
						</div>
					{/if}
				{/if}

			</div>
		</div>
	{/if}

	{if $category->isCategory() || $availableTags|count || $additionalBoxes|isset}
		{cycle values='container-1,container-2' print=false advance=false}
		<div class="border infoBox">
			{if $category->isCategory()}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}sortM.png{/icon}" alt="" /> </div>
					<div class="containerContent">
						<h3>{lang}wsif.category.sorting{/lang}</h3>
						<form method="get" action="index.php">
							<div class="entrySort">
								<input type="hidden" name="page" value="Category" />
								<input type="hidden" name="categoryID" value="{@$categoryID}" />
								<input type="hidden" name="tagID" value="{@$tagID}" />

								<div class="floatedElement">
									<label for="sortField">{lang}wsif.category.sortBy{/lang}</label>
									<select name="sortField" id="sortField">
										<option value="subject"{if $sortField == 'subject'} selected="selected"{/if}>{lang}wsif.entry.subject{/lang}</option>
										<option value="username"{if $sortField == 'username'} selected="selected"{/if}>{lang}wsif.entry.username{/lang}</option>
										<option value="time"{if $sortField == 'time'} selected="selected"{/if}>{lang}wsif.entry.time{/lang}</option>
										<option value="views"{if $sortField == 'views'} selected="selected"{/if}>{lang}wsif.entry.views{/lang}</option>
										<option value="downloads"{if $sortField == 'downloads'} selected="selected"{/if}>{lang}wsif.entry.downloads{/lang}</option>
										{if $enableRating}<option value="ratingResult"{if $sortField == 'ratingResult'} selected="selected"{/if}>{lang}wsif.entry.rating{/lang}</option>{/if}
									</select>
									<select name="sortOrder">
										<option value="ASC"{if $sortOrder == 'ASC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
										<option value="DESC"{if $sortOrder == 'DESC'} selected="selected"{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
									</select>
								</div>

								<div class="floatedElement">
									<label for="filterDate">{lang}wsif.category.entries.filterByDate{/lang}</label>
									<select name="daysPrune" id="filterDate">
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
								</div>

								{if $category->getPrefixes()|count > 0}
									<div class="floatedElement">
										<label for="filterPrefix">{lang}wsif.category.entries.filterByPrefixID{/lang}</label>
										<select name="prefixID" id="filterPrefix">
											<option value="-1"></option>
											<option value="0"{if $prefixID == 0} selected="selected"{/if}>{lang}wsif.category.entries.filterByPrefixID.0{/lang}</option>
											{foreach from=$category->getPrefixes() item=prefix}
												<option value="{$prefix->prefixID}"{if $prefixID == $prefix->prefixID} selected="selected"{/if}>{@$prefix->getPrefixName()}</option>
											{/foreach}
										</select>
									</div>
								{/if}

								{if $contentLanguages|count > 1}
									<div class="floatedElement">
										<label for="filterByLanguage">{lang}wsif.category.filterByLanguage{/lang}</label>
										<select name="languageID" id="filterByLanguage">
											<option value="0"></option>
											{htmlOptions options=$contentLanguages selected=$languageID disableEncoding=true}
										</select>
									</div>
								{/if}

								<div class="floatedElement">
									<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
								</div>

								{@SID_INPUT_TAG}
							</div>
						</form>
					</div>
				</div>

				{if CATEGORY_ENABLE_STATS}
					<div class="{cycle}">
						<div class="containerIcon"><img src="{icon}statisticsM.png{/icon}" alt="" /></div>
						<div class="containerContent">
							<h3>{lang}wsif.category.stats{/lang}</h3>
							<p class="smallFont">{lang}wsif.category.stats.detail{/lang}</p>
						</div>
					</div>
				{/if}
			{/if}

			{if $availableTags|count}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}tagM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}wcf.tagging.filter{/lang}</h3>
						<ul class="tagCloud">
							{foreach from=$availableTags item=tag}
								<li><a href="index.php?page=Category&amp;categoryID={@$category->categoryID}&amp;pageNo={@$pageNo}&amp;sortField={@$sortField}&amp;sortOrder={@$sortOrder}&amp;daysPrune={@$daysPrune}&amp;prefixID={@$prefixID}&amp;languageID={@$languageID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}" style="font-size: {@$tag->getSize()}%">{$tag->getName()}</a></li>
							{/foreach}
						</ul>
					</div>
				</div>
			{/if}

			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}

	{include file='categoryQuickJump'}
</div>

{include file='footer' sandbox=false}
</body>
</html>