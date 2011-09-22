{if $categories|count > 0}	
	{cycle name='categoryListCycle' values='1,2' advance=false print=false}
	<ul id="categoryList">
		{foreach from=$categories item=category}
			{assign var="categoryID" value=$category->categoryID}
			{counter assign=categoryNo print=false}
			<li class="category border">
				<div class="categoryListInner container-{cycle name='categoryListCycle'} category{@$categoryID}">
					<div class="categoryListTitle{if CATEGORY_LIST_ENABLE_LAST_ENTRY && CATEGORY_LIST_ENABLE_STATS} categoryListCols-3{else}{if CATEGORY_LIST_ENABLE_LAST_ENTRY || CATEGORY_LIST_ENABLE_STATS} categoryListCols-2{/if}{/if}">
						<div class="containerIcon">
							<img id="categoryIcon{@$categoryNo}" src="{icon}{@$category->getIconName()}M.png{/icon}" alt="" />
						</div>
						
						<div class="containerContent">
							<h3 class="categoryTitle">
								<a id="categoryLink{@$categoryNo}" href="index.php?page=Category&amp;categoryID={@$categoryID}{@SID_ARG_2ND}">{@$category->getTitle()}</a>
							</h3>
								
							{if $category->getFormattedDescription()}
								<p class="categoryListDescription">
									{@$category->getFormattedDescription()}
								</p>
							{/if}
								
							{if $subCategories.$categoryID|isset}
								<div class="categoryListSubCategories">
									<ul>
										{foreach name='subCategories' from=$subCategories.$categoryID item=subCategory}
											{assign var="subCategoryID" value=$subCategory->categoryID}
											{counter assign=categoryNo print=false}
											<li{if $tpl.foreach.subCategories.last} class="last"{/if}>
												<h4><img id="categoryIcon{@$categoryNo}" src="{icon}{$subCategory->getIconName()}S.png{/icon}" alt="" /> <a id="categoryLink{@$categoryNo}" href="index.php?page=Category&amp;categoryID={@$subCategoryID}{@SID_ARG_2ND}">{@$subCategory->getTitle()}</a></h4>
											</li>
										{/foreach}
									</ul>
								</div>
							{/if}
								
							{if $additionalCategoryBoxes.$categoryID|isset}{@$additionalCategoryBoxes.$categoryID}{/if}
						</div>
					</div>
						
					{if $lastEntries.$categoryID|isset}
						<div class="categoryListLastItem">								
							<div class="containerIconSmall">
								<a href="index.php?page=Entry&amp;entryID={@$lastEntries.$categoryID->entryID}{@SID_ARG_2ND}"><img src="{icon}goToEntryS.png{/icon}" alt="" title="{lang}wsif.index.goToEntry{/lang}" /></a>
							</div>
							<div class="containerContentSmall">
								<p>
									<span class="prefix">{if $lastEntries.$categoryID->prefixID}{@$lastEntries.$categoryID->getPrefix()->getStyledPrefix()}{/if}</span>
									<a href="index.php?page=Entry&amp;entryID={@$lastEntries.$categoryID->entryID}{@SID_ARG_2ND}">{$lastEntries.$categoryID->subject}</a>
								</p>
								<p>{lang}wsif.category.entries.entryBy{/lang}
									{if $lastEntries.$categoryID->userID != 0}
										<a href="index.php?page=User&amp;userID={@$lastEntries.$categoryID->userID}{@SID_ARG_2ND}">{$lastEntries.$categoryID->username}</a>
									{else}
										{$lastEntries.$categoryID->username}
									{/if}
									<span class="light">({@$lastEntries.$categoryID->time|shorttime})</span>
								</p>
							</div>
						</div>
					{/if}
						
					{if $categoryStats.$categoryID|isset}
						<div class="categoryListStats">
							{if $category->isExternalLink()}
								<dl>
									<dt>{lang}wsif.category.stats.clicks{/lang}</dt>
									<dd>{#$categoryStats.$categoryID.clicks}</dd>
								</dl>
							{else}
								<dl>
									<dt>{lang}wsif.category.stats.entries{/lang}</dt>
									<dd>{#$categoryStats.$categoryID.entries}</dd>
								</dl>
								<dl>
									<dt>{lang}wsif.category.stats.entryImages{/lang}</dt>
									<dd>{#$categoryStats.$categoryID.entryImages}</dd>
								</dl>
								<dl>
									<dt>{lang}wsif.category.stats.entryFiles{/lang}</dt>
									<dd>{#$categoryStats.$categoryID.entryFiles}</dd>
								</dl>
								<dl>
									<dt>{lang}wsif.category.stats.entryDownloads{/lang}</dt>
									<dd>{#$categoryStats.$categoryID.entryDownloads}</dd>
								</dl>
							{/if}
						</div>
					{/if}
				</div>
			</li>
		{/foreach}
	</ul>
{/if}