{if $additionalBoxes|isset}
	<div class="border infoBox">
		{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
	</div>
{/if}

<div class="pageOptions">
	{if $enableRating && $entry->isRatable($category)}
		{if $entry->userRating === null || $entry->userRating > 0}
			<script type="text/javascript" src="{@RELATIVE_WSIF_DIR}js/EntryRating.class.js"></script>
			<form method="post" action="index.php?page=Entry">
				<div>
					<input type="hidden" name="entryID" value="{@$entry->entryID}" />
					{@SID_INPUT_TAG}
					<input type="hidden" id="entryRating" name="rating" value="0" />
						
					<span class="hidden" id="entryRatingSpan"></span>
						
					<span>{lang}wsif.entry.rate{/lang}</span>
						
					<noscript>
						<div>
							<select id="entryRatingSelect" name="rating">
								<option value="1"{if $entry->userRating == 1} selected="selected"{/if}>1</option>
								<option value="2"{if $entry->userRating == 2} selected="selected"{/if}>2</option>
								<option value="3"{if $entry->userRating == 3} selected="selected"{/if}>3</option>
								<option value="4"{if $entry->userRating == 4} selected="selected"{/if}>4</option>
								<option value="5"{if $entry->userRating == 5} selected="selected"{/if}>5</option>
							</select>
							<input type="image" class="inputImage" src="{icon}submitS.png{/icon}" alt="{lang}wcf.global.button.submit{/lang}" />
						</div>
					</noscript>
				</div>
			</form>
				
			<script type="text/javascript">
				//<![CDATA[
				document.observe("dom:loaded", function() {
					new EntryRating('entryRating', {
						currentRating:		{@$entry->userRating|intval},
						iconRating:		'{icon}ratingS.png{/icon}',
						iconNoRating:		'{icon}noRatingS.png{/icon}'
					});
				});
				//]]>
			</script>
		{/if}
	{/if}
</div>

{include file='categoryQuickJump'}