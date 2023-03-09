<?php
        // get events headlines
        if (preg_match_all('/{headlines:events(:limit(\(([0-9]+)\))?)?}/i', $body, $matches)) {
            // load events model
            $this->CI->load->model('events/events_model', 'events');

            // filter matches
            $headlineID = preg_replace('/{|}/', '', $matches[0][0]);
            $limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];

            // get latest posts
            if ($headlines = $this->CI->events->get_events($limit)) {
                // fill up template array
                $i = 0;
                foreach ($headlines as $headline) {
                    $headlineBody = $this->parse_body($headline['description'], true, site_url('events/viewevent/'.$headline['eventID']));
                    $headlineExcerpt = $this->parse_body($headline['excerpt'], true, site_url('events/viewevent/'.$headline['eventID']));

                    $template[$headlineID][$i] = array(
                        'headline:link' => site_url('events/viewevent/'.$headline['eventID']),
                        'headline:title' => $headline['eventTitle'],
                        'headline:date' => dateFmt($headline['eventDate'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                        'headline:day' => dateFmt($headline['eventDate'], 'd'),
                        'headline:month' => dateFmt($headline['eventDate'], 'M'),
                        'headline:year' => dateFmt($headline['eventDate'], 'y'),
                        'headline:body' => $headlineBody,
                        'headline:excerpt' => $headlineExcerpt,
                        'headline:author' => $this->CI->events->lookup_user($headline['userID'], true),
                        'headline:author-id' => $headline['userID'],
                        'headline:class' => ($i % 2) ? ' alt ' : ''
                    );

                    $i++;
                }
            } else {
                $template[$headlineID] = array();
            }
        }