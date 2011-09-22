{if $additionalBoxes|isset}
	<div class="border infoBox">
		{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
	</div>
{/if}

<div class="pageOptions">
	{if $enableRating && $entry->isRatable($category)}
		<span>{lang}wsif.entry.rate{/lang}</span>
		{include file='objectRating'}
		<div id="com.wcfsolutions.wsif.entry-rating{@$entry->entryID}"></div>
		<noscript>
			<form method="post" action="index.php?action=ObjectRating{@SID_ARG_2ND}">
				<div>
					<select id="entryRatingSelect" name="rating">
						{section name=i start=1 loop=6}
							<option value="{@$i}"{if $i == $rating->getUserRating()} selected="selected"{/if}>{@$i}</option>
						{/section}
					</select>
					<input type="hidden" name="objectName" value="com.wcfsolutions.wsif.entry" />
					<input type="hidden" name="objectID" value="{@$entryID}" />
					<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
					<input type="hidden" name="url" value="index.php?page=Entry&amp;entryID={@$entry->entryID}" />
					<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
				</div>
			</form>
		</noscript>
		<script type="text/javascript">
			//<![CDATA[
			objectRatingObj.initializeObject({
				currentRating: {@$rating->getUserRating()},
				objectID: {@$entryID},
				objectName: 'com.wcfsolutions.wsif.entry',
				packageID: {@PACKAGE_ID}
			});
			//]]>
		</script>
	{/if}
</div>

{include file='categoryQuickJump'}