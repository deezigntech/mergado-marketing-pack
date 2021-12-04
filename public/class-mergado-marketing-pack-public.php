<?php

use Mergado\Tools\Languages;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.mergado.cz
 * @since      1.0.0
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mergado_Marketing_Pack
 * @subpackage Mergado_Marketing_Pack/public
 * @author     Mergado technologies, s. r. o. <info@mergado.cz>
 */
class Mergado_Marketing_Pack_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Mergado_Marketing_Pack_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Mergado_Marketing_Pack_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
//        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/mergado-marketing-pack-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Mergado_Marketing_Pack_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Mergado_Marketing_Pack_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $lang = Languages::getLang();
        $glamiPixelClass = new Mergado\Glami\GlamiPixelClass();
        if($glamiPixelClass->isActive($lang)) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/glami.js', array('jquery'), $this->version, false);
        }

//        wp_enqueue_script($this->plugin_name . '_heureka', plugin_dir_url(__FILE__) . 'js/heureka.js', array('jquery'), $this->version, false);
    }

}
