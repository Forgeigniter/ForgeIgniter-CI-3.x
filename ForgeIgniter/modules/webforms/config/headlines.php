<?php
// get web forms
        if (preg_match_all('/{webform:([A-Za-z0-9_\-]+)}/i', $body, $matches)) {
            // filter matches
            $webformID = preg_replace('/{|}/', '', $matches[0][0]);
            $webform = $this->CI->core->get_web_form_by_ref($matches[1][0]);
            $template[$webformID] = '';
            $required = array();

            // get web form
            if ($webform) {
                // set fields
                if ($webform['fieldSet'] == 1) {
                    $required[] = 'fullName';
                    $required[] = 'subject';
                    $required[] = 'message';

                    // populate template
                    $template[$webformID] .= '
						<div class="formrow field-fullName">
							<label for="fullName">Full Name</label>
							<input type="text" id="fullName" name="fullName" value="'.$this->CI->input->post('fullName').'" class="formelement" />
						</div>

						<div class="formrow field-email">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" value="'.$this->CI->input->post('email').'" class="formelement" />
						</div>

						<div class="formrow field-subject">
							<label for="subject">Subject</label>
							<input type="text" id="subject" name="subject" value="'.$this->CI->input->post('subject').'" class="formelement" />
						</div>

						<div class="formrow field-message">
							<label for="message">Message</label>
							<textarea id="message" name="message" class="formelement small">'.$this->CI->input->post('message').'</textarea>
						</div>
					';
                }

                // set fields
                if ($webform['fieldSet'] == 2) {
                    $required[] = 'fullName';

                    // populate template
                    $template[$webformID] .= '
						<div class="formrow field-fullName">
							<label for="fullName">Full Name</label>
							<input type="text" id="fullName" name="fullName" value="'.$this->CI->input->post('fullName').'" class="formelement" />
						</div>

						<div class="formrow field-email">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" value="'.$this->CI->input->post('email').'" class="formelement" />
						</div>

						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
					';
                }

                // set fields
                if ($webform['fieldSet'] == 0) {
                    // populate template
                    $template[$webformID] .= '
						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
					';
                }

                // set account
                if ($webform['account'] == 1) {
                    // populate template
                    $template[$webformID] .= '
						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
						<input type="hidden" name="message" value="'.$webform['outcomeMessage'].'" />
						<input type="hidden" name="groupID" value="'.$webform['groupID'].'" />
					';
                }

                // set required
                if ($required) {
                    $template[$webformID] .= '
						<input type="hidden" name="required" value="'.implode('|', $required).'" />
					';
                }

                // output encoded webform ID
                $template[$webformID] .= '
					<input type="hidden" name="formID" value="'.$this->CI->core->encode($matches[1][0]).'" />
				';
            } else {
                $template[$webformID] = '';
            }
        }