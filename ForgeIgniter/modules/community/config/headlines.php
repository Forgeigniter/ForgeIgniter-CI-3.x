<?php
        // message centre stuff
        if (preg_match('/({((.+)?)messages:unread((.+)?)})+/i', $body)) {
            // load messages model
            $this->CI->load->model('community/messages_model', 'messages');

            // get message count
            @$template['messages:unread'] = ($messageCount = $this->CI->messages->get_unread_message_count()) ? $messageCount : 0;
        }