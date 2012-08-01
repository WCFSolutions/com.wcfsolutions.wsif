<div class="entryList">
	{assign var="entry" value=$item.message}
	{assign var="entryID" value=$entry->entryID}
	<div class="message">
		<div class="messageInner container-{cycle name='results'}">
			<div class="entryImage">
				{if $entry->defaultImageID}
					{if $entry->getImage()->hasThumbnail}
						<a href="index.php?page=EntryImageShow&amp;imageID={@$entry->getImage()->imageID}{@SID_ARG_2ND}" title="{$entry->getImage()->title}"><img src="index.php?page=EntryImageShow&amp;imageID={@$entry->getImage()->imageID}{if $entry->getImage()->hasThumbnail}&amp;thumbnail=1{/if}{@SID_ARG_2ND}" alt="{$entry->getImage()->title}" /></a>
					{else}
						<img src="index.php?page=EntryImageShow&amp;imageID={@$entry->getImage()->imageID}{@SID_ARG_2ND}" alt="{$entry->getImage()->title}" title="{$entry->getImage()->title}" />
					{/if}
				{else}
					<img src="images/noThumbnail.png" alt="" />
				{/if}
			</div>
			<div class="entryDetails">
				<div class="messageHeader">
					<p class="messageCount">
						{@$entry->getLanguageIcon()}
					</p>
					<div class="containerIcon">
						<img src="{icon}{@$entry->getIconName()}M.png{/icon}" alt="" />
					</div>
					<div class="containerContent">
						<h3 class="subject">
							<span class="prefix"><strong>{if $entry->prefixID}{@$entry->getPrefix()->getStyledPrefix()}{/if}</strong></span>
							<a href="index.php?page=Entry&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}">{$entry->subject}</a>
						</h3>
						<p class="light smallFont">{lang}wsif.entry.by{/lang} {if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{@SID_ARG_2ND}">{$entry->username}</a>{else}{$entry->username}{/if} ({@$entry->time|time})</p>
					</div>
				</div>

				<div class="messageBody">
					{$entry->teaser}
				</div>

				<div class="editNote smallFont light">
					<p>{lang}wsif.entry.downloads{/lang}: {#$entry->downloads}</p>
					{if MODULE_COMMENT && $entry->enableComments}{/if}<p>{lang}wsif.entry.comments{/lang}: {#$entry->comments}</p>{/if}
					<p>{lang}wsif.entry.views{/lang}: {#$entry->views}</p>
				</div>

				<div class="messageFooter">
					<div class="smallButtons">
						<ul>
							<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
						</ul>
					</div>
				</div>
				<hr />
			</div>
		</div>
	</div>
</div>