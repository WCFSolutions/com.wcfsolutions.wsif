<?xml version="1.0" encoding="{@CHARSET}"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title>{lang}wsif.feed.title{/lang}</title>
	<id>{@PAGE_URL}/</id>
	<updated>{@'c'|gmdate:TIME_NOW}</updated>
	<link href="{@PAGE_URL}/" />
	<generator uri="http://www.wcfsolutions.com/" version="{@PACKAGE_VERSION}">
		WCF Solutions Infinite Filebase
	</generator>
	<subtitle>{lang}wsif.feed.description{/lang}</subtitle>
	
	{foreach from=$entries item=entry}
		<entry>
			<title>{$entry->subject}</title>
			<id>{@PAGE_URL}/index.php?page=Entry&amp;entryID={@$entry->entryID}</id>
			<updated>{@'c'|gmdate:$entry->time}</updated>
			<author>
				<name>{$entry->username}</name>
			</author>
			<content type="html"><![CDATA[{@$entry->getFormattedMessage()}]]></content>
			<link href="{@PAGE_URL}/index.php?page=Entry&amp;entryID={@$entry->entryID}" />
		</entry>
	{/foreach}
</feed>