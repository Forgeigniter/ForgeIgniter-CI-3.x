<?php

// get gallery
        if (preg_match_all('/{gallery:([A-Za-z0-9_-]+)(:limit\(([0-9]+)\))?}/i', $body, $matches)) {
            // load libs etc
            $this->CI->load->model('images/images_model', 'images');

            // filter through matches
            for ($x = 0; $x < sizeof($matches[0]); $x++) {
                // filter matches
                $headlineID = preg_replace('/{|}/', '', $matches[0][0]);
                $limit = ($matches[3][$x]) ? $matches[3][$x] : 9;

                // get latest posts
                if ($gallery = $this->CI->images->get_images_by_folder_ref($matches[1][$x], $limit)) {
                    // fill up template array
                    $i = 0;
                    foreach ($gallery as $galleryimage) {
                        if ($imageData = $this->get_image($galleryimage['imageRef'])) {
                            $imageHTML = display_image($imageData['src'], $imageData['imageName']);
                            $imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);

                            $thumbTMLL = display_image($imageData['src'], $imageData['imageName']);
                            $thumbHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);

                            $template[$headlineID][$i] = array(
                                'galleryimage:link' => site_url('images/'.$imageData['imageRef'].$imageData['ext']),
                                'galleryimage:title' => $imageData['imageName'],
                                'galleryimage:image' => $imageHTML,
                                'galleryimage:thumb' => $thumbHTML,
                                'galleryimage:filename' => $imageData['imageRef'].$imageData['ext'],
                                'galleryimage:date' => dateFmt($imageData['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                                'galleryimage:author' => $this->CI->images->lookup_user($imageData['userID'], true),
                                'galleryimage:author-id' => $imageData['userID'],
                                'galleryimage:class' => $imageData['class']
                            );

                            $i++;
                        }
                    }
                } else {
                    $template[$headlineID] = array();
                }
            }
        }