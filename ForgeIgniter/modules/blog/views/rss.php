<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "
";
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:atom="http://www.w3.org/2005/Atom">

    <channel>
    
    <title><?php echo $feed_name; ?></title>

    <link><?php echo $feed_url; ?></link>
    <description><?php echo $page_description; ?></description>
    <dc:language><?php echo $page_language; ?></dc:language>
    <dc:creator><?php echo $creator_email; ?></dc:creator>

    <dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>
    <admin:generatorAgent rdf:resource="<?php echo site_url() ?>" />
    <atom:link href="<?php echo $feed_url; ?>/feed" rel="self" type="application/rss+xml" />

    <?php foreach($posts as $entry): ?>
    
	    <item>

          <title><?php echo xml_convert($entry['postTitle']); ?></title>
          <link><?php echo site_url('blog/'.dateFmt($entry['dateCreated'], 'Y/m').'/'.$entry['uri']); ?></link>
          <guid><?php echo site_url('blog/'.dateFmt($entry['dateCreated'], 'Y/m').'/'.$entry['uri']); ?></guid>

			<description><![CDATA[
				<?php
					echo $this->template->parse_body($entry['body'], TRUE, site_url('blog/'.dateFmt($entry['dateCreated'], 'Y/m').'/'.$entry['uri']));
				?>
			]]></description>
      <pubDate><?php echo dateFmt($entry['dateCreated'], 'r');?></pubDate>
        </item>

        
    <?php endforeach; ?>
    
</channel></rss>