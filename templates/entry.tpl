{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{include file="entryHeader" activeTabMenuItem='entry'}

	<div class="border">
		<div class="layout-2">
			<div class="columnContainer">
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wsif.entry{/lang}</h3>

							<div class="entryMessage">{@$entry->getFormattedMessage()}</div>

							{if !$category->getPermission('canDownloadEntryFile')}
								<div class="info">
									{lang}wsif.entry.noDownloadPermission{/lang}
								</div>
							{/if}

							{if !$socialBookmarks|empty}
								<div class="buttonBar">
									{@$socialBookmarks}
								</div>
							{/if}

							<div class="buttonBar">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if $entry->isEditable($category)}<li><a href="index.php?form=EntryEdit&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsif.entry.edit{/lang}</span></a></li>{/if}
										{if $this->user->userID}
											{if !$entry->subscribed}
												<li><a href="index.php?action=EntrySubscribe&amp;entryID={@$entry->entryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsif.entry.subscribe{/lang}"><img src="{icon}entrySubscribeS.png{/icon}" alt="" /> <span>{lang}wsif.entry.subscribe{/lang}</span></a></li>
												{else}
												<li><a href="index.php?action=EntryUnsubscribe&amp;entryID={@$entry->entryID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsif.entry.unsubscribe{/lang}"><img src="{icon}entryUnsubscribeS.png{/icon}" alt="" /> <span>{lang}wsif.entry.unsubscribe{/lang}</span></a></li>
											{/if}
										{/if}
										{if $category->getPermission('canDownloadEntryFile')}<li><a href="index.php?page=EntryFileDownload&amp;fileID={@$entry->defaultFileID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.download{/lang}"><img src="{icon}entryFileDownloadS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.download{/lang}</span></a></li>{/if}
										{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
									</ul>
								</div>
							</div>

							{if $additionalContent1|isset}{@$additionalContent1}{/if}

							{if $entryComments|count > 0}
								<div class="contentBox">
									<h3 class="subHeadline"><a href="index.php?page=EntryComments&amp;entryID={@$entryID}{@SID_ARG_2ND}">{lang}wsif.entry.comments{/lang}</a> <span>({#$entry->comments})</span></h3>

									<ul class="dataList">
										{foreach from=$entryComments item=comment}
											<li class="{cycle values='container-1,container-2'}">
												<div class="containerIcon">
													<img src="{icon}entryCommentM.png{/icon}" alt="" />
												</div>
												<div class="containerContent">
													<h4><a href="index.php?page=EntryComments&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}#comment{@$comment->commentID}">{@$comment->subject}</a></h4>
													<p class="firstPost smallFont light">{lang}wsif.entry.comment.by{/lang} {if $comment->userID}<a href="index.php?page=User&amp;userID={@$comment->userID}{@SID_ARG_2ND}">{$comment->username}</a>{else}{$comment->username}{/if} ({@$comment->time|time})</p>
												</div>
											</li>
										{/foreach}
									</ul>
									<div class="buttonBar">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												<li><a href="index.php?page=EntryComments&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.allComments{/lang}"><img src="{icon}entryCommentS.png{/icon}" alt="" /> <span>{lang}wsif.entry.allComments{/lang}</span></a></li>
											</ul>
										</div>
									</div>
								</div>
							{/if}

							{if $additionalContent2|isset}{@$additionalContent2}{/if}

							{if $entryImages|count > 0}
								<div class="contentBox">
									<h3 class="subHeadline"><a href="index.php?page=EntryImages&amp;entryID={@$entryID}{@SID_ARG_2ND}">{lang}wsif.entry.images{/lang}</a> <span>({#$entry->images})</span></h3>

									<ul class="dataList thumbnailView squared floatContainer container-1">
										{foreach name='images' from=$entryImages item=image}
											<li class="floatedElement smallFont{if $tpl.foreach.images.iteration == 5} last{/if}">
												<a href="index.php?page=EntryImage&amp;imageID={@$image->imageID}{@SID_ARG_2ND}" title="{$image->title}">
													<span class="thumbnail" style="width: 75px;">
														<img src="index.php?page=EntryImageShow&amp;imageID={@$image->imageID}{if $image->hasThumbnail}&amp;thumbnail=1{/if}{@SID_ARG_2ND}" alt="{$image->title}" style="width: 75px;" />
													</span>
													<span class="avatarCaption{if $image->isDefault} default{/if}">{$image->title}</span>
												</a>
												<p class="light smallFont">{@$entry->time|time}</p>
											</li>
										{/foreach}
									</ul>
									<div class="buttonBar">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												<li><a href="index.php?page=EntryImages&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.allImages{/lang}"><img src="{icon}entryImageS.png{/icon}" alt="" /> <span>{lang}wsif.entry.allImages{/lang}</span></a></li>
											</ul>
										</div>
									</div>
								</div>
							{/if}

							{if $additionalContent3|isset}{@$additionalContent3}{/if}

							{if $entryFiles|count > 0}
								<div class="contentBox">
									<h3 class="subHeadline"><a href="index.php?page=EntryFiles&amp;entryID={@$entryID}{@SID_ARG_2ND}">{lang}wsif.entry.files{/lang}</a> <span>({#$entry->files})</span></h3>

									<ul class="dataList">
										{foreach from=$entryFiles item=file}
											<li class="{cycle values='container-1,container-2'}">
												<div class="containerIcon">
													<img src="{icon}entryFileM.png{/icon}" alt="" />
												</div>
												<div class="containerContent">
													<h4{if $file->isDefault} class="default"{/if}><a href="index.php?page=EntryFile&amp;fileID={@$file->fileID}{@SID_ARG_2ND}">{@$file->title}</a></h4>
													<p class="firstPost smallFont light">{lang}wsif.entry.file.by{/lang} {if $file->userID}<a href="index.php?page=User&amp;userID={@$file->userID}{@SID_ARG_2ND}">{$file->username}</a>{else}{$file->username}{/if} ({@$file->time|time})</p>
												</div>
											</li>
										{/foreach}
									</ul>
									<div class="buttonBar">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
												<li><a href="index.php?page=EntryFiles&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.allFiles{/lang}"><img src="{icon}entryFileS.png{/icon}" alt="" /> <span>{lang}wsif.entry.allFiles{/lang}</span></a></li>
											</ul>
										</div>
									</div>
								</div>
							{/if}

							{if $additionalContent4|isset}{@$additionalContent4}{/if}
						</div>
					</div>
				</div>

				<div class="container-3 column second">
					<div class="columnInner">

						{if $entry->defaultImageID}
							<div class="contentBox">
								<div class="border">
									<div class="containerHead">
										<h3>{lang}wsif.entry.image.default{/lang}</h3>
									</div>
									<div class="container-1">
										<div class="entryThumbnail">
											<a href="index.php?page=EntryImage&amp;imageID={@$entry->getImage()->imageID}{@SID_ARG_2ND}" class="enlargable" title="{$entry->getImage()->title}"><img src="index.php?page=EntryImageShow&amp;imageID={@$entry->getImage()->imageID}{if $entry->getImage()->hasThumbnail}&amp;thumbnail=1{/if}{@SID_ARG_2ND}" alt="{$entry->getImage()->title}" /></a>
										</div>
									</div>
								</div>
							</div>
						{/if}

						<div class="contentBox">
							<div class="border">
								<div class="containerHead">
									<h3>{lang}wsif.entry.general{/lang}</h3>
								</div>

								<ul class="dataList">
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}profileM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.creator{/lang}</h4>
											<p>{if $entry->userID}<a href="index.php?page=User&amp;userID={@$entry->userID}{SID_ARG_2ND}">{$entry->username}</a>{else}{$entry->username}{/if}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}clockM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.time{/lang}</h4>
											<p>{@$entry->time|time}</p>
										</div>
									</li>
									{assign var=languageIcon value=$entry->getLanguageIcon()}
									{if !$languageIcon|empty}
										<li class="{cycle values='container-1,container-2'}">
											<div class="containerContent">
												<h4 class="smallFont">{lang}wsif.entry.language{/lang}</h4>
												<p>{@$languageIcon}</p>
											</div>
										</li>
									{/if}
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}visitsM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.views{/lang}</h4>
											<p>{#$entry->views}{if $entry->getViewsPerDay() > 0} ({lang}wsif.entry.viewsPerDay{/lang}){/if}</p>
										</div>
									</li>
									{if $entry->downloads}
										<li class="{cycle values='container-1,container-2'}">
											<div class="containerContent">
												<h4 class="smallFont">{lang}wsif.entry.downloads{/lang}</h4>
												<p>{#$entry->downloads}{if $entry->getDownloadsPerDay() > 0} ({lang}wsif.entry.downloadsPerDay{/lang}){/if}</p>
											</div>
										</li>
									{/if}
									{if $additionalContent2|isset}{@$additionalContent2}{/if}
								</ul>
							</div>
						</div>

						{if $tags|count > 0}
							<div class="contentBox">
								<div class="border">
									<div class="containerHead">
										<h3>{lang}wcf.tagging.tags.used{/lang}</h3>
									</div>
									<div class="container-1">
										<div class="tagList">
											{implode from=$tags item=tag}<a href="index.php?page=Category&amp;categoryID={@$entry->categoryID}&amp;tagID={@$tag->getID()}{@SID_ARG_2ND}">{$tag->getName()}</a>{/implode}
										</div>
									</div>
								</div>
							</div>
						{/if}

						{if $entryVisitors|count > 0}
							<div class="contentBox">
								<div class="border">
									<div class="containerHead">
										<h3>{lang}wsif.entry.visitors{/lang}</h3>
									</div>

									<ul class="dataList">
										{foreach from=$entryVisitors item=entryVisitor}
											<li class="{cycle values='container-1,container-2'}">
												<div class="containerIcon">
													<a href="index.php?page=User&amp;userID={@$entryVisitor->userID}{@SID_ARG_2ND}" title="{lang username=$entryVisitor->username}wcf.user.viewProfile{/lang}">
														{if $entryVisitor->getAvatar()}
															{assign var=x value=$entryVisitor->getAvatar()->setMaxSize(24, 24)}
															{@$entryVisitor->getAvatar()}
														{else}
															<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 24px; height: 24px" />
														{/if}
													</a>
												</div>
												<div class="containerContent">
													<h4><a href="index.php?page=User&amp;userID={@$entryVisitor->userID}{@SID_ARG_2ND}" title="{lang username=$entryVisitor->username}wcf.user.viewProfile{/lang}">{@$entryVisitor->username}</a></h4>
													<p class="light smallFont">{@$entryVisitor->time|time}</p>
												</div>
											</li>
										{/foreach}
									</ul>
								</div>
							</div>
						{/if}

						<div class="contentBox">
							<div class="border">
								<div class="containerHead">
									<h3>{lang}wsif.entry.share{/lang}</h3>
								</div>

								<ul class="dataList">
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}wysiwyg/linkInsertM.png{/icon}" alt="" onclick="document.getElementById('entryLink').select()" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont" onclick="document.getElementById('entryLink').select()">{lang}wsif.entry.link{/lang}</h4>
											<p><input type="text" class="inputText" id="entryLink" readonly="readonly" onclick="this.select()" value="{PAGE_URL}/index.php?page=Entry&amp;entryID={@$entry->entryID}" /></p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}wysiwyg/insertImageM.png{/icon}" alt="" onclick="document.getElementById('entryEmbed').select()" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont" onclick="document.getElementById('entryEmbed').select()">{lang}wsif.entry.image.embed{/lang}</h4>
											<p><input type="text" class="inputText" id="entryEmbed" readonly="readonly" onclick="this.select()" value="[url={PAGE_URL}/index.php?page=Entry&amp;entryID={@$entryID}]{$entry->subject}[/url]" /></p>
										</div>
									</li>
								</ul>
							</div>
						</div>

						{if $additionalContent3|isset}{@$additionalContent3}{/if}
					</div>
				</div>
			</div>
		</div>
	</div>

	{include file="entryFooter"}
</div>

{include file='footer' sandbox=false}
</body>
</html>