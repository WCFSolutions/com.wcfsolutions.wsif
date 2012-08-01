{if $permissions.canHandleEntry || $permissions.canHandleEntry}
	<script type="text/javascript">
		//<![CDATA[
		if (typeof language == 'undefined') var language = new Object();
		var entryData = new Hash();
		var url = 'index.php?page=Entry&threadID={@$entry->entryID}{@SID_ARG_2ND_NOT_ENCODED}';
		//]]>
	</script>
	{include file='entryInlineEdit' pageType=entry}
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

<ul class="breadCrumbs">
	<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="{icon}indexS.png{/icon}" alt="" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
	{foreach from=$category->getParentCategories() item=parentCategory}
		<li><a href="index.php?page=Category&amp;categoryID={@$parentCategory->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$parentCategory->getIconName()}S.png{/icon}" alt="" /> <span>{@$parentCategory->getTitle()}</span></a> &raquo;</li>
	{/foreach}
	<li><a href="index.php?page=Category&amp;categoryID={@$category->categoryID}{@SID_ARG_2ND}"><img src="{icon}{@$category->getIconName()}S.png{/icon}" alt="" /> <span>{@$category->getTitle()}</span></a> &raquo;</li>
</ul>

<div class="mainHeadline">
	<img id="entryEdit{@$entry->entryID}" src="{icon}entryL.png{/icon}" alt="" />
	<div class="headlineContainer">
		<h2 id="entryTitle{@$entry->entryID}">
			<span id="entryPrefix{@$entry->entryID}" class="prefix"><strong>{if $entry->prefixID}{@$entry->getPrefix()->getStyledPrefix()}{/if}</strong></span>
			<span class="title"><a href="index.php?page=Entry&amp;entryID={@$entryID}{@SID_ARG_2ND}">{$entry->subject}</a></span>
		</h2>
		{if $enableRating}<p id="com.wcfsolutions.wsif-ratingOutput{@$entry->entryID}">{@$entry->getRatingOutput()}</p>{/if}
	</div>
</div>

{if $userMessages|isset}{@$userMessages}{/if}

{if !$activeTabMenuItem|isset}{assign var=activeTabMenuItem value=''}{/if}
<div id="entryContent" class="tabMenu">
	<ul>
		<li{if $activeTabMenuItem == 'entry'} class="activeTabMenu"{/if}><a href="index.php?page=Entry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}"><img src="{icon}entryM.png{/icon}" alt="" /> <span>{lang}wsif.entry.overview{/lang}</span></a></li>
		{if MODULE_COMMENT && $entry->enableComments}<li{if $activeTabMenuItem == 'entryComments'} class="activeTabMenu"{/if}><a href="index.php?page=EntryComments&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}"><img src="{icon}entryCommentM.png{/icon}" alt="" /> <span>{lang}wsif.entry.comments{/lang}{if $entry->comments} ({#$entry->comments}){/if}</span></a></li>{/if}
		{if $entry->images || ($this->user->userID && $this->user->userID == $entry->userID) || $entry->isEditable($category)}<li{if $activeTabMenuItem == 'entryImages'} class="activeTabMenu"{/if}><a href="index.php?page=EntryImages&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}"><img src="{icon}entryImageM.png{/icon}" alt="" /> <span>{lang}wsif.entry.images{/lang}{if $entry->images} ({#$entry->images}){/if}</span></a></li>{/if}
		<li{if $activeTabMenuItem == 'entryFiles'} class="activeTabMenu"{/if}><a href="index.php?page=EntryFiles&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}"><img src="{icon}entryFileM.png{/icon}" alt="" /> <span>{lang}wsif.entry.files{/lang}{if $entry->files} ({#$entry->files}){/if}</span></a></li>
		{if $additionalTabs|isset}{@$additionalTabs}{/if}
	</ul>
</div>

<div class="subTabMenu">
	<div class="containerHead">
		<div> </div>
	</div>
</div>