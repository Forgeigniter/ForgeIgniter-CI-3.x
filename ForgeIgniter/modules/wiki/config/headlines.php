<?php
// get wiki headlines
        if (preg_match_all('/{headlines:wiki(:category(\(([A-Za-z0-9_-]+)\))?)?(:limit(\(([0-9]+)\))?)?}/i', $body, $matches)) {
            // load wiki model
            $this->CI->load->model('wiki/wiki_model', 'wiki');

            // filter matches
            $headlineID = preg_replace('/{|}/', '', $matches[0][0]);
            $limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];

            // get latest posts
            if ($headlines = $this->CI->wiki->get_pages($limit)) {
                // fill up template array
                $i = 0;
                foreach ($headlines as $headline) {
                    $template[$headlineID][$i] = array(
                        'headline:link' => site_url('wiki/' .$headline['uri']),
                        'headline:title' => $headline['pageName'],
                    );

                    $i++;
                }
            } else {
                $template[$headlineID] = array();
            }
        }