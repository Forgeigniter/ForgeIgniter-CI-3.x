<?php
// get blog headlines
        if (preg_match_all('/{headlines:blog(:category(\(([A-Za-z0-9_-]+)\))?)?(:limit(\(([0-9]+)\))?)?}/i', $body, $matches)) {
            // load blog model
            $this->CI->load->model('blog/blog_model', 'blog');

            // filter through matches
            for ($x = 0; $x < sizeof($matches[0]); $x++) {
                // filter matches
                $headlineID = preg_replace('/{|}/', '', $matches[0][$x]);
                $limit = ($matches[6][$x]) ? $matches[6][$x] : $this->CI->site->config['headlines'];
                $headlines = ($matches[3][$x]) ? $this->CI->blog->get_posts_by_category($matches[3][$x], $limit) : $this->CI->blog->get_posts($limit);

                // get latest posts
                if ($headlines) {
                    // fill up template array
                    $i = 0;
                    foreach ($headlines as $headline) {
                        // get rid of any template tags
                        $headlineBody = $this->parse_body($headline['body'], true, site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']));
                        $headlineExcerpt = $this->parse_body($headline['excerpt'], true, site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']));

                        // populate loop
                        $template[$headlineID][$i] = array(
                            'headline:link' => site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']),
                            'headline:title' => $headline['postTitle'],
                            'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                            'headline:day' => dateFmt($headline['dateCreated'], 'd'),
                            'headline:month' => dateFmt($headline['dateCreated'], 'M'),
                            'headline:year' => dateFmt($headline['dateCreated'], 'y'),
                            'headline:body' => $headlineBody,
                            'headline:excerpt' => $headlineExcerpt,
                            'headline:comments-count' => $headline['numComments'],
                            'headline:author' => $this->CI->blog->lookup_user($headline['userID'], true),
                            'headline:author-id' => $headline['userID'],
                            'headline:class' => ($i % 2) ? ' alt ' : ''
                        );

                        $i++;
                    }
                } else {
                    $template[$headlineID] = array();
                }
            }
        }