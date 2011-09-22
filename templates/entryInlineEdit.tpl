<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/InlineListEdit.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WSIF_DIR}js/EntryListEdit.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	// entry data
	var entryData = new Hash();
	
	// entry prefix data
	var entryPrefixData = new Hash();
	{if $category|isset}
		{foreach from=$category->getPrefixes() item=prefix}
			entryPrefixData.set({@$prefix->prefixID}, {
				'prefixName': '{@$prefix->getPrefixName()|encodeJS}',
				'styledPrefixName': '{@$prefix->getStyledPrefix()|encodeJS}'
			});
		{/foreach}
	{/if}

	// language
	language['wcf.global.button.mark']				= '{lang}wcf.global.button.mark{/lang}';
	language['wcf.global.button.unmark'] 				= '{lang}wcf.global.button.unmark{/lang}';
	language['wcf.global.button.delete'] 				= '{lang}wcf.global.button.delete{/lang}';
	language['wcf.global.button.deleteCompletely'] 			= '{lang}wcf.global.button.deleteCompletely{/lang}';
	language['wcf.global.button.submit']				= '{lang}wcf.global.button.submit{/lang}';
	language['wcf.global.button.reset']				= '{lang}wcf.global.button.reset{/lang}';
	language['wsif.category.entries.recover'] 			= '{lang}wsif.category.entries.recover{/lang}';
	language['wsif.category.entries.enable'] 			= '{lang}wsif.category.entries.enable{/lang}';
	language['wsif.category.entries.disable'] 			= '{lang}wsif.category.entries.disable{/lang}';
	language['wsif.category.entries.editTitle'] 			= '{lang}wsif.category.entries.editTitle{/lang}';
	language['wsif.category.entries.editPrefix'] 			= '{lang}wsif.category.entries.editPrefix{/lang}';
	language['wsif.category.entries.delete.sure'] 			= '{lang}wsif.category.entries.delete.sure{/lang}';
	language['wsif.category.entries.deleteCompletely.sure'] 	= '{lang}wsif.category.entries.deleteCompletely.sure{/lang}';
	language['wsif.category.entries.delete.reason'] 		= '{lang}wsif.category.entries.delete.reason{/lang}';
	language['wsif.category.entries.markedEntries'] 		= '{lang}wsif.category.entries.markedEntries{/lang}';
	language['wsif.category.entries.deleteMarked.sure'] 		= '{lang}wsif.category.entries.deleteMarked.sure{/lang}';
	language['wsif.category.entries.deleteMarked.reason'] 		= '{lang}wsif.category.entries.deleteMarked.reason{/lang}';
	language['wsif.category.entries.move'] 				= '{lang}wsif.category.entries.move{/lang}';
	language['wsif.category.entries.showMarked'] 			= '{lang}wsif.category.entries.showMarked{/lang}';
	
	// permissions
	var permissions = new Object();
	permissions['canDeleteEntry'] = {@$permissions.canDeleteEntry};
	permissions['canViewDeletedEntry'] = {@$permissions.canViewDeletedEntry};
	permissions['canDeleteEntryCompletely'] = {@$permissions.canDeleteEntryCompletely};
	permissions['canEnableEntry'] = {@$permissions.canEnableEntry};
	permissions['canMarkEntry'] = {@$permissions.canMarkEntry};
	permissions['canMoveEntry'] = {@$permissions.canMoveEntry};
	permissions['canEditEntry'] = {@$permissions.canEditEntry};

	// init
	document.observe("dom:loaded", function() {
		entryListEdit = new EntryListEdit(entryData, entryPrefixData, {@$markedEntries}, {
			page:			'{@$pageType}',
			url:			'{@$url}',
			categoryID:		{if $category|isset}{@$category->categoryID}{else}0{/if},
			entryID:		{if $entry|isset}{@$entry->entryID}{else}0{/if},
			enableRecycleBin:	{@ENTRY_ENABLE_RECYCLE_BIN}
		});
	});
	//]]>
</script>