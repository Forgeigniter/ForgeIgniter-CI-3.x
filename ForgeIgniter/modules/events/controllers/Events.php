<?php
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2015, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Events extends MX_Controller
{
    public $partials = array();

    public function __construct()
    {
        parent::__construct();

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // get site permissions and redirect if it don't have access to this module
        if (!$this->permission->sitePermissions) {
            show_error('You do not have permission to view this page');
        }
        if (!in_array($this->uri->segment(1), $this->permission->sitePermissions)) {
            show_error('You do not have permission to view this page');
        }

        // load models and modules
        $this->load->library('tags');
        $this->load->library(
            'calendar',
            array(
                                                'show_next_prev' => true,
                                                'next_prev_url' => site_url('/events')

                                            )

                            );
        $this->load->model('events_model', 'events');
        $this->load->module('pages');

        // load partials - archive
        if ($archive = $this->events->get_archive()) {
            foreach ($archive as $date) {
                $this->partials['events:archive'][] = array(
                    'archive:link' => site_url('/events/'.$date['year'].'/'.$date['month'].'/'),
                    'archive:title' => $date['dateStr'],
                    'archive:count' => $date['numEvents']
                );
            }
        }

        // load partials - latest
        if ($latest = $this->events->get_headlines()) {
            foreach ($latest as $event) {
                $this->partials['events:latest'][] = array(
                    'latest:link' => site_url('events/viewevent/'.$event['eventID']),
                    'latest:title' => $event['eventTitle'],
                    'latest:date' => date((($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'), strtotime($event['eventDate'])),
                );
            }
        }

        // load partials - calendar
        $month = ($this->uri->segment(3) && intval($this->uri->segment(2))) ? $this->uri->segment(3) : date('m', time());
        $year = ($this->uri->segment(2) && intval($this->uri->segment(2))) ? $this->uri->segment(2) : date('Y', time());
        $monthEvents = array();
        if ($data['month'] = $this->events->get_month($month, $year)) {
            foreach ($data['month'] as $event) {
                $monthEvents[date('j', strtotime($event['eventDate']))] = site_url('/events/'.date('Y/m/d', strtotime($event['eventDate'])));
            }
        }
        @$this->partials['events:calendar'] = $this->calendar->generate($year, $month, $monthEvents);
    }

    public function index()
    {
        // get partials
        $output = $this->partials;

        // get latest events
        $events = $this->events->get_events(10);
        $output['events:events'] = $this->_populate_events($events);

        // send events to page
        $data['events'] = $events;

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events';
        $output['page:heading'] = 'Upcoming Events';

        // display with cms layer
        $this->pages->view('events', $output, true);
    }

    public function featured()
    {
        // get partials
        $output = $this->partials;

        // get latest events
        $events = $this->events->get_featured_events();
        $output['events:featured'] = $this->_populate_events($events);

        // send events to page
        $data['events'] = $events;

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events';
        $output['page:heading'] = 'Featured Events';

        // display with cms layer
        $this->pages->view('events_featured', $output, true);
    }

    public function viewevent($eventID = '')
    {
        // get partials
        $output = $this->partials;

        // get event
        if ($event = $this->events->get_event($eventID)) {

            // populate template
            $output['event:title'] = $event['eventTitle'];
            $output['event:link'] = site_url('events/viewevent/'.$event['eventID']);
            $output['event:location'] = $event['location'];
            $output['event:date'] = date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($event['eventDate'])).
                        (($event['time']) ? ' ('.$event['time'].')' : '').
                        (($event['eventEnd'] > 0) ? ' - '.date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($event['eventEnd'])) : '');
            $output['event:body'] = $this->template->parse_body($event['description']);
            $output['event:excerpt'] = $this->template->parse_body($event['excerpt']);
            $output['event:author'] = $this->events->lookup_user($event['userID'], true);
            $output['event:author-id'] = $event['userID'];
            $output['event:edit'] = ($event['userID'] == $this->session->userdata('userID')) ? ' | '.anchor('/admin/events/edit_event/'.$eventID, 'Edit Event') : '';

            // set title
            $output['page:title'] = $this->site->config['siteName'].' Events - '.$event['eventTitle'];
            $output['keywords'] = $event['tags'];

            // set meta description
            if ($event['excerpt']) {
                $output['page:description'] = $event['excerpt'];
            }

            // output other stuff
            $data['event'] = $event;
            $data['tags'] = explode(' ', $event['tags']);

            // display with cms layer
            $this->pages->view('events_single', $output, true);
        } else {
            show_404();
        }
    }

    public function tag($tag = '')
    {
        // get partials
        $output = $this->partials;

        // get tags
        $events = $this->events->get_events_by_tag($tag);
        $output['events:events'] = $this->_populate_events($events);

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events';
        $output['page:heading'] = 'Events on "'.$tag.'"';

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // display with cms layer
        $this->pages->view('events', $output, true);
    }

    public function month()
    {
        // get partials
        $output = $this->partials;

        // get event based on uri
        $year = $this->uri->segment(2);
        $month = $this->uri->segment(3);

        // get tags
        $events = $this->events->get_events_by_date($year, $month);
        $output['events:events'] = $this->_populate_events($events);

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events - '.date('F Y', mktime(0, 0, 0, $month, 1, $year));
        $output['page:heading'] = date('F Y', mktime(0, 0, 0, $month, 1, $year));

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // display with cms layer
        $this->pages->view('events', $output, true);
    }

    public function year()
    {
        // get partials
        $output = $this->partials;

        // get event based on uri
        $year = $this->uri->segment(2);

        // get tags
        $events = $this->events->get_events_by_date($year);
        $output['events:events'] = $this->_populate_events($events);

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events - '.date('Y', mktime(0, 0, 0, 1, 1, $year));
        $output['page:heading'] = date('Y', mktime(0, 0, 0, 1, 1, $year));

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // display with cms layer
        $this->pages->view('events', $output, true);
    }

    public function day()
    {
        // get partials
        $output = $this->partials;

        // get event based on uri
        $year = $this->uri->segment(2);
        $month = $this->uri->segment(3);
        $day = $this->uri->segment(4);

        // get tags
        $events = $this->events->get_events_by_date($year, $month, $day);
        $output['events:events'] = $this->_populate_events($events);

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events - '.date('D jS F Y', mktime(0, 0, 0, $month, $day, $year));
        $output['page:heading'] = date('D jS F Y', mktime(0, 0, 0, $month, $day, $year));

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // display with cms layer
        $this->pages->view('events', $output, true);
    }

    public function search($query = '')
    {
        // get partials
        $output = $this->partials;

        // set tags
        $query = ($query) ? $query : strip_tags($this->input->post('query', true));

        // get result from tags
        $objectIDs = $this->tags->search('events', $query);

        $events = $this->events->search_events($query, $objectIDs);
        $output['events:events'] = $this->_populate_events($events);
        $output['query'] = $query;

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Events - Searching Events for "'.$output['query'].'"';
        $output['page:heading'] = 'Search events for: "'.$output['query'].'"';

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // display with cms layer
        $this->pages->view('events_search', $output, true);
    }

    public function ac_search()
    {
        $tags = strtolower($_POST["q"]);
        if (!$tags) {
            return false;
        }

        if ($objectIDs = $this->tags->search('events', $tags)) {
            // form dropdown and myql get countries
            if ($searches = $this->events->search_events($objectIDs)) {
                // go foreach
                foreach ($searches as $search) {
                    $items[$search['tags']] = array('id' => $search['eventID'], 'name' => $search['eventTitle']);
                }
                foreach ($items as $key=>$value) {
                    $id = $value['id'];
                    $name = $value['name'];
                    /* If you want to force the results to the query
                    if (strpos(strtolower($key), $tags) !== false)
                    {
                        echo "$key|$id|$name\n";
                    }*/
                    $this->output->set_output("$key|$id|$name\n");
                }
            }
        }
    }

    public function feed()
    {
        $tagdata = array();

        $this->load->helper('xml');

        $data['encoding'] = 'utf-8';
        $data['feed_name'] = $this->site->config['siteName'] . ' | Events RSS Feed';
        $data['feed_url'] = site_url('/events');
        $data['page_description'] = 'Events RSS Feed for '.$this->site->config['siteName'].'.';
        $data['page_language'] = 'en';
        $data['creator_email'] = $this->site->config['siteEmail'];
        $data['events'] = $this->events->get_events(10);

        $this->output->set_header('Content-Type: application/rss+xml');
        $this->load->view('rss', $data);
    }

    public function _populate_events($events = '')
    {
        if ($events && is_array($events)) {
            $x = 0;
            foreach ($events as $event) {
                // populate template array
                $data[$x] = array(
                    'event:link' => site_url('events/viewevent/'.$event['eventID']),
                    'event:title' => $event['eventTitle'],
                    'event:location' => $event['location'],
                    'event:date' => date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($event['eventDate'])).
                        (($event['time']) ? ' ('.$event['time'].')' : '').
                        (($event['eventEnd'] > 0) ? ' - '.date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($event['eventEnd'])) : ''),
                    'event:day' => date('d', strtotime($event['eventDate'])),
                    'event:month' => date('M', strtotime($event['eventDate'])),
                    'event:year' => date('y', strtotime($event['eventDate'])),
                    'event:body' => $this->template->parse_body($event['description'], true, site_url('events/viewevent/'.$event['eventID'])),
                    'event:excerpt' => $this->template->parse_body($event['excerpt'], true, site_url('events/viewevent/'.$event['eventID'])),
                    'event:author' => $this->events->lookup_user($event['userID'], true),
                    'event:author-id' => $event['userID']
                );

                // get tags
                if ($event['tags']) {
                    $tags = explode(' ', $event['tags']);

                    $i = 0;
                    foreach ($tags as $tag) {
                        $data[$x]['event:tags'][$i]['tag:link'] = site_url('blog/tag/'.$tag);
                        $data[$x]['event:tags'][$i]['tag'] = $tag;

                        $i++;
                    }
                }

                $x++;
            }

            return $data;
        } else {
            return false;
        }
    }
}
