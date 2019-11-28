<?php

namespace GroundhoggTrialsClient;

use function Groundhogg\get_array_var;
use function Groundhogg\get_contactdata;
use function Groundhogg\html;

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2018-08-30
 * Time: 10:34 PM
 */
class Replacements
{
    public function get_replacements()
    {

        $replacements = array(
            array(
                'code' => 'trial_login',
                'callback' => array($this, 'trial_login'),
                'description' => __('Trial login information.', 'groundhogg-edd'),
            ),

        );

        return apply_filters('groundhogg/trials/replacements/init', $replacements);
    }

    /**
     * The trial login information.
     *
     * @param int $contact_id
     * @return string
     */
    public function trial_login( $contact_id )
    {

        $contact = get_contactdata( $contact_id );

        if ( ! $contact ){
            return __( 'No trial data', 'groundhogg' );
        }

        $trial_data = $contact->get_meta( 'trial_data' );

        if ( ! $trial_data ){
            return __( 'No trial data', 'groundhogg' );
        }

        $html = sprintf( __( "<b>Login:</b> %s\n<b>Username:</b> %s\n<b>Password:</b> %s" ),
            get_array_var( $trial_data, 'site_url' ) . '/wp-admin/',
            get_array_var( $trial_data, 'user_name' ),
            get_array_var( $trial_data, 'password' )
        );

        return $html;

    }

}