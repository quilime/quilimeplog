<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	>

<channel>
	<title>{$smarty.const.SITE_TITLE}</title>
	<atom:link href="http://{$domain}{$base_dir}/log/" rel="self" type="application/rss+xml" />
	<link>http://{$domain}{$base_dir}</link>
	<description>Project Log for thoughts, writings, projects</description>
	<pubDate>Tue, 07 Oct 2008 09:32:44 +0000</pubDate>

	<generator>http://{$domain}</generator>
	<language>en</language>
	
	{foreach item="post" from=$posts}
	<item>
	    <title>{$post.title}</title>
	    <link>{$post.url}</link>
	    <pubDate>{$post.date_RFC}</pubDate>
	    <tags>{$post.tags_imploded}</tags>
        <guid isPermaLink="false">{$post.url}</guid>
        <content:encoded><![CDATA[
        {$post.content_cleaned}            
        ]]></content:encoded>
	</item>
	{/foreach}
	
	</channel>
</rss>
