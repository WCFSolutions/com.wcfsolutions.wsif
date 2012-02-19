{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{include file="entryHeader" activeTabMenuItem='entryFiles'}

	<div class="border">
		<div class="layout-2">
			<div class="columnContainer">
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{$file->title}</h3>

							{if $file->description}<p class="entryFileDescription">{@$file->getFormattedDescription()}</p>{/if}

							<div class="buttonBar">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if $entry->defaultFileID != $file->fileID && $entry->isEditable($category) && $file->isEditable($category)}<li><a href="index.php?action=EntryFileSetAsDefault&amp;fileID={@$file->fileID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.setAsDefault{/lang}"><img src="{icon}defaultS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.setAsDefault{/lang}</span></a></li>{/if}
										{if $file->isEditable($category)}<li><a href="index.php?form=EntryFileEdit&amp;fileID={@$file->fileID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.edit{/lang}</span></a></li>{/if}
										{if $file->isDeletable($entry, $category)}<li><a href="index.php?action=EntryFileDelete&amp;fileID={@$file->fileID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsif.entry.file.delete.sure{/lang}')" title="{lang}wsif.entry.file.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.delete{/lang}</span></a></li>{/if}
										{if $category->getPermission('canDownloadEntryFile')}<li><a href="index.php?page=EntryFileDownload&amp;fileID={@$file->fileID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.download{/lang}"><img src="{icon}entryFileDownloadS.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.download{/lang}</span></a></li>{/if}
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
									<h3>{lang}wsif.entry.file.general{/lang}</h3>
								</div>

								<ul class="dataList">
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}profileM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.file.uploader{/lang}</h4>
											<p>{if $file->userID}<a href="index.php?page=User&amp;userID={@$file->userID}{SID_ARG_2ND}">{$file->username}</a>{else}{$file->username}{/if}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}clockM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.file.time{/lang}</h4>
											<p>{@$file->uploadTime|time}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}visitsM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.file.views{/lang}</h4>
											<p>{#$file->views}{if $file->getViewsPerDay() > 0} ({lang}wsif.entry.file.viewsPerDay{/lang}){/if}</p>
										</div>
									</li>
									{if $file->isUpload()}
										<li class="{cycle values='container-1,container-2'}">
											<div class="containerContent">
												<h4 class="smallFont">{lang}wsif.entry.file.filename{/lang}</h4>
												<p>{$file->filename}</p>
											</div>
										</li>
										<li class="{cycle values='container-1,container-2'}">
											<div class="containerContent">
												<h4 class="smallFont">{lang}wsif.entry.file.filesize{/lang}</h4>
												<p>{@$file->filesize|filesize}</p>
											</div>
										</li>
									{/if}
									{if $file->downloads}
										<li class="{cycle values='container-1,container-2'}">
											<div class="containerContent">
												<h4 class="smallFont">{lang}wsif.entry.file.downloads{/lang}</h4>
												<p>{#$file->downloads}{if $file->getDownloadsPerDay() > 0} ({lang}wsif.entry.file.downloadsPerDay{/lang}){/if}</p>
											</div>
										</li>
									{/if}
									{if $file->lastDownloadTime}
										<li class="{cycle values='container-1,container-2'}">
											<div class="containerContent">
												<h4 class="smallFont">{lang}wsif.entry.file.lastDownloadTime{/lang}</h4>
												<p>{@$file->lastDownloadTime|time}</p>
											</div>
										</li>
									{/if}
									{if $additionalContent2|isset}{@$additionalContent2}{/if}
								</ul>
							</div>
						</div>

						{if $fileDownloaders|count > 0}
							<div class="contentBox">
								<div class="border">
									<div class="containerHead">
										<h3>{lang}wsif.entry.file.downloaders{/lang}</h3>
									</div>

									<ul class="dataList">
										{foreach from=$fileDownloaders item=fileDownloader}
											<li class="{cycle values='container-1,container-2'}">
												<div class="containerIcon">
													<a href="index.php?page=User&amp;userID={@$fileDownloader->userID}{@SID_ARG_2ND}" title="{lang username=$fileDownloader->username}wcf.user.viewProfile{/lang}">
														{if $fileDownloader->getAvatar()}
															{assign var=x value=$fileDownloader->getAvatar()->setMaxSize(24, 24)}
															{@$fileDownloader->getAvatar()}
														{else}
															<img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 24px; height: 24px" />
														{/if}
													</a>
												</div>
												<div class="containerContent">
													<h4><a href="index.php?page=User&amp;userID={@$fileDownloader->userID}{@SID_ARG_2ND}" title="{lang username=$fileDownloader->username}wcf.user.viewProfile{/lang}">{@$fileDownloader->username}</a></h4>
													<p class="light smallFont">{@$fileDownloader->time|time}</p>
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
									<h3>{lang}wsif.entry.file.share{/lang}</h3>
								</div>

								<ul class="dataList">
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}wysiwyg/linkInsertM.png{/icon}" alt="" onclick="document.getElementById('entryFileLink').select()" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont" onclick="document.getElementById('entryFileLink').select()">{lang}wsif.entry.file.link{/lang}</h4>
											<p><input type="text" class="inputText" id="entryFileLink" readonly="readonly" onclick="this.select()" value="{PAGE_URL}/index.php?page=EntryFile&amp;fileID={@$file->fileID}" /></p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}wysiwyg/insertImageM.png{/icon}" alt="" onclick="document.getElementById('entryFileEmbed').select()" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont" onclick="document.getElementById('entryFileEmbed').select()">{lang}wsif.entry.image.embed{/lang}</h4>
											<p><input type="text" class="inputText" id="entryFileEmbed" readonly="readonly" onclick="this.select()" value="[url={PAGE_URL}/index.php?page=EntryFile&amp;fileID={@$fileID}]{$file->title}[/url]" /></p>
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

	{include file='entryFooter'}
</div>

{include file='footer' sandbox=false}
</body>
</html>