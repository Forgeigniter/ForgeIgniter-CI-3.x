<?php
        // get shop gateway
        if (preg_match('/{shop:(.+)}|{headlines:shop/i', $body)) {
            // load messages model
            $this->CI->load->model('shop/shop_model', 'shop');

            // shop globals
            $template['shop:email'] = $this->CI->site->config['shopEmail'];
            $template['shop:paypal'] = $this->CI->shop->paypal_url;
            $template['shop:gateway'] = ($this->CI->site->config['shopGateway'] == 'sagepay' || $this->CI->site->config['shopGateway'] == 'authorize') ? site_url('/shop/checkout') : $this->CI->shop->gateway_url;

            // get shop headlines
            if (preg_match_all('/{headlines:shop(:category\(([A-Za-z0-9_-]+)\))?(:limit\(([0-9]+)\))?}/i', $body, $matches)) {
                // filter matches
                $headlineID = preg_replace('/{|}/', '', $matches[0][0]);
                $limit = ($matches[4][0]) ? $matches[4][0] : $this->CI->site->config['headlines'];
                $catSafe = $matches[2][0];

                // get latest posts
                if ($headlines = $this->CI->shop->get_latest_products($catSafe, $limit)) {
                    // fill up template array
                    $i = 0;
                    foreach ($headlines as $headline) {
                        // get body and excerpt
                        $headlineBody = (strlen($headline['description']) > 100) ? substr($headline['description'], 0, 100).'...' : $headline['description'];
                        $headlineExcerpt = nl2br($headline['excerpt']);

                        // get images
                        if (!$headlineImage = $this->CI->uploads->load_image($headline['productID'], false, true)) {
                            $headlineImage['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
                        }

                        // get images
                        if (!$headlineThumb = $this->CI->uploads->load_image($headline['productID'], true, true)) {
                            $headlineThumb['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
                        }

                        // populate template
                        $template[$headlineID][$i] = array(
                            'headline:id' => $headline['productID'],
                            'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
                            'headline:title' => $headline['productName'],
                            'headline:subtitle' => $headline['subtitle'],
                            'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                            'headline:body' => $headlineBody,
                            'headline:excerpt' => $headlineExcerpt,
                            'headline:price' => currency_symbol().number_format($headline['price'], 2),
                            'headline:image-path' => $headlineImage['src'],
                            'headline:thumb-path' => $headlineThumb['src'],
                            'headline:cell-width' => floor((1 / $limit) * 100),
                            'headline:price' => currency_symbol().number_format($headline['price'], 2),
                            'headline:stock' => $headline['stock'],
                            'headline:class' => ($i % 2) ? ' alt ' : ''
                        );

                        $i++;
                    }
                } else {
                    $template[$headlineID] = array();
                }
            }

            // get shop headlines
            if (preg_match_all('/{headlines:shop:featured(:limit(\(([0-9]+)\))?)?}/i', $body, $matches)) {
                // filter matches
                $headlineID = preg_replace('/{|}/', '', $matches[0][0]);
                $limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];

                // get latest posts
                if ($headlines = $this->CI->shop->get_latest_featured_products($limit)) {
                    // fill up template array
                    $i = 0;
                    foreach ($headlines as $headline) {
                        // get body and excerpt
                        $headlineBody = (strlen($headline['description']) > 100) ? substr($headline['description'], 0, 100).'...' : $headline['description'];
                        $headlineExcerpt = nl2br($headline['excerpt']);

                        // get images
                        if (!$headlineImage = $this->CI->uploads->load_image($headline['productID'], false, true)) {
                            $headlineImage['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
                        }

                        // get thumb
                        if (!$headlineThumb = $this->CI->uploads->load_image($headline['productID'], true, true)) {
                            $headlineThumb['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
                        }

                        $template[$headlineID][$i] = array(
                            'headline:id' => $headline['productID'],
                            'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
                            'headline:title' => $headline['productName'],
                            'headline:subtitle' => $headline['subtitle'],
                            'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                            'headline:body' => $headlineBody,
                            'headline:excerpt' => $headlineExcerpt,
                            'headline:price' => currency_symbol().number_format($headline['price'], 2),
                            'headline:image-path' => $headlineImage['src'],
                            'headline:thumb-path' => $headlineThumb['src'],
                            'headline:cell-width' => floor((1 / $limit) * 100),
                            'headline:price' => currency_symbol().number_format($headline['price'], 2),
                            'headline:stock' => $headline['stock'],
                            'headline:class' => ($i % 2) ? ' alt ' : ''
                        );

                        $i++;
                    }
                } else {
                    $template[$headlineID] = array();
                }
            }

            // get shop cart headlines
            if (preg_match('/({headlines:shop:((.+)?)})+/i', $body)) {
                // get shopping cart
                $cart = $this->CI->shop->load_cart();

                // get latest posts
                if ($headlines = $cart['cart']) {
                    // fill up template array
                    $i = 0;
                    foreach ($headlines as $headline) {
                        $template['headlines:shop:cartitems'][$i] = array(
                            'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
                            'headline:title' => $headline['productName'],
                            'headline:quantity' => $headline['quantity'],
                            'headline:price' => currency_symbol().(number_format($headline['price'] * $headline['quantity'], 2)),
                            'headline:class' => ($i % 2) ? ' alt ' : ''
                        );

                        $i++;
                    }
                    $template['headlines:shop:numitems'] = count($headlines);
                    $template['headlines:shop:subtotal'] = currency_symbol().number_format($cart['subtotal'], 2);
                } else {
                    $template['headlines:shop:numitems'] = 0;
                    $template['headlines:shop:subtotal'] = currency_symbol().number_format(0, 2);
                    $template['headlines:shop:cartitems'] = array();
                }
            }

            // get shop navigation
            if (preg_match('/({shop:categories((.+)?)})+/i', $body)) {
                $template['shop:categories'] = '';

                if ($categories = $this->CI->shop->get_category_parents()) {
                    $i = 1;
                    foreach ($categories as $nav) {
                        // get subnav
                        if ($children = $this->CI->shop->get_category_children($nav['catID'])) {
                            $template['shop:categories'] .= '<li class="expanded ';
                            if ($i == 1) {
                                $template['shop:categories'] .= 'first ';
                            }
                            if ($i == sizeof($categories)) {
                                $template['shop:categories'] .= 'last ';
                            }
                            $template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'">'.htmlentities($nav['catName'], null, 'UTF-8').'</a><ul class="subnav">';

                            foreach ($children as $child) {
                                $template['shop:categories'] .= '<li class="';
                                if ($child['catID'] == $this->CI->uri->segment(3) || $nav['catSafe'] == $this->CI->uri->segment(2)) {
                                    $template['shop:categories'] .= 'active selected';
                                }
                                $template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'/'.$child['catSafe'].'">'.htmlentities($child['catName'], null, 'UTF-8').'</a></li>';
                            }
                            $template['shop:categories'] .= '</ul>';
                        } else {
                            $template['shop:categories'] .= '<li class="';
                            if ($nav['catID'] == $this->CI->uri->segment(3) || $nav['catSafe'] == $this->CI->uri->segment(2)) {
                                $template['shop:categories'] .= 'active selected ';
                            }
                            if ($i == 1) {
                                $template['shop:categories'] .= 'first ';
                            }
                            if ($i == sizeof($categories)) {
                                $template['shop:categories'] .= 'last ';
                            }
                            $template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'">'.htmlentities($nav['catName'], null, 'UTF-8').'</a>';
                        }

                        $template['shop:categories'] .= '</li>';
                        $i++;
                    }
                }
            }
        }