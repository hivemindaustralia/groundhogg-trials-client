<?php

namespace GroundhoggTrialsClient\Steps;

use Groundhogg\Contact;
use Groundhogg\Event;
use Groundhogg\HTML;
use Groundhogg\Reporting\Reports\Report;
use Groundhogg\Steps\Actions\Action;
use function Groundhogg\do_replacements;
use function Groundhogg\get_array_var;
use function Groundhogg\get_contactdata;
use function Groundhogg\get_request_var;
use Groundhogg\Plugin;
use Groundhogg\Step;
use function Groundhogg\remote_post_json;

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * HTTP Post
 *
 * This allows the user send an http post with contact information to any specified URL.
 * The URL must be HTTPS
 *
 * @package     Elements
 * @subpackage  Elements/Actions
 * @author      Adrian Tobey <info@groundhogg.io>
 * @copyright   Copyright (c) 2018, Groundhogg Inc.
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License v3
 * @since       File available since Release 0.9
 */
class Trial_Request extends Action
{

    /**
     * Get the element name
     *
     * @return string
     */
    public function get_name()
    {
        return _x('Trial Request', 'step_name', 'groundhogg');
    }

    /**
     * Get the element type
     *
     * @return string
     */
    public function get_type()
    {
        return 'trial_request';
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function get_description()
    {
        return _x('Create or lock a trial.', 'step_description', 'groundhogg-pro');
    }

    /**
     * Get the icon URL
     *
     * @return string
     */
    public function get_icon()
    {
        return GROUNDHOGG_TRIALS_CLIENT_ASSETS_URL . '/images/response-hook.png';
    }

    /**
     * Display the settings
     *
     * @param $step Step
     */
    public function settings($step)
    {
        $this->start_controls_section();

        $this->add_control('trial_action', [
            'label' => __('Action'),
            'type' => HTML::DROPDOWN,
            'default' => '',
            'field' => [
                'options' => [
                    'start'     => __('Start Trial'),
                    'lock'      => __('Lock Demo Site'),
                    'unlock'    => __('Unlock Demo Site')
                ],
            ],
            'description' => __('Start or end the trial.'),
        ]);

        $this->end_controls_section();
    }

    /**
     * Save the settings
     *
     * @param $step Step
     */
    public function save($step)
    {
        $this->save_setting('trial_action', sanitize_text_field($this->get_posted_data('trial_action')));
    }

    const TRIAL_URL = 'https://try.groundhogg.io/wp-json/gh/v3/trial/';
    const SECRET_KEY = 'SrGRFbcANN5aqPR6aZMpt9X8vFjJr8wU';

    /**
     * Process the http post step...
     *
     * @param $contact Contact
     * @param $event Event
     *
     * @return bool|object|array
     */
    public function run($contact, $event)
    {

        $action = $this->get_setting('trial_action', 'start');

        $args = [];

        switch ($action) {

            case 'start':
                $args = [
                    'first_name' => $contact->get_first_name(),
                    'last_name' => $contact->get_last_name(),
                    'email' => $contact->get_email(),
                ];
                break;
            case 'lock':
            case 'unlock':
                $args = [
                    'site_id' => $contact->get_meta('trial_site_id')
                ];
                break;
        }

        $url = self::TRIAL_URL . $action;

        if (!$action) {
            return new \WP_Error('invalid_action', 'Invalid action.');
        }

        // Increase the timeout.
        add_filter( 'http_request_timeout', function (){ return 60; } );

        $response = remote_post_json($url, $args, 'POST', [
            'gh-secret-key' => self::SECRET_KEY
        ]);

        if (is_wp_error($response)) {
            $contact->add_note($response->get_error_message());
            return $response;
        }

        // Starting the trail, save the trial data
        if ( $action === 'start' ){
            $trial_data = [
                'user_id'   => absint( $response->user_id ),
                'site_id'   => absint( $response->site_id ),
                'site_url'  => esc_url_raw( $response->site_url ),
                'home_url'  => esc_url_raw( $response->home_url ),
                'user_name' => sanitize_text_field( $response->user_name ),
                'password'  => sanitize_text_field( $response->password ),
                'trial_start_time' => time()
            ];

            // Save the trial data
            $contact->update_meta( 'trial_data', $trial_data );
        }


        return $response;

    }

}