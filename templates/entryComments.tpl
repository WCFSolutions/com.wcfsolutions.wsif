{include file="documentHeader"}
<head>
	<title>{$entry->subject} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	
	{include file='imageViewer'}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">
	{include file="entryHeader" activeTabMenuItem='entryComments'}
	
	<div class="border">
		<div class="layout-1">
			<div class="columnContainer">	
				<div class="container-1 column first">
					<div class="columnInner">
						<div class="contentBox">
							<h3 class="subHeadline">{lang}wsif.entry.comments{/lang} <span>({#$items})</span></h3>
							
							{if $comments|count > 0}
								<div class="contentHeader">
									{assign var=multiplePagesLink value="index.php?page=EntryComments&entryID=$entryID&pageNo=%d"}
									{pages print=true assign=pagesOutput link=$multiplePagesLink|concat:SID_ARG_2ND_NOT_ENCODED}

									{if $entry->isCommentable($category) || $additionalLargeButtons|isset}
										<div class="largeButtons">
											<ul>
												{if $entry->isCommentable($category)}<li><a href="index.php?form=EntryCommentAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.comment.add{/lang}"><img src="{icon}entryCommentAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.comment.add{/lang}</span></a></li>{/if}
												{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
											</ul>
										</div>
									{/if}
								</div>
								
								{if $this->getStyle()->getVariable('messages.color.cycle')}
									{cycle name=messageCycle values='2,1' print=false}
								{else}
									{cycle name=messageCycle values='1' print=false}
								{/if}
								
								{if $this->getStyle()->getVariable('messages.sidebar.color.cycle')}
									{if $this->getStyle()->getVariable('messages.color.cycle')}
										{cycle name=commentCycle values='1,2' print=false}
									{else}
										{cycle name=commentCycle values='3,2' print=false}
									{/if}
								{else}
									{cycle name=commentCycle values='3' print=false}
								{/if}
								
								{capture assign='messageClass'}message{if $this->getStyle()->getVariable('messages.framed')}Framed{/if}{@$this->getStyle()->getVariable('messages.sidebar.alignment')|ucfirst}{if $this->getStyle()->getVariable('messages.sidebar.divider.use')} dividers{/if}{/capture}
								{capture assign='messageFooterClass'}messageFooter{@$this->getStyle()->getVariable('messages.footer.alignment')|ucfirst}{/capture}
								
								{assign var='messageNumber' value=$items-$startIndex+1}
								{foreach from=$comments item=comment}
									{assign var="sidebar" value=$sidebarFactory->get('entryComment', $comment->commentID)}
									{assign var="author" value=$sidebar->getUser()}
									{assign var="messageID" value=$comment->commentID}
								
									<div id="commentRow{@$comment->commentID}" class="message">
										<div class="messageInner {@$messageClass} container-{cycle name=commentCycle}">
											<a id="comment{@$comment->commentID}"></a>
											
											{include file='messageSidebar'}
											
											<div class="messageContent">
												<div class="messageContentInner color-{cycle name=messageCycle}">
													<div class="messageHeader">
														<p class="messageCount">
															<a href="index.php?page=EntryComments&amp;commentID={@$comment->commentID}#comment{@$comment->commentID}" title="{lang}wsif.entry.comment.permalink{/lang}" class="messageNumber">{#$messageNumber}</a>
														</p>
														<div class="containerIcon">
															<img src="{icon}entryCommentM.png{/icon}" alt="" />
														</div>
														<div class="containerContent">
															<p class="smallFont light">{@$comment->time|time}</p>
														</div>
													</div>
													
													<h3 id="entryCommentSubject{@$comment->commentID}" class="messageTitle"><span>{$comment->subject}</span></h3>
													
													<div class="messageBody">
														<div id="entryCommentText{@$comment->commentID}">
															{@$comment->getFormattedMessage()}
														</div>
													</div>
													
													{include file='attachmentsShow'}
													
													<div class="{@$messageFooterClass}">
														<div class="smallButtons">
															<ul id="entryCommentButtons{@$comment->commentID}">
																<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
																{if $comment->isEditable($category)}<li><a href="index.php?form=EntryCommentEdit&amp;commentID={@$comment->commentID}{@SID_ARG_2ND}" title="{lang}wsif.entry.comment.edit{/lang}"><img src="{icon}editS.png{/icon}" alt="" /> <span>{lang}wsif.entry.comment.edit{/lang}</span></a></li>{/if}
																{if $comment->isDeletable($category)}<li><a href="index.php?action=EntryCommentDelete&amp;commentID={@$comment->commentID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsif.entry.comment.delete.sure{/lang}')" title="{lang}wsif.entry.comment.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}wsif.entry.comment.delete{/lang}</span></a></li>{/if}
																{if $additionalSmallButtons.$messageID|isset}{@$additionalSmallButtons.$messageID}{/if}
															</ul>
														</div>
													</div>
													<hr />
												</div>
											</div>
											
										</div>
									</div>
									{assign var='messageNumber' value=$messageNumber-1}
								{/foreach}
								
								<div class="contentFooter">
									{@$pagesOutput}
									
									{if $entry->isCommentable($category) || $additionalLargeButtons|isset}
										<div class="largeButtons">
											<ul>
												{if $entry->isCommentable($category)}<li><a href="index.php?form=EntryCommentAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.comment.add{/lang}"><img src="{icon}entryCommentAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.comment.add{/lang}</span></a></li>{/if}
												{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
											</ul>
										</div>
									{/if}
								</div>
							{else}
								<p>{lang}wsif.entry.comment.noComments{/lang}</p>
								
								<div class="contentFooter">									
									{if $entry->isCommentable($category) || $additionalLargeButtons|isset}
										<div class="largeButtons">
											<ul>
												{if $entry->isCommentable($category)}<li><a href="index.php?form=EntryCommentAdd&amp;entryID={@$entryID}{@SID_ARG_2ND}" title="{lang}wsif.entry.comment.add{/lang}"><img src="{icon}entryCommentAddM.png{/icon}" alt="" /> <span>{lang}wsif.entry.comment.add{/lang}</span></a></li>{/if}
												{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
											</ul>
										</div>
									{/if}
								</div>
							{/if}
						</div>
		
						{if $additionalContent1|isset}{@$additionalContent1}{/if}
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