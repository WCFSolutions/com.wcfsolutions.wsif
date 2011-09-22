<div class="formElement">
	<div class="formFieldLabel">
		<label for="searchCategories">{lang}wsif.search.categories{/lang}</label>
	</div>
	<div class="formField">
		<select id="searchCategories" name="categoryIDs[]" multiple="multiple" size="10">
			<option value="*"{if $selectAllCategories} selected="selected"{/if}>{lang}wsif.search.categories.all{/lang}</option>
			<option value="-">--------------------</option>
			{htmloptions options=$categoryOptions selected=$categoryIDs disableEncoding=true}
		</select>
	</div>
	<div class="formFieldDesc">
		<p>{lang}wcf.global.multiSelect{/lang}</p>
	</div>
</div>