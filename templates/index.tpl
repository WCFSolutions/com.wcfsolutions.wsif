{include file="documentHeader"}
<head>
	<title>{lang}wsif.index.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>

	{include file='headInclude' sandbox=false}
	<link rel="alternate" type="application/rss+xml" href="index.php?page=EntriesFeed&amp;format=rss2" title="{lang}wsif.index.feed{/lang} (RSS2)" />
	<link rel="alternate" type="application/atom+xml" href="index.php?page=EntriesFeed&amp;format=atom" title="{lang}wsif.index.feed{/lang} (Atom)" />
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">


	<div class="mainHeadline">
		<img src="{icon}indexL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}{PAGE_TITLE}{/lang}</h2>
			<p>{lang}{PAGE_DESCRIPTION}{/lang}</p>
		</div>
	</div>

	{if $userMessages|isset}{@$userMessages}{/if}

	{*{include file='newestEntries'}*}

	{if $additionalTopContents|isset}{@$additionalTopContents}{/if}

	{include file='categoryList'}

	{if $additionalBottomContents|isset}{@$additionalBottomContents}{/if}

	{if INDEX_ENABLE_STATS || $tags|count || $additionalBoxes|isset}
		{cycle values='container-1,container-2' print=false advance=false}
		<div class="border infoBox">
			{if INDEX_ENABLE_STATS}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}statisticsM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3>{lang}wsif.index.stats{/lang}</h3>
						<p class="smallFont">{lang}wsif.index.stats.detail{/lang}</p>
					</div>
				</div>
			{/if}

			{if $tags|count}
				<div class="{cycle}">
					<div class="containerIcon"><img src="{icon}tagM.png{/icon}" alt="" /></div>
					<div class="containerContent">
						<h3><a href="index.php?page=TaggedObjects{@SID_ARG_2ND}">{lang}wcf.tagging.mostPopular{/lang}</a></h3>
						{include file='tagCloud'}
					</div>
				</div>
			{/if}

			{if $additionalBoxes|isset}{@$additionalBoxes}{/if}
		</div>
	{/if}

</div>

{include file='footer' sandbox=false}

</body>
</html>