{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{include file="entryHeader" activeTabMenuItem='entryImages'}

	<div class="border">
		<div class="layout-1">
			<div class="columnContainer">
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wsif.entry.images{/lang} <span>({#$items})</span></h3>

							{if $images|count > 0}
								<div class="contentHeader">
									{assign var=multiplePagesLink value="index.php?page=EntryImages&entryID=$entryID&pageNo=%d"}
									{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}

									{if $entry->isEditable($category) || $additionalLargeButtons|isset}
										<div class="largeButtons">
											<ul>
												{if $entry->isEditable($category)}<li><a href="index.php?form=EntryImageAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.add{/lang}"><img src="{icon}entryImageAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.image.add{/lang}</span></a></li>{/if}
												{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
											</ul>
										</div>
									{/if}
								</div>

								<ul class="entryImageList floatContainer">
									{foreach from=$images item=image}
										<li class="floatedElement container-4">
											<a href="index.php?page=EntryImage&amp;imageID={@$image->imageID}{@SID_ARG_2ND}" title="{$image->title}">
												<span class="thumbnail" style="width: {if $image->hasThumbnail}{@$image->thumbnailWidth}{else}{@$image->width}{/if}px;"><img src="index.php?page=EntryImageShow&amp;imageID={@$image->imageID}{if $image->hasThumbnail}&amp;thumbnail=1{/if}{@SID_ARG_2ND}" alt="" /></span>
												<span class="caption {if $image->isDefault}default{/if}">{$image->title}</span>
											</a>
											<p class="smallFont light">{lang}wsif.entry.image.by{/lang} {if $image->userID}<a href="index.php?page=User&amp;userID={@$image->userID}{@SID_ARG_2ND}">{$image->username}</a>{else}{$image->username}{/if}</p>
										</li>
									{/foreach}
								</ul>

								<div class="contentFooter">
									{@$pagesOutput}

									{if $entry->isEditable($category) || $additionalLargeButtons|isset}
										<div class="largeButtons">
											<ul>
												{if $entry->isEditable($category)}<li><a href="index.php?form=EntryImageAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.add{/lang}"><img src="{icon}entryImageAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.image.add{/lang}</span></a></li>{/if}
												{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
											</ul>
										</div>
									{/if}
								</div>

								<div class="buttonBar">
									<div class="smallButtons">
										<ul>
											<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
											{if $additionalSmallButtons|isset}{@$additionalSmallButtons}{/if}
										</ul>
									</div>
								</div>
							{else}
								<p>{lang}wsif.entry.image.noImages{/lang}</p>

								<div class="contentFooter">
									{if $entry->isEditable($category) || $additionalLargeButtons|isset}
										<div class="largeButtons">
											<ul>
												{if $entry->isEditable($category)}<li><a href="index.php?form=EntryImageAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.image.add{/lang}"><img src="{icon}entryImageAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.image.add{/lang}</span></a></li>{/if}
												{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
											</ul>
										</div>
									{/if}
								</div>
							{/if}

							{if $additionalContent1|isset}{@$additionalContent1}{/if}
						</div>
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