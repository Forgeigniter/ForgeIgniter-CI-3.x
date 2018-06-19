<?php
echo '<?xml version="1.0" encoding="utf-8"?>' . "
";
?>
<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:content="http://purl.org/rss/1.0/modules/content/">

	<channel>

	<title><?php echo $feed_name; ?></title>

	<link><?php echo $feed_url; ?></link>
	<description><?php echo $page_description; ?></description>
	<dc:language><?php echo $page_language; ?></dc:language>
	<dc:creator><?php echo $creator_email; ?></dc:creator>

	<dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>
	<admin:generatorAgent rdf:resource="<?php echo site_url(); ?>" />

	<?php foreach($posts as $entry): ?>

		<item>

			<title><?php echo ($entry['replies'] > 0) ? xml_convert('RE: '.$entry['topicTitle']) : xml_convert($entry['topicTitle']); ?></title>
			<link><?php echo site_url('forums/viewpost/'.$entry['lastPostID']); ?></link>
			<guid><?php echo site_url('forums/viewpost/'.$entry['lastPostID']); ?></guid>
			<description><![CDATA[
			<?php echo (strlen(bbcode($entry['body']) > 200)) ? substr(bbcode($entry['body']), 0, 200) : bbcode($entry['body']); ?>
			]]></description>
			<author><?php echo ($entry['displayName']) ? $entry['displayName'] : $entry['firstName'].' '.$entry['lastName']; ?></author>
			<pubDate><?php echo dateFmt($entry['dateCreated'], 'r');?></pubDate>
		</item>

	<?php endforeach; ?>

</channel></rss>
