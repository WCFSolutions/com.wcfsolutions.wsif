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
		<div class="layout-1">
			<div class="columnContainer">	
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wsif.entry.files{/lang} <span>({#$items})</span></h3>
								
							<div class="contentHeader">
								{assign var=multiplePagesLink value="index.php?page=EntryFiles&entryID=$entryID&pageNo=%d"}
								{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}

								{if $entry->isEditable($category) || $additionalLargeButtons|isset}
									<div class="largeButtons">
										<ul>
											{if $entry->isEditable($category)}<li><a href="index.php?form=EntryFileAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.add{/lang}"><img src="{icon}entryFileAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.add{/lang}</span></a></li>{/if}
											{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
										</ul>
									</div>
								{/if}
							</div>
								
							<div class="border">
								<table class="tableList">
									<thead>
										<tr class="tableHead">
											<th colspan="2" class="columnTitle"><div><span class="emptyHead">{lang}wsif.entry.files.title{/lang}</span></div></th>
											<th class="columnDownloads"><div><span class="emptyHead">{lang}wsif.entry.files.downloads{/lang}</span></div></th>
											<th class="columnViews"><div><span class="emptyHead">{lang}wsif.entry.files.views{/lang}</span></div></th>
										</tr>
									</thead>
									<tbody>
							
										{foreach from=$files item=file}
											{assign var=fileID value=$file->fileID}
										
											<tr class="{cycle values='container-1,container-2'}">
												<td class="columnIcon">
													<img src="{icon}entryFileM.png{/icon}" alt="" />
												</td>
												<td class="columnTitle">
													<div{if $file->isDefault} class="default"{/if}><a href="index.php?page=EntryFile&amp;fileID={@$file->fileID}{@SID_ARG_2ND}">{@$file->title}</a></div>
													<p class="light smallFont">
														{lang}wsif.entry.file.by{/lang}
														{if $file->userID}
															<a href="index.php?page=User&amp;userID={@$file->userID}{@SID_ARG_2ND}">{$file->username}</a>
														{else}
															{$file->username}
														{/if}
														({@$file->uploadTime|shorttime})
													</p>												
												</td>
												<td class="columnDownloads">{#$file->downloads}</td>
												<td class="columnViews">{#$file->views}</td>
											</tr>
										{/foreach}
								
									</tbody>
								</table>
							</div>
							
							<div class="contentFooter">
								{@$pagesOutput}
									
								{if $entry->isEditable($category) || $additionalLargeButtons|isset}
									<div class="largeButtons">
										<ul>
											{if $entry->isEditable($category)}<li><a href="index.php?form=EntryFileAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.file.add{/lang}"><img src="{icon}entryFileAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.file.add{/lang}</span></a></li>{/if}
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