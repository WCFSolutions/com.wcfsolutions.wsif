{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	{include file='imageViewer'}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{include file="entryHeader" activeTabMenuItem='entryImages'}

	<div class="border">
		<div class="layout-2">
			<div class="columnContainer">
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{$image->title}</h3>

							<div class="entryImage"><a href="index.php?page=EntryImageShow&amp;imageID={@$image->imageID}{SID_ARG_2ND}" class="enlargable" title="{$image->title}"><img src="index.php?page=EntryImageShow&amp;imageID={@$image->imageID}" style="max-width: {@$image->width}px; max-height: {@$image->height}px" alt="" /></a></div>
							{if $image->description}<p class="entryImageCaption">{@$image->getFormattedDescription()}</p>{/if}

							<div class="buttonBar">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
										{if $entry->defaultImageID != $image->imageID && $entry->isEditable($category) && $image->isEditable($category)}<li><a href="index.php?action=EntryImageSetAsDefault&amp;imageID={@$image->imageID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.setAsDefault{/lang}"><img src="{icon}defaultS.png{/icon}" alt="" /> <span>{lang}wsif.entry.image.setAsDefault{/lang}</span></a></li>{/if}
										{if $image->isEditable($category)}<li><a href="index.php?form=EntryImageEdit&amp;imageID={@$image->imageID}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsif.entry.image.edit{/lang}</span></a></li>{/if}
										{if $image->isDeletable($entry, $category)}<li><a href="index.php?action=EntryImageDelete&amp;imageID={@$image->imageID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsif.entry.image.delete.sure{/lang}')" title="{lang}wsif.entry.image.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wsif.entry.image.delete{/lang}</span></a></li>{/if}
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

						{if $previousImage || $nextImage}
							<div class="contentBox">
								<div class="border">
									<div class="containerHead">
										<h3>{lang}wsif.entry.image.navigation{/lang}</h3>
									</div>

									<ul class="dataList">
										{if $previousImage}
											<li class="{cycle values='container-1,container-2'}">
												<div class="containerIcon">
													<img src="{icon}entryImagePreviousM.png{/icon}" alt="" />
												</div>
												<div class="containerContent">
													<h4>{lang}wsif.entry.image.previous{/lang}: <a href="index.php?page=EntryImage&amp;imageID={@$previousImage->imageID}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.previous{/lang}">{$previousImage->title}</a></h4>
													<p class="light smallFont">{@$previousImage->uploadTime|time}</p>
												</div>
											</li>
										{/if}
										{if $nextImage}
											<li class="{cycle values='container-1,container-2'}">
												<div class="containerIcon">
													<img src="{icon}entryImageNextM.png{/icon}" alt="" />
												</div>
												<div class="containerContent">
													<h4>{lang}wsif.entry.image.next{/lang}: <a href="index.php?page=EntryImage&amp;imageID={@$nextImage->imageID}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.next{/lang}">{$nextImage->title}</a></h4>
													<p class="light smallFont">{@$nextImage->uploadTime|time}</p>
												</div>
											</li>
										{/if}
									</ul>
								</div>
							</div>
						{/if}

						<div class="contentBox">
							<div class="border">
								<div class="containerHead">
									<h3>{lang}wsif.entry.image.general{/lang}</h3>
								</div>

								<ul class="dataList">
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}profileM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.image.uploader{/lang}</h4>
											<p>{if $image->userID}<a href="index.php?page=User&amp;userID={@$image->userID}{SID_ARG_2ND}">{$image->username}</a>{else}{$image->username}{/if}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}clockM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.image.time{/lang}</h4>
											<p>{@$image->uploadTime|time}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}visitsM.png{/icon}" alt="" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.image.views{/lang}</h4>
											<p>{#$image->views}{if $image->getViewsPerDay() > 0} ({lang}wsif.entry.image.viewsPerDay{/lang}){/if}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.image.filename{/lang}</h4>
											<p>{$image->filename}</p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerContent">
											<h4 class="smallFont">{lang}wsif.entry.image.filesize{/lang}</h4>
											<p>{@$image->filesize|filesize}</p>
										</div>
									</li>
									{if $additionalContent2|isset}{@$additionalContent2}{/if}
								</ul>
							</div>
						</div>

						<div class="contentBox">
							<div class="border">
								<div class="containerHead">
									<h3>{lang}wsif.entry.image.share{/lang}</h3>
								</div>

								<ul class="dataList">
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}wysiwyg/linkInsertM.png{/icon}" alt="" onclick="document.getElementById('entryImageLink').select()" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont" onclick="document.getElementById('entryImageLink').select()">{lang}wsif.entry.image.link{/lang}</h4>
											<p><input type="text" class="inputText" id="entryImageLink" readonly="readonly" onclick="this.select()" value="{PAGE_URL}/index.php?page=EntryImage&amp;imageID={@$image->imageID}" /></p>
										</div>
									</li>
									<li class="{cycle values='container-1,container-2'}">
										<div class="containerIcon">
											<img src="{icon}wysiwyg/insertImageM.png{/icon}" alt="" onclick="document.getElementById('entryImageEmbed').select()" />
										</div>
										<div class="containerContent">
											<h4 class="smallFont" onclick="document.getElementById('entryImageEmbed').select()">{lang}wsif.entry.image.embed{/lang}</h4>
											<p><input type="text" class="inputText" id="entryImageEmbed" readonly="readonly" onclick="this.select()" value="[url={PAGE_URL}/index.php?page=EntryImage&amp;imageID={@$imageID}][img]{PAGE_URL}/index.php?page=EntryImageShow&amp;imageID={@$image->imageID}[/img][/url]" /></p>
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