<?php

namespace Disclaimer\Providers;

class TemplateServiceProvider implements Provider
{
    private $countries;

    public function __construct()
    {
        add_action( 'get_footer', [$this, 'injectDisclaimerTemplate'] );
        add_action( 'wp_enqueue_scripts', [$this, 'disclaimerScripts'] );
        add_filter( 'body_class', [$this, 'disclaimerUserTypeBodyClass'] );
        add_action( 'template_redirect', [$this, 'disclaimerRedirects'] );
    }

    public function register()
    {
        //
    }

    public function disclaimerRedirects()
    {
        $countryCodeArray = [];
        if(get_field('disclaimer_posts_settings_countries_allow')) {
            foreach(get_field('disclaimer_posts_settings_countries_allow') as $countryCode) {
                $countryCodeArray[] = strtolower($countryCode['value']);
            }
        }

        // if(!is_user_logged_in()){
            if((isset($_COOKIE['userType']) && !empty($_COOKIE['userType'])) && (get_field('disclaimer_posts_settings_user_types_allow')) || (isset($_COOKIE['countryCode']) && !empty($_COOKIE['countryCode'])) && ($countryCodeArray)) {
                if(!in_array($_COOKIE['userType'], get_field('disclaimer_posts_settings_user_types_allow')) && !in_array($_COOKIE['countryCode'], $countryCodeArray)) {
                    wp_redirect( get_permalink(get_field('disclaimer_settings_deny_page', 'option')) );
                    die;
                }
            }
        // }
    }

    /*
     * Gets a list of all country choices and country codes
     *
     * @return array
     */
    public function getCountries()
    {
        $countryList = $this->countries;
        $countries = [];
        if ($countryList['choices']){
            foreach ($countryList['choices'] as $key => $value){
                $countries[] = [
                    'name' => $value,
                    'code' => $key
                ];
            }
        }

        return $countries;
    }

    /*
     * Disclaimer modal template
     */
    public function injectDisclaimerTemplate()
    {
        $this->countries = get_field_object('disclaimer_settings_countries_to_allow', 'option');
        include_once wp_normalize_path( DISCLAIMER_PLUGIN_DIR . '/template/disclaimer.php' );
    }
    
    /*
     * Disclaimer assets
     */
    public function disclaimerScripts()
    {
        add_action('wp_enqueue_scripts', function () {
            // enqueue disclaimer style
            wp_enqueue_style('disclaimer/disclaimer.css', DISCLAIMER_PLUGIN_URL . 'public/styles/disclaimer.css', false, null);
            // enqueue disclaimer script
            wp_enqueue_script('disclaimer/disclaimer', DISCLAIMER_PLUGIN_URL . 'public/scripts/disclaimer.js', null ,null, true);
        }, 100);
    }

    /*
     * Return userType cookie value for use with body class
     */
    public function setUserTypeClassName()
    {
        return (isset($_COOKIE['userType']) && !empty($_COOKIE['userType'])) ? 'disclaimer-' . $_COOKIE['userType'] : '';
    }

    /*
     * Set UserTypeClassName in the body class
     */
    public function disclaimerUserTypeBodyClass($classes)
    {
        $classes[] = $this->setUserTypeClassName();
            
        return $classes;
    }
}
