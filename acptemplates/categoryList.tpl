{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $categories|count > 0 && $categories|count < 100 && $this->user->getPermission('admin.filebase.canEditCategory')}
			new ItemListEditor('categoryList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=CategoryRename&categoryID=', tree: true, treeTag: 'ol' });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WSIF_DIR}icon/categoryL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wsif.acp.category.list{/lang}</h2>
	</div>
</div>

{if $deletedCategoryID}
	<p class="success">{lang}wsif.acp.category.delete.success{/lang}</p>	
{/if}

{if $successfulSorting}
	<p class="success">{lang}wsif.acp.category.sort.success{/lang}</p>	
{/if}

{if $this->user->getPermission('admin.filebase.canAddCategory')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=CategoryAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wsif.acp.category.add{/lang}"><img src="{@RELATIVE_WSIF_DIR}icon/categoryAddM.png" alt="" /> <span>{lang}wsif.acp.category.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $categories|count > 0}
	{if $this->user->getPermission('admin.filebase.canEditCategory')}
	<form method="post" action="index.php?action=CategorySort">
	{/if}
		<div class="border content">
			<div class="container-1">
				<ol class="itemList" id="categoryList">
					{foreach from=$categories item=child}
						{assign var="category" value=$child.category}
						
						<li id="item_{@$category->categoryID}">
							<div class="buttons">
								{if $this->user->getPermission('admin.filebase.canEditCategory')}
									<a href="index.php?form=CategoryEdit&amp;categoryID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wsif.acp.category.edit{/lang}" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wsif.acp.category.edit{/lang}" />
								{/if}
								{if $this->user->getPermission('admin.filebase.canAddCategory')}
									<a href="index.php?form=CategoryAdd&amp;parentID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wsif.acp.category.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}wsif.acp.category.add{/lang}" />
								{/if}								
								{if $this->user->getPermission('admin.filebase.canDeleteCategory')}
									<a href="index.php?action=CategoryDelete&amp;categoryID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" onclick="return confirm('{lang}wsif.acp.category.delete.sure{/lang}')" title="{lang}wsif.acp.category.delete{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" /></a>
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wsif.acp.category.delete{/lang}" />
								{/if}
								
								{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
							</div>
							
							<h3 class="itemListTitle">
								<img src="{@RELATIVE_WSIF_DIR}icon/categoryS.png" alt="" />
								
								{if $this->user->getPermission('admin.filebase.canEditCategory')}
									<select name="categoryListPositions[{@$category->categoryID}][{@$child.parentID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$category->categoryID} <a href="index.php?form=CategoryEdit&amp;categoryID={@$category->categoryID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{@$category->getTitle()}</a>
							</h3>
						
						{if $child.hasChildren}<ol id="parentItem_{@$category->categoryID}">{else}<ol id="parentItem_{@$category->categoryID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
					{/foreach}
				</ol>
			</div>
		</div>
	{if $this->user->getPermission('admin.filebase.canEditCategory')}
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" id="reset" value="{lang}wcf.global.button.reset{/lang}" />
			<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
	 		{@SID_INPUT_TAG}
	 	</div>
	</form>
	{/if}
{else}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wsif.acp.category.count.noCategories{/lang}</p>
		</div>
	</div>
{/if}

{include file='footer'}
