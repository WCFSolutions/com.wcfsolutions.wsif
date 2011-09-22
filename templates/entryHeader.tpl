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

{if $this->getEntryMenu()->getMenuItems('')|count > 1}
	<div id="entryContent" class="tabMenu">
		<ul>
			{foreach from=$this->getEntryMenu()->getMenuItems('') item=item}
				<li{if $item.menuItem|in_array:$this->getEntryMenu()->getActiveMenuItems()} class="activeTabMenu"{/if}><a href="{$item.menuItemLink}">{if $item.menuItemIcon}<img src="{$item.menuItemIcon}" alt="" /> {/if}<span>{lang}{@$item.menuItem}{/lang}</span></a></li>
			{/foreach}
		</ul>
	</div>
	
	<div class="subTabMenu">
		<div class="containerHead">
			{assign var=activeMenuItem value=$this->getEntryMenu()->getActiveMenuItem()}
			{if $activeMenuItem && $this->getEntryMenu()->getMenuItems($activeMenuItem)|count}
				<ul>
					{foreach from=$this->getEntryMenu()->getMenuItems($activeMenuItem) item=item}
						<li{if $item.menuItem|in_array:$this->getEntryMenu()->getActiveMenuItems()} class="activeSubTabMenu"{/if}><a href="{$item.menuItemLink}">{if $item.menuItemIcon}<img src="{$item.menuItemIcon}" alt="" /> {/if}<span>{lang}{@$item.menuItem}{/lang}</span></a></li>
					{/foreach}
				</ul>
			{else}
				<div> </div>
			{/if}
		</div>
	</div>
{/if}