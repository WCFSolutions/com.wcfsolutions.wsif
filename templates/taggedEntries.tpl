			<div class="entryList">
				{cycle values='container-1,container-2' name='className' print=false advance=false}
				{assign var='messageNumber' value=$items-$startIndex+1}
				{foreach from=$taggedObjects item=entry}
					{assign var="entryID" value=$entry->entryID}
					<div class="message">
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
					{assign var='messageNumber' value=$messageNumber-1}
				{/foreach}
			</div>