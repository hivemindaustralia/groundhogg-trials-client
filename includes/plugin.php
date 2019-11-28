<?php

namespace GroundhoggTrialsClient;

use Groundhogg\Admin\Admin_Menu;
use Groundhogg\DB\Manager;
use Groundhogg\Extension;
use GroundhoggTrialsClient\Steps\Response_Hook;
use GroundhoggTrialsClient\Steps\Trial_Request;

class Plugin extends Extension
{


    /**
     * Override the parent instance.
     *
     * @var Plugin
     */
    public static $instance;

    /**
     * Include any files.
     *
     * @return void
     */
    public function includes()
    {
//        require  GROUNDHOGG_TRIALS_CLIENT_PATH . '/includes/functions.php';
    }

    /**
     * Init any components that need to be added.
     *
     * @return void
     */
    public function init_components()
    {

    }

    /**
     * Get the ID number for the download in EDD Store
     *
     * @return int
     */
    public function get_download_id()
    {
        return 781995;
    }

    /**
     * Get the version #
     *
     * @return mixed
     */
    public function get_version()
    {
        return GROUNDHOGG_TRIALS_CLIENT_VERSION;
    }

    /**
     * @return string
     */
    public function get_plugin_file()
    {
        return GROUNDHOGG_TRIALS_CLIENT__FILE__;
    }

    public function add_replacements($replacements)
    {
        $edd_replacements = new Replacements();
        foreach ($edd_replacements->get_replacements() as $replacement ){
            $replacements->add( $replacement[ 'code' ], $replacement[ 'callback' ], $replacement[ 'description' ] );
        }
    }

    /**
     * @param \Groundhogg\Steps\Manager $manager
     */
    public function register_funnel_steps( $manager )
    {
        $manager->add_step( new Trial_Request() );
    }

    /**
     * Register autoloader.
     *
     * Groundhogg autoloader loads all the classes needed to run the plugin.
     *
     * @since 1.6.0
     * @access private
     */
    protected function register_autoloader()
    {
        require GROUNDHOGG_TRIALS_CLIENT_PATH . 'includes/autoloader.php';
        Autoloader::run();
    }
}

Plugin::instance();