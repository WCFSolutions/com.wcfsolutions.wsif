{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $prefixes|count > 0 && $this->user->getPermission('admin.filebase.canEditEntryPrefix')}
			new ItemListEditor('entryPrefixList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=EntryPrefixRename&prefixID=' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIF_DIR}icon/entryPrefixL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsif.acp.entry.prefix.view{/lang}</h2>
	</div>
</div>

{if $deletedPrefixID}
	<p class="success">{lang}wsif.acp.entry.prefix.delete.success{/lang}</p>	
{/if}

{if $successfullSorting}
	<p class="success">{lang}wsif.acp.entry.prefix.sort.success{/lang}</p>	
{/if}

<div class="contentHeader">
	{if $this->user->getPermission('admin.filebase.canAddEntryPrefix')}
		<div class="largeButtons">
			<ul><li><a href="index.php?form=EntryPrefixAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WSIF_DIR}icon/entryPrefixAddM.png" alt="" title="{lang}wsif.acp.entry.prefix.add{/lang}" /> <span>{lang}wsif.acp.entry.prefix.add{/lang}</span></a></li></ul>
		</div>
	{/if}
</div>

{if $prefixes|count}
	<form method="post" action="index.php?action=EntryPrefixSort">
		<div class="border content">
			<div class="container-1">		
				<ol id="entryPrefixList" class="itemList">
					{foreach from=$prefixes item=prefix}
						<li id="item_{@$prefix->prefixID}">
							<div class="buttons">
								{if $this->user->getPermission('admin.filebase.canEditEntryPrefix')}									
									<a href="index.php?form=EntryPrefixEdit&amp;prefixID={@$prefix->prefixID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wsif.acp.entry.prefix.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wsif.acp.entry.prefix.editDisabled{/lang}" />
								{/if}
								
								{if $this->user->getPermission('admin.filebase.canDeleteEntryPrefix')}
									<a href="index.php?action=EntryPrefixDelete&amp;prefixID={@$prefix->prefixID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsif.acp.entry.prefix.delete.sure{/lang}')" title="{lang}wsif.acp.entry.prefix.delete{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wsif.acp.entry.prefix.deleteDisabled{/lang}" />
								{/if}
							</div>
							
							<h3 class="itemListTitle">
								{if $this->user->getPermission('admin.filebase.canEditEntryPrefix')}
									<select name="entryPrefixListPositions[{$prefix->prefixID}]">
										{section name='prefixPositions' loop=$prefixes|count}
											<option value="{@$prefixPositions+1}"{if $prefixPositions+1 == $prefix->showOrder} selected="selected"{/if}>{@$prefixPositions+1}</option>
										{/section}
									</select>	
								{/if}
																	
								ID-{@$prefix->prefixID}
								{if $this->user->getPermission('admin.filebase.canEditEntryPrefix')}
									<a href="index.php?form=EntryPrefixEdit&amp;prefixID={@$prefix->prefixID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}wsif.entry.prefix.{$prefix->prefix}{/lang}</a>
								{else}
									{lang}wsif.entry.prefix.{$prefix->prefix}{/lang}
								{/if}
							</h3>
						</li>
					{/foreach}
				</ol>
			</div>
		</div>
		
		{if $this->user->getPermission('admin.filebase.canEditEntryPrefix')}
			<div class="formSubmit">
				<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
				<input type="reset" id="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
				<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
				{@SID_INPUT_TAG}
			</div>
		{/if}
	</form>
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wsif.acp.entry.prefix.view.count.noPrefixes{/lang}</p>
		</div>
	</div>
{/if}

{include file='footer'}