<?php

namespace Disclaimer\Providers;

class OptionsServiceProvider implements Provider
{
    public function __construct()
    {
        add_action('acf/init', [$this, 'acf_user_role_options']);
    }
    
    public function register()
    {
       //
    }

    public function acf_user_role_options() {
        if( function_exists('acf_add_options_page') ) {
        
            acf_add_options_page(array(
                'page_title'  => __('Disclaimer Settings'),
                'menu_title'  => __('Disclaimer Settings'),
                'menu_slug'   => 'acf-options-disclaimer-settings',
                'capability'  => 'edit_theme_options',
                'redirect'	  => false,
                'icon_url'    => 'dashicons-warning',
            ));
    
        }
    }
}
