{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{include file="entryHeader"}
	
	<div class="border">
		<div class="layout-2">
			<div class="columnContainer">	
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wsif.entry{/lang}</h3>
						
							<div class="entryMessage">{@$entry->getFormattedMessage()}</div>
							
							<div class="buttonBar">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if $entry->isEditable($category)}<li><a href="index.php?form=EntryEdit&amp;entryID={@$entry->entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsif.entry.edit{/lang}</span></a></li>{/if}
										{if $category->getPermission('canDownloadEntryFile')}<li><a href="index.php?page=EntryFileDownload&amp;fileID={@$entry->defaultFileID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.download{/lang}"><img src="{icon}entryFileDownloadS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.download{/lang}</span></a></li>{/if}
										{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
									</ul>
								</div>
							</div>
							
							{if $additionalContent1|isset}{@$additionalContent1}{/if}
						</div>
					</div>
				</div>
					
				<div class="container-3 column second">
					<div class="columnInner">
					
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