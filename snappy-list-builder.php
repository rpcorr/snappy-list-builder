<?php

/*
Plugin Name: Snappy List Builder
Plugin URI: http:www.ronancorr.com/plugins/snappy-list-builder
Description: The ultimate email list building plugin for WordPress. Captures new subscribers. Reward subscribers with a custom download upon opt-in.  Build unlimited lists. Import and export subscribers easily with .csv
Version: 1.0
Author: Ronan Corr
Author URI: http://www.ronancorr.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: snappy-list-builder
*/

/* !0. TABLE OF CONTENT */

/*
    1. HOOKS
        1.1 - registers all our custom shortcodes
        1.2 - register custom admin column headers
        1.3 - register custom admin column data
        1.4 - register ajax actions
        1.5 - load external files to public website
        1.6 - Advance Custom Field settings (for version 5.8.9 or higher)
        1.7 - register our custom menus
        1.8 - load external files in WordPress admin
        1.9 - register plugin options
        1.10 - register activation/deactivation/uninstall functions
        1.11 - register trigger reward downloads
        
    2. SHORTCODES
        2.1 - slb_register_shortcodes() registers all our custom shortcodes 
        2.2 - slb_form_shortcode( $args, $content="" ) returns a html string for an email capture form
        2.3 - slb_manage_subscriptions_shortcode( $args, $content = "" ) displays a form for managing the users list subscriptions
        2.4 - slb_confirm_subscription_shortcode( $args, $content="" ) display subscription opt-in confirmation text and link message subscriptions
        2.5 - slb_download_reward_shortcode( $args, $content = "" ) returns a message if the download link has expired or is invalid

    3. FILTERS
        3.1 - slb_subscriber_column_headers ( $columns ) provide custom heading labels for subscriber custom post
        3.2 - slb_subscriber_column_data ( $column, $post_id ) show custom data in admin page of the subscriber custom post
        3.2.2 - slb_register_custom_admin_titles() registers special custom admin title columns
        3.2.3 - slb_custom_admin_titles( $title, $post_id ) handles custom admin title "title" column data for post types without titles; display person name under Subscriber Name
        3.3 - slb_list_column_headers ( $columns ) provide custom heading labels for list custom post 
        3.4 - slb_list_column_data()  show custom data in admin lists page of the custom post
        3.5 - slb_admin_menus() registers custom plugin admin menus  
         
    4. EXTERNAL SCRIPTS
        4.1 - Include ACF
        4.2 - slb_public_scripts() loads external files into PUBLIC website
        4.3 - slb_admin_scripts() loads external files into WordPress ADMIN

    5. ACTIONS
        5.1 - slb_save_subscription() saves subscription data to an existing or new subscriber
        5.2 - slb_save_subscriber( $subscriber_data ) creates a new subscriber or updates an existing one
        5.3 - slb_add_subscription( $subscriber_id, $list_id ) adds list to subscribers subscriptions
        5.4 - slb_unsubscribe() removes one or more subscriptions from a subscriber and notifies them via email
        5.5 - slb_remove_subscription( $subscriber_id, $list_id ) removes a single subscription from a subscriber
        5.6 - slb_send_subscriber_email( $subscriber_id, $email_template_name,  $list_id ) sends a unique customize email to a subscriber
        5.7 - slb_confirm_subscription( $subscriber_id, $list_id ) add subscription to database and emails subscriber confirmation email
        5.8 - slb_create_plugin_tables() create custom tables for our plugin
        5.9 - slb_activate_plugin() runs on plugin activation
        5.10 - slb_add_reward_link( $uid, $subscriber_id, $list_id, $attachment_id ) add new reward links to the database
        5.11 - slb_trigger_reward_download() triggers a download of the reward file
        5.12 - slb_upate_reward_link_downloads( $uid ) increase reward link download count by one
        5.13 - slb_download_subscribers_csv() generates a .csv file of subscribers data expects $_GET['list_id'] to be set in the URL
        5.14 - slb_parse_import_csv() this function retrieves a csv file from the server and parse data into a php array; it then returns that array in a json formatted object
        5.15 - slb_import_subscribers() imports new subscribers from our import admin page; this function is a form handler and expects subscriber data in the $_POST scope
        5.16 - slb_check_wp_version() checks the current version of WordPress and displays a message in the plugin page if the version is untested
        5.17 - slb_uninstall_plugin() run functions for plugin uninstall
        5.18 - slb_remove_plugin_tables() removes our custom database tables
        5.19 - slb_remove_post_data() removes plugin related custom post type data
        5.20 - slb_remove_options() removes any custom options from the database
        
    6. HELPERS
        6.1 - slb_subscriber_has_subscription( $subscriber_id, $list_id ) returns true or false
        6.2 - slb_get_subscriber_id( $email ) retrieves a subscriber_id from an email address
        6.3 - slb_get_subscriptions( $subscriber_id ) returns an array of list_id's
        6.4 - slb_return_json( $php_array ) transform result to json string
        6.5 - slb_get_acf_key( $field_name ) gets the unique act field key from the field name
        6.6 - slb_get_subscriber_data( $subscriber_id ) returns an array of subscriber data including subscriptions
        6.7 - slb_get_page_select( $input_name = "slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="") returns html for a page selector
        6.8 - slb_get_default_options() returns default option value as an associative array
        6.9 - slb_get_option( $option_name ) returns the requested page option value or it's default
        6.10 - slb_get_current_options() gets the current options and returns values in associative array
        6.11 - slb_get_manage_subscriptions_html( $subscriber_id) generates an html for managing subscriptions
        6.12 - slb_get_email_template( $subscriber_id, $email_template_name, $list_id ) returns an array of email template data IF the template exists
        6.13 - slb_validate_list( $list_object ) validates whether the post object exists and that it's a validate list post_type
        6.14 - slb_validate_subscriber( $subscriber_object ) validates whether the post object exists and that it's a validate subscriber post_type
        6.15 - slb_get_manage_subscriptions_link( $email, $list_id=0 ) returns a unique link for managing a particular users subscriptions
        6.16 - slb_get_querystring_start( $permalink ) returns the appropriate character for the begining of a querystring
        6.17 - slb_get_optin_link( $email, $list_id=0 ) returns a unique link for opting into an email list
        6.18 - slb_get_message_html( $message, $message_type ) returns html for message
        6.19 - slb_get_list_reward( $list_id ) returns false if list has no reward or returns the object containing file and title if it does
        6.20 - slb_get_reward_link( $subscriber_id, $list_id ) returns a unique link for downloading a reward file
        6.21 - slb_generate_reward_uid( $subscriber_id, $list_id) generates a unique number
        6.22 - slb_get_reward( $uid ) return false if list has no reward or returns the object containing file and title if it does
        6.23 - slb_get_list_subscribers( $list_id = 0) returns an array of subscriber_id's
        6.24 - slb_get_list_subscriber_count( $list_id=0 ) returns the amount of subscribers in the list
        6.25 - slb_get_export_link( $list_id=0 ) returns a unique link for downloading a subscribers csv
        6.26 - slb_csv_to_array( $filename, ',') converts csv file into an array
        6.27 - slb_get_admin_notice( $message, $class) returns html formatted for WP admin notices
        6.28 - slb_get_options_settings() get's an array of plugin option data (group and settings) so as to save it all in one place 
        
    7. CUSTOM POST TYPES
        7.1 - subscribers
        7.2 - lists

    8. ADMIN PAGES
        8.1 - slb_dashboard_admin_page() dashboard admin page
        8.2 - slb_import_admin_page() import subscribers admin page
        8.3 - slb_options_admin_page() plugin options admin page 

    9. SETTINGS
        9.1 - slb_register_options() registers all our plugin options

    10. MISC.
        10.1 - slb_add_subscriber_metaboxes( $post ) add subscriber metaboxes to the admin page
        10.2 - slb_subscriber_metabox() fill in data for the subscriber

*/

/* !1. HOOKS */

// 1.1
// hint: registers all our custom shortcodes on init
add_action( 'init', 'slb_register_shortcodes' );

// 1.2
// hint: register custom admin column headers
add_filter( 'manage_edit-slb_subscriber_columns', 'slb_subscriber_column_headers' );
add_filter( 'manage_edit-slb_list_columns', 'slb_list_column_headers' );

// 1.3
// hint: register custom admin column data
add_filter( 'manage_slb_subscriber_posts_custom_column', 'slb_subscriber_column_data', 1, 2 );
add_action( 'admin_head-edit.php', 'slb_register_custom_admin_titles');
add_filter( 'manage_slb_list_posts_custom_column', 'slb_list_column_data', 1, 2);

// 1.4
// hint: register ajax actions
add_action('wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription'); // regular website vistor
add_action('wp_ajax_slb_save_subscription', 'slb_save_subscription'); // admin user
add_action('wp_ajax_nopriv_slb_unsubscribe', 'slb_unsubscribe'); // regular website vistor
add_action('wp_ajax_slb_unsubscribe', 'slb_unsubscribe'); // admin user
add_action('wp_ajax_slb_download_subscribers_csv', 'slb_download_subscribers_csv'); // admin user
add_action('wp_ajax_slb_parse_import_csv','slb_parse_import_csv'); // admin user
add_action('wp_ajax_slb_import_subscribers', 'slb_import_subscribers'); // admin user

// 1.5
// hint: load external files to public website
add_action('wp_enqueue_scripts', 'slb_public_scripts');

// 1.6
// hint: Advance Custom Field settings (for version 5.8.9 or higher)
add_filter( 'acf/settings/url', 'slb_acf_settings_url');
add_filter( 'acf/settings/show_admin', 'slb_acf_show_admin');
add_action('views_edit-slb_subscriber', 'slb_older_acf_warning');
add_action('views_edit-slb_list', 'slb_older_acf_warning');
//if( !defined( 'ACF_LITE') ) define( 'ACF_LITE', true );  // turn off ACF plugin menu

$slb_show_acf_admin = false;

if ( class_exists('ACF') ) :
    $slb_show_acf_admin = true;
endif;

// 1.7
// hint: register our custom menus
add_action('admin_menu', 'slb_admin_menus');

// 1.8
// hint: load external files in WordPress admin
add_action('admin_enqueue_scripts', 'slb_admin_scripts');

// 1.9
// hint: register plugin options
add_action('admin_init', 'slb_register_options');

// 1.10
// hint: register activation/deactivation/uninstall functions
register_activation_hook(__FILE__, 'slb_activate_plugin');
add_action( 'admin_notices', 'slb_check_wp_version');
register_uninstall_hook(__FILE__, 'slb_uninstall_plugin');

// 1.11
// hint: trigger reward downloads
add_action('wp', 'slb_trigger_reward_download');


/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function slb_register_shortcodes() {
    add_shortcode( 'slb_form', 'slb_form_shortcode' );
    add_shortcode( 'slb_manage_subscriptions', 'slb_manage_subscriptions_shortcode' );
    add_shortcode( 'slb_confirm_subscription', 'slb_confirm_subscription_shortcode' );
    add_shortcode( 'slb_download_reward', 'slb_download_reward_shortcode' );
}

// 2.2
// hint: returns a html string for an email capture form
function slb_form_shortcode( $args, $content="") {

    // get the list id
    $list_id = 0;
    if( isset($args['id']) ) $list_id = (int)$args['id'];

    // get the title
    $title = '';
    if( isset($args['title']) ) $title = (string)$args['title'];
    
    // setup our output variable - the form html
    $output = '
    
        <div class="slb">
        
            <form id="slb_register_form" name="slb_form" class="slb-form"
            action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post"> 

                <input type="hidden" name="slb_list" value="' . $list_id .'">
                
                '.  wp_nonce_field( 'slb-register-subscription_' . $list_id, '_wpnonce', true, false );

                if( strlen( $title ) ) :
                    $output .= '<h3 class="slb-title">' . $title . '</h3>';
                endif;

                $output .= '<p class="slb-input-container">
                    <label>Your Name</label><br/>
                    <input type="text" name="slb_fname" placeholder="First Name" />
                    <input type="text" name="slb_lname" placeholder="Last Name" />
                </p>

                <p class="slb-input-container">
                    <label>Your Email</label><br/>
                    <input type="email" name="slb_email" placeholder="ex. you@email.com" /> 
                </p>';

                // including content in our form html if content is passed in the function
                if ( strlen($content)) :
                    $output .= '<div class="slb-content">' . wpautop($content) . '</div>'; // wpauto automattically adds paragraph tags
                endif;

                // get reward
                $reward = slb_get_list_reward( $list_id );

                // if reward exists
                if( $reward !== false ) :

                    // include message about reward
                    $output .='
                        <div class="slb-content slb-reward-message">
                            <p>Get a FREE DOWNLOAD of <strong>' . $reward['title'] . '</strong> when you join this list!</p>
                        </div>
                    ';
                endif;

                // completing our form html
                $output .= '<p class="slb-input-container">
                    
                    <input type="submit" name="slb_submit" value="Sign Me Up!" /> 
                
                </p>
                
            </form>
        
        </div>
    ';

    // return our results/html
    return $output;
}

// 2.3
// hint: displays a form for managing the users list subscriptions
// example: [slb_manage_subscriptions]
function slb_manage_subscriptions_shortcode( $args, $content = "" ) {
    
    // setup our return string
    $output = '<div class="slb slb_manage_subscriptions">';

    try {
        
        // get the email address from the URL
        $email = ( isset ( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';

        // get the subscriber id from the email address
        $subscriber_id = slb_get_subscriber_id( $email );

        // get the subscriber data
        $subscriber_data = slb_get_subscriber_data( $subscriber_id );
        
        // if subscriber exists
        if ( $subscriber_id ):
            
            // get subscription html
            $output .= slb_get_manage_subscriptions_html( $subscriber_id );
            
        else:
            
            // invalid link
            $output .= '<p>This link is invalid</p>';
            
        endif;
        
    } catch ( Exception $e ) {

        //PHP error
    }

    // close the html div tag
    $output .= '</div>';

    // return the html
    return $output;
}

// 2.4
// hint: display subscription opt-in confirmation text and link message subscriptions
// example: [slb_confirm_subscription]
function slb_confirm_subscription_shortcode( $args, $content="" ) {
    
    // setup output variable
    $output = '<div class="slb">';
    
    // setup email and list_id variables and handle if they are not defined in the GET scope
    $email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
    $list_id = ( isset( $_GET['list'] ) ) ? esc_attr( $_GET['list'] ) : 0;

    // get subscriber id from email
    $subscriber_id = slb_get_subscriber_id( $email );
    $subscriber = get_post( $subscriber_id );
    
    // if we found a subscriber matching that email address
    if ( $subscriber_id && slb_validate_subscriber( $subscriber ) ) :
        
        // get list object
        $list = get_post( $list_id );

        // if list and subscriber are valid
        if( slb_validate_list( $list ) ) :
        
            // if subscriptions has not yet been added
            if( !slb_subscriber_has_subscription( $subscriber_id, $list_id ) ) :

                // complete opt-in
                $optin_complete = slb_confirm_subscription( $subscriber_id, $list_id );

                if ( !$optin_complete ) :
                    
                    $output .= slb_get_message_html( 'Due to an unknown error, we were unable to confirm your subscription',
                'error' );

                    $output .= '</div>';
                
                    return $output;
                    
                endif;
                
            endif;

            // get confirmation message html and append it to output
            $output .= slb_get_message_html( 'Your subscription to ' . $list->post_title . ' has now been confirmed.',
            'confirmation' );

            // get manage subscriptions link
            $manage_subscriptions_link = slb_get_manage_subscriptions_link( $email, $list_id );  // missing $list_id in lecture

            // append link to output
            $output .= '<p><a href="' . $manage_subscriptions_link . '">Click here to manage your subscriptions.</a></p>';  
        
        else :
            
            $output .= slb_get_message_html( 'This link is invaild.', 'error' );
                 
        endif;
    
    else :

        $output .= slb_get_message_html( 'This link is invalid. Invalid Subscriber ' . $email . '.', 'error' );
            
    endif;

    // close .slb div
    $output .= '</div>';
    
    // return output html
    return $output;
}

// 2.5
// [slb_download_reward]
// hint: returns a message if the download link has expired or is invalid
function slb_download_reward_shortcode( $args, $content = "" ) {
    
    $output = '';

    $uid = ( isset($_GET['reward'])) ? (string)$_GET['reward'] : 0;

    // get reward form link uid
    $reward = slb_get_reward( $uid );

    // if reward was found
    if( $reward !== false) :
        
        if( $reward['downloads'] >= slb_get_option( 'slb_download_limit' ) ) :

            $output .= slb_get_message_html( 'This link has reached it\'s download limit.', 'warning');
        
        endif;
        
    else :
        
        $output .= slb_get_message_html( 'This link is invalid.', 'error');
        
    endif;

    return $output;
    
}

/* !3. FILTERS */

// 3.1
// hint: provide custom heading labels for subscriber custom post
function slb_subscriber_column_headers ( $columns ) {
    
    // creating custom column header data
    $columns = array (
        'cb' => '<input type="checkbox" />',
        'title' => __('Subscriber Name'),
        'email' => __('Subscriber Email Address'),
    );

    // returning new columns names
    return $columns;
}

// 3.2
// hint: show custom data in admin page of the subscriber custom post
function slb_subscriber_column_data ( $column, $post_id ) {
    
    // setup our return text
    $output = '';
    
    switch ( $column )  {
        
        case 'title' :
            // get the custom name data
            $fname = get_field( 'slb_fname', $post_id );
            $lname = get_field( 'slb_lname', $post_id );
            $output .= $fname . ' ' . $lname;
            break;
        case 'email' :
            // get the custom email data
            $email = get_field( 'slb_email', $post_id );
            $output .= $email;
            break;
    }

    // echo the output
    echo $output;
}

// 3.2.2
// hint: registers special custom admin title columns
function slb_register_custom_admin_titles() {
    add_filter(
        'the_title',
        'slb_custom_admin_titles',
        99,
        2
    );
}

// 3.2.3
// hint: handles custom admin title "title" column data for post types without titles; display person name under Subscriber Name
function slb_custom_admin_titles( $title, $post_id ) {
    
    global $post;

    $output = $title;

    if ( isset($post->post_type) ) :
        switch( $post->post_type ) {
            case 'slb_subscriber' :
                $fname = get_field('slb_fname', $post_id );
                $lname = get_field('slb_lname', $post_id );
                $output = $fname . ' ' . $lname;
                break;
        }
    endif;

    // echo the output
    return $output;
}

// 3.3 
// hint: provide custom heading labels for list custom post
function slb_list_column_headers ( $columns ) {
    
    // creating custom column header data
    $columns = array (
        'cb' => '<input type="checkbox" />',
        'title' => __('List Name'),
        'reward' => __('Opt-in Reward'),
        'subscribers' => __('Subscribers'),
        'shortcode' => __('Shortcode'),
    );

    // returning new columns names
    return $columns;
}


// 3.4
// hint: show custom data in admin lists page of the custom post
function slb_list_column_data ( $column, $post_id ) {
    
    // setup our return text
    $output = '';
    
    switch ( $column )  {
        
        case 'reward' :
            $reward = slb_get_list_reward( $post_id );
            if( $reward !== false ) :                
                $output .= '<a href="' . $reward['file']['url'] . '" download="' . $reward['title'] . '">' . $reward['title'] .'</a>';
            endif;
            break;
        case 'subscribers' :
            // get the count of current subscribers
            $subscriber_count = slb_get_list_subscriber_count( $post_id );
            // get our unique export link
            $export_href = slb_get_export_link( $post_id );
            // append the subscriber count to output
            $output .= $subscriber_count;
            // if there are more than one subscriber, add new export link to $output
            if ( $subscriber_count ) $output .= ' <a href="' . $export_href . '">Export</a>';
            break;
        case 'shortcode' :
            $output .= '[slb_form id="' . $post_id . '"]';
            break;
    }

    // echo the output
    echo $output;
}

// 3.5
// hint: registers custom plugin admin menus
function slb_admin_menus(){
    
    /* main menu */
        
        $top_menu_item = 'slb_dashboard_admin_page';

        add_menu_page( '', 'List Builder', 'manage_options', 'slb_dashboard_admin_page', 'slb_dashboard_admin_page', 'dashicons-email-alt' );

        /* sub menus */
            
            // dashboard
            add_submenu_page( $top_menu_item, '', 'Dashboard', 'manage_options', $top_menu_item, $top_menu_item );

            // email lists
            add_submenu_page( $top_menu_item, '', 'Email Lists', 'manage_options', 'edit.php?post_type=slb_list' );

            // subscribers
            add_submenu_page( $top_menu_item, '', 'Subscribers', 'manage_options', 'edit.php?post_type=slb_subscriber' );

            // import subscribers
            add_submenu_page( $top_menu_item, '', 'Import Subscribers', 'manage_options', 'slb_import_admin_page', 'slb_import_admin_page' );

            // plugin options
            add_submenu_page( $top_menu_item, '', 'Plugin Options', 'manage_options', 'slb_options_admin_page', 'slb_options_admin_page' );
        }


/* !4. EXTERNAL SCRIPTS */

// 4.1
// hint: Include ACF
include_once( plugin_dir_path( __FILE__ ) . 'lib/advanced-custom-fields/acf.php');   

// 4.2
// hint: loads external files into PUBLIC website
function slb_public_scripts() {
    
    // register scripts with WordPress's internal library
    wp_register_script('snappy-list-builder-js-public', plugins_url('/js/public/snappy-list-builder.js', __FILE__),
    array( 'jquery'), '', true);

    wp_register_style('snappy-list-builder-css-public', plugins_url('/css/public/snappy-list-builder.css', __FILE__));

    // add to queue of scripts that get loaded into every page
    wp_enqueue_script('snappy-list-builder-js-public'); 
    wp_enqueue_style('snappy-list-builder-css-public');

    // setup PHP variables to pass into out javascript file
    $php_vars = [
        'admin_url' => admin_url(),
        'ajax_url' => admin_url('admin-ajax.php'),
        'hello' => 'world'
    ];

    // pass in our php variables and make them available in javascript as variable php (ex. php.ajax_url)
    wp_localize_script('snappy-list-builder-js-public', 'php', $php_vars ); 
}

// 4.3
// hint: loads external files into WordPress ADMIN
function slb_admin_scripts() {
    
    // register scripts with WordPress' internal library
    wp_register_script( 'snappy-list-builder-js-private', plugins_url('/js/private/snappy-list-builder.js', __FILE__), array( 'jquery' ), '', true );

    // add to queue of scripts that get loaded into every admin page
    wp_enqueue_script( 'snappy-list-builder-js-private' );
    
}


/* !5. ACTIONS */

// 5.1 
// hint: saves subscription data to an existing or new subscriber
function slb_save_subscription() {
    
    // setup default result data
    $result = array(
        'status' => 0,
        'message' => 'Subscription was not saved. ',
        'error' => '',
        'errors' => array()
    );

    try {
        
        // get list_id
        //$list_id = (int)$_POST['slb_list'];
        $list_id = (isset($_POST['slb_list'])) ? (int)$_POST['slb_list'] : 0;

        // verify nonce
        if( check_ajax_referer( 'slb-register-subscription_' . $list_id ) ) :
        
            // prepare subscriber data
            $subscriber_data = array(
            'fname' => esc_attr( $_POST['slb_fname'] ),
            'lname' => esc_attr( $_POST['slb_lname'] ),
            'email' => esc_attr( $_POST['slb_email'] ),
            );

            // setup our errors array
            $errors = array(); 

            // form validation
            if ( !strlen( $subscriber_data[ 'fname'] ) ) $errors['fname'] = 'First name is required.';
            if ( !strlen( $subscriber_data[ 'lname'] ) ) $errors['lname'] = 'Last name is required.';
            if ( !strlen( $subscriber_data[ 'email'] ) ) $errors['email'] = 'Email address is required.';
            if ( !strlen( $subscriber_data[ 'email'] ) && !is_email( $subscriber_data[ 'email' ]) ) $errors['email'] = 'Email address must be valid.';
            

            // if there are errors
            if ( count( $errors ) ) :

                // append errors to result structure for later use
                $result[ 'error' ] = 'Some fields are still required. ';
                $result[ 'errors' ] = $errors;
                
            else :
                // if there are no errors, proceed...
                
                // attempt to create/save subscriber
                $subscriber_id = slb_save_subscriber( $subscriber_data );

                // if subscriber was saved successfully $subscriber_id will be greater than 0
                if ( $subscriber_id ) :
                    
                    // if subscriber already has this subscription
                    if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ) :
                        
                        // get the list object
                        $list = get_post( $list_id );

                        // return detailed error
                        $result[ 'error' ] .= esc_attr( $subscriber_data['email'] . ' is already subscribed to ' . $list->post_title . '.');

                    else :

                        // send new subscriber a confirmation email, returns true if we were successful
                        $email_sent = slb_send_subscriber_email( $subscriber_id, 'new_subscription', $list_id );

                        // if email was sent
                        if ( !$email_sent ) :

                            // email could not be sent
                            $result[ 'error' ] = 'Unable to send email. ';

                        else :

                            // email sent and subscription saved!
                            $result[ 'status' ] = 1;
                            $result[ 'message' ] = 'Success! A confirmation email has been sent to '. $subscriber_data[ $email ];
                            
                            // clean up: remove our empty error
                            unset( $result[ $error ]);
                            
                        endif;
                        
                    endif;
                    
                endif;
            
            endif;
        
        endif;
        
    } catch ( Exception $e ) {
    
    }

    // return result as json
    slb_return_json( $result );
}

// 5.2
// hint: creates a new subscriber or updates an existing one
function slb_save_subscriber( $subscriber_data ) {

    // setup default subscriber id
    // 0 means the subscriber was not saved
    $subscriber_id = 0;

    try{
        
        $subscriber_id = slb_get_subscriber_id( $subscriber_data['email'] );

        //if the subscriber does not already exist...
        if( !$subscriber_id ) :
            
            // add new subscriber to database
            $subscriber_id = wp_insert_post(
                array(
                    'post_type' => 'slb_subscriber',
                    'post_title' => $subscriber_data[ 'fname' ] . ' ' . $subscriber_data[ 'lname' ],
                    'post_status' => 'publish', 
                ),
                true
            );
            
        endif;

        // add/update custom meta data
        update_field(slb_get_acf_key('slb_fname'), $subscriber_data['fname'], $subscriber_id);
        update_field(slb_get_acf_key('slb_lname'), $subscriber_data['lname'], $subscriber_id);
        update_field(slb_get_acf_key('slb_email'), $subscriber_data['email'], $subscriber_id);
        
    } catch ( Exeception $e ) {
        
        // a PHP error has occurred
    }

    // reset the WordPress post object
    wp_reset_query();

    // return subscriber_id
    return $subscriber_id;
}

// 5.3
// hint: adds list to subscribers subscriptions
function slb_add_subscription( $subscriber_id, $list_id ) {
    
    // setup default return value
    $subscription_saved = false;

    // if the subscriber does NOT have the current list subscription
    if( !slb_subscriber_has_subscription( $subscriber_id, $list_id ) ) :

        // get subscriptions and append new $list_id
        $subscriptions = slb_get_subscriptions( $subscriber_id );
        $subscriptions[] = $list_id; 
        
        //array_push( $subscriptions, $list_id ); does the same as $subscriptions[] = $list_id;

        // update slb_subscriptions
        update_field( slb_get_acf_key( 'slb_subscriptions' ), $subscriptions, $subscriber_id );

        // subscriptions updated!
        $subscription_saved = true;
        
    endif;

    // return result
    return $subscription_saved;
}

// 5.4
// hint: removes one or more subscriptions from a subscriber and notifies them via email
// this function is an ajax handler...
// expects form post data: $_POST['subscriber_id'] and $_POST['list_id']
function slb_unsubscribe() {
    
    // setup default result data
    $result = array(
        'status' => 0,
        'message' => 'Subscriptions were NOT updated.',
        'error' => '',
        'errors' => array(),  
    );

    $subscriber_id = ( isset( $_POST['subscriber_id']) ) ? esc_attr( (int)$_POST['subscriber_id'] ) : 0;
    $list_ids = ( isset($_POST['list_ids']) ) ? $_POST['list_ids'] : 0;

    try {

        // validate nonce
        if( check_ajax_referer( 'slb-update-subscriptions_' . $subscriber_id ) ):
            // if there are lists to remove
            if( is_array($list_ids) ) :

                // loop over lists to remove
                foreach( $list_ids as &$list_id ) :

                    // remove this subscription
                    slb_remove_subscription( $subscriber_id, $list_id );
                endforeach;
            endif;

            // setup success status and message
            $result['status'] = 1;
            $result['message'] = 'Subscriptions updated. ';

            // get the updated list of subscriptions as html
            $result['html'] = slb_get_manage_subscriptions_html( $subscriber_id );

        endif;
        
    } catch ( Exception $e ) {
        //PHP error
    }

    // return result as json
    slb_return_json( $result );
}

// 5.5
// hint: removes a single subscription from a subscriber
function slb_remove_subscription( $subscriber_id, $list_id ) {
    
    // setup default return value
    $subscription_saved = false;

    // if the subscriber has the current list subscription
    if( slb_subscriber_has_subscription( $subscriber_id, $list_id) ) :

        // get current subscriptions
        $subscriptions = slb_get_subscriptions( $subscriber_id );

        // get the position of the $list_id to remove
        $needle = array_search( $list_id, $subscriptions );

        // remove $list_id from subscriptions array
        unset( $subscriptions[$needle] );

        // update slb_subscriptions
        update_field( slb_get_acf_key( 'slb_subscriptions'), $subscriptions, $subscriber_id );

        // subscriptions updated!
        $subscription_saved = true;
        
    endif;

    // return result
    return $subscription_saved;
}

// 5.6
// hint: sends a unique customize email to a subscriber
function slb_send_subscriber_email( $subscriber_id, $email_template_name,  $list_id ) {

    // setup return variable
    $email_sent = false;

    // get email template data
    $email_template_object = slb_get_email_template( $subscriber_id, $email_template_name, $list_id );

    // if email template data was found
    if ( !empty( $email_template_object ) ) :
        
        // get subscriber data
        $subscriber_data = slb_get_subscriber_data( $subscriber_id );

        // set wp_mail headers
        $wp_mail_headers = array( 'Content-Type: text/html; charset=UTF-8' );

        // use wp_mail to send email
        $email_sent = wp_mail( array( $subscriber_data[ 'email'] ), $email_template_object[ 'subject'],
        $email_template_object[ 'body' ], $wp_mail_headers );
        
    endif;
    
    return $email_sent;
}

// 5.7
// hint add subscription to database and emails subscriber confirmation email
function slb_confirm_subscription( $subscriber_id, $list_id ) {
    
    // setup return variable
    $optin_complete = false;

    // add new subscription
    $subscription_saved = slb_add_subscription( $subscriber_id, $list_id );

    // if subscription was saved
    if ( $subscription_saved ) :

        // send email
        $email_sent = slb_send_subscriber_email( $subscriber_id, 'subscription_confirmed', $list_id );
        
        // if email sent
        if ( $email_sent ) :
            
            // return true
            $optin_complete = true;
            
        endif;
            
    endif;

    // return result
    return $optin_complete;
}

// 5.8
// hint: create custom tables for our plugin
function slb_create_plugin_tables() {
    
    global $wpdb;

    // setup return value
    $return_value = false;

    try {

        $table_name = $wpdb->prefix . "slb_reward_links";
        $charset_collate = $wpdb->get_charset_collate();
         
        // sql for our table creation
        $sql = "CREATE TABLE $table_name (
                id mediumint(11) NOT NULL AUTO_INCREMENT,
                uid varchar(128) NOT NULL,
                subscriber_id mediumint(11) NOT NULL,
                list_id mediumint(11) NOT NULL,
                attachment_id mediumint(11) NOT NULL,
                downloads mediumint(11) DEFAULT 0 NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

        // make sure we include WordPress functions for dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // dbDelta will create a new table if none exists or update an existing one
        dbDelta($sql);

        // return true
        $return_value = true;
        
    } catch (Exception $e ) {
        
        // php error
    }

    // return result
    return $return_value;
}

// 5.9
// hint: runs on plugin activation
function slb_activate_plugin() {
    
    // setup custom database tables
    slb_create_plugin_tables();
}

// 5.10
// hint: add new reward links to the database
function slb_add_reward_link( $uid, $subscriber_id, $list_id, $attachment_id ) {

    global $wpdb;

    // setup our return value
    $return_value = false;

    try {

        $table_name = $wpdb->prefix . "slb_reward_links";

        $wpdb->insert(
            $table_name,
            array(
                'uid' => $uid,
                'subscriber_id' => $subscriber_id,
                'list_id' => $list_id,
                'attachment_id' => $attachment_id,
            ),
            array (
                '%s',
                '%d',
                '%d',
                '%d',
            )
        );

        // return true
        $return_value = true;
                
    } catch ( Exception $e ) {

        // php error
        
    }

    // return the result
    return $return_value;
    
}

// 5.11
// hint: triggers a download of the reward file
function slb_trigger_reward_download() {

    global $post;

    if( $post->ID == slb_get_option( 'slb_reward_page_id') && isset($_GET['reward']) ) :
        
        $uid = ( $_GET['reward']) ? (string)$_GET['reward'] : 0;

        // get reward form link uid
        $reward = slb_get_reward( $uid );

        // if reward was found
        if( $reward !== false && $reward['downloads'] < slb_get_option( 'slb_download_limit') ) :

            slb_upate_reward_link_downloads( $uid );

            // get the reward mimetype
            $mimetype = $reward['file']['mime_type'];
            // extract the filetype from the mimetype
            $mimetype_array = explode('/', $mimetype);
            $filetype = $mimetype_array[1];

            header("Content-type: ". $mimetype, true, 200 );
            header("Content-Disposition: attachment; filename=".$reward['title'] . '.' . $filetype);
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($reward['file']['url']);
            exit();
                
        endif;
        
    endif;
}

// 5.12
// hint: increase reward link download count by one
function slb_upate_reward_link_downloads( $uid ) {
    
    global $wpdb;

    // setup our return value
    $return_value = false;

    try {

        $table_name = $wpdb->prefix. "slb_reward_links";

        // get current download count
        $current_count = $wpdb->get_var(
            $wpdb->prepare(
                "
                    SELECT downloads
                    FROM $table_name
                    WHERE uid = %s
                ",
                $uid
            )
        );

        // set new count
        $new_count = (int)$current_count + 1;
        
        // update downloads for this reward link entry
        $wpdb->query(
            $wpdb->prepare(
                "
                  UPDATE $table_name
                  SET downloads = $new_count
                  WHERE uid = %s
                ",
                $uid
            ) 
        );

        $return_value = true;

    } catch ( Exception $e ) {
        
        // php error
    }

    return $return_value;
}

// 5.13
// hint: generates a .csv file of subscribers data
// expects $_GET['list_id'] to be set in the URL
function slb_download_subscribers_csv() {
    
    // get the list id from the URL scope
    $list_id = ( isset($_GET['list_id']) ) ? (int)$_GET['list_id'] : 0;

    // set up our return value
    $csv = '';

    // get the list object
    $list = get_post( $list_id );
    
    // get the list's subscribers or get all subscribers if no list id is given
    $subscribers = slb_get_list_subscribers( $list_id );

    // if we have confirm subscribers
    if( $subscribers !== false ) :
        
        // get the current date
        $now = new DateTime();
        
        // setup a unique filename for the generated export file
        $fn1 = 'snappy-list-builder-export-list-id-' . $list_id . '-date-' . $now->format('Ymd') . '.csv';
        $fn2 = plugin_dir_path( __FILE__ ) . 'exports/' . $fn1;

        // open new file in write mode
        $fp = fopen( $fn2, 'w' );
        
        // get the first subscriber's data
        $subscriber_data = slb_get_subscriber_data( $subscribers[0] );

        // remove the subscriptions and name column from the data
        unset( $subscriber_data['subscriptions']);
        unset( $subscriber_data['name']);

        // build our csv headers array from $subscriber_data's data keys
        $csv_headers = array();
        foreach( $subscriber_data as $key => $value ) :
            array_push( $csv_headers, $key);
        endforeach;

        // append $csv_headers to our csv file
        fputcsv( $fp, $csv_headers );

        // loop over all our subscribers
        foreach( $subscribers as &$subscriber_id) :

            // get the subscriber data of the current subscriber
            $subscriber_data = slb_get_subscriber_data( $subscriber_id );

            // remove the subscriptions and name column from the data
            unset( $subscriber_data['subscriptions']);
            unset( $subscriber_data['name']);

            // append $csv_headers to our csv file
            fputcsv( $fp, $subscriber_data );
                        
        endforeach;

        // read open our new file is read mode
        $fp = fopen($fn2, 'r');
        //read our new csv file and store it's content in $fc
        $fc = fread( $fp, filesize( $fn2 ) );
        // close our open pointer
        fclose( $fp );
        
        // setup file headers
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=" . $fn1);
        // echo the contents of our file and return it to the browser
        echo( $fc );
        // exit PHP process
        exit;
        
    endif;

    // return false if we were unable to download our csv
    return false;
}

// 5.14
// hint: this function retrieves a csv file from the server and parse data into a php array
// it then returns that array in a json formatted object
// this function is a ajax post form handler
// expects: $_POST['slb_import_file_id]
function slb_parse_import_csv() {
    
    // setup our return array
    $result = array(
      'status' => 0,
      'message' => 'Could not parse import CSV. ',
      'error' => '',
      'data' => array(),  
    );

    try {
        
        // get the attachment id from $_POST['slb_import_file_id']
        $attachment_id = (isset($_POST['slb_import_file_id'])) ? esc_attr( $_POST['slb_import_file_id'] ) : 0;

        // get the filename using wp's get_attached_file
        $filename = get_attached_file( $attachment_id );

        // if we got the filename
        if( $filename !== false ) :

            // parse the csv file into a php array using slb_csv_to_array
            $csv_data = slb_csv_to_array( $filename, ',');

            // if we were able to parse the file and there's data in it
            if( $csv_data !== false && count( $csv_data ) ) :

                // append the data to our result array and return as success
                $result = array(
                    'status' => 1,
                    'message' => 'CSV Import data parsed successfully',
                    'error' => '',
                    'data' => $csv_data,
                );

            endif;

        else :
            
            // return an error message if we could not retrieve the file
            $result['error'] = 'The import file does not exists. ';
            
        endif;

    } catch ( Exception $e) {
        
        // php error
    }

    // return the result as json
    slb_return_json( $result );
}

// 5.15
// hint: imports new subscribers from our import admin page
// this function is a form handler and expects subscriber data in the $_POST scope
function slb_import_subscribers() {
    
    // setup our return array
    $result = array(
      'status' => 0,
      'message' => 'Could not import subscribers.',
      'error' => '',
      'errors' => array(),  
    );

    try {

        // get the assignment values
        $fname_column = (isset($_POST['slb_fname_column'])) ? (int)$_POST['slb_fname_column'] : 0;
        $lname_column = (isset($_POST['slb_lname_column'])) ? (int)$_POST['slb_lname_column'] : 0;
        $email_column = (isset($_POST['slb_email_column'])) ? (int)$_POST['slb_email_column'] : 0;

        // get the list id to import to
        $list_id = (isset($_POST['slb_import_list_id'])) ? (int)$_POST['slb_import_list_id'] : 0;

        // get the selected subscriber rows to import
        $selected_rows = (isset($_POST['slb_import_rows'])) ? (array)$_POST['slb_import_rows'] : array();

        // setup the data for selected rows
        $subscribers = array();

        // setup a variable for counting the subscribers we add
        $added_count = 0;

        // loop over selected rows and get the data
        foreach( $selected_rows as &$row_id ) :

            // build our subscriber data
            $subscriber_data = array(
                'fname' => (string)$_POST['s_' . $row_id . '_' . $fname_column],
                'lname' => (string)$_POST['s_' . $row_id . '_' . $lname_column],
                'email' => (string)$_POST['s_' . $row_id . '_' . $email_column],
            );

            // if the subscriber email is invalid
            if( !is_email( $subscriber_data['email']) ) :
            
                // don't attempt to add the subscriber if the email is not valid
                $result['errors'][] = 'Invalid email detected: ' . $subscriber_data['email'] . '. This subscriber was not added';

            else:
                
                // if subscriber email is valid ...
                // add subscriber to the database
                $subscriber_id = slb_save_subscriber( $subscriber_data );
                
                // if subscriber was created or updated successfully
                if( $subscriber_id ) :
                    
                    // add subscription for this subscriber without opt-in
                    $subscription_added = slb_add_subscription( $subscriber_id, $list_id );

                    // updated our added count
                    $added_count++;
                    
                endif;
                
            endif;
            
        endforeach;

        // if no subscribers were actually added ...
        if ( $added_count == 0 ) :
        
            // return error message
            $result['error'] = 'No subscribers were imported.';

        else :
            
            // if subscribers were added ...
            // return success!
            $result = array(
                'status' => 1,
                'message' => $added_count . ' subscribers imported successfully. ',
                'error' => '',
                'errors' => array(),
            );
            
        endif;
    
    } catch (Exception $e) {
        
        // php error
    }

    // return result as json
    slb_return_json( $result );
}

// 5.16
// hint: checks the current version of WordPress and displays a 
// message in the plugin page if the version is untested
function slb_check_wp_version() {
    
    global $pagenow;
    
    if ( $pagenow == 'plugins.php' && is_plugin_active('snappy-list-builder/snappy-list-builder.php') ):
        
        // get the wp version
        $wp_version = get_bloginfo( 'version' );

        // tested versions
        // these are the versions we've tested our plugin
        $tested_versions = array(
            '4.2.0',
            '4.2.1',
            '4.2.2',
            '4.2.3',
            '4.2.4',
            '6.0',
        );

        $tested_range = array(4.0,6.0);

        // if the current wp version is not in our tested versions...
        // remove: if( !in_array( $wp_version, $tested_versions ) ):
        if( (float)$wp_version >= (float)$tested_range[0] && (float)$wp_version <= (float)$tested_range[1] ):

            // we're good!
        
        else :

            // get notice html
            $notice = slb_get_admin_notice('Snappy List Builder has not been tested in your version of WordPress.  It still may
            work though...', 'error');

            // echo the notice html
            echo( $notice );
            
        endif;
    
    endif;
}

// 5.17
// hint: run functions for plugin uninstall
function slb_uninstall_plugin() {
    
    // remove our custom plugin tables
    slb_remove_plugin_tables();
    // remove custom post types posts and data
    slb_remove_post_data();
    // remove plugin options
    slb_remove_options();    
}

// 5.18
// hint: removes our custom database tables
function slb_remove_plugin_tables() {
    
    // get WP'wpdb class
    global $wpdb;

    // setup return variable
    $tables_removed = false;

    try {
        
        // get our custom table name
        $table_name = $wpdb->prefix . "slb_reward_links";

        // delete table from database
        $tables_removed = $wpdb->query("DROP TABLE IF EXISTS $table_name;");
        
    } catch ( Exception $e ) {
        // PHP error
    }

    // return result
    return $tables_removed;
}

// 5.19
// hint: removes plugin related custom post type post data
function slb_remove_post_data() {

    // get WP's wpdb class
    global $wpdb;
    
    // setup our return variable
    $data_removed = false;

    try {
        
        // get our custom table name
        $table_name = $wpdb->prefix . "posts";
        
        // set up custom post types array
        $custom_post_types = array(
          'slb_subscriber',
          'slb_list'  
        );

        // remove data from the posts db table where post types are equal to our custom post types
        $data_removed = $wpdb->query(
            $wpdb->prepare(
                "
                    DELETE FROM $table_name
                    WHERE post_type = %s OR post_type = %s
                ",
                $custom_post_types[0],
                $custom_post_types[1]
            )
        );

        // get our custom table name
        $table_name_1 = $wpdb->prefix . "_postmeta";
        $table_name_2 = $wpdb->prefix . "_posts"; 

        // delete orphaned meta data
        $wpdb->query(
            $wpdb->prepare(
                "
                DELETE pm
                FROM $table_name_1 pm
                LEFT JOIN $table_name_2 wp ON wp.ID = pm.post_id
                WHERE wp.ID IS NULL
                "
            )
        );
        
    } catch ( Exception $e ) {
        // PHP error
    }

    // return result
    return $data_removed;    
}

// 5.20
// hint: removes any custom options from the database
function slb_remove_options() {
    
    // setup our return variable
    $options_removed = false;

    try {
        
        // get plugin option settings
        $options = slb_get_options_settings();

        // loop over all the settings
        foreach ( $options['settings'] as &$setting ):

            // unregister the setting
            unregister_setting( $options['group'], $setting );
            
        endforeach;

        $options_removed = true;
        
    } catch ( Exception $e ) {
        // PHP error
    }

    // return result
    return $options_removed;
}

/* !6. HELPERS */

// 6.1
// hint: returns true or false
function slb_subscriber_has_subscription( $subscriber_id, $list_id ) {
    
    // setup default return value
    $has_subscription = false;

    // get subscriber
    $subscriber = get_post( $subscriber_id );

    // get subscriptions
    $subscriptions = slb_get_subscriptions( $subscriber_id );

    // check subscriptions for $list_id
    if ( in_array( $list_id, $subscriptions) ) :
        
        // found the $list_id in $subscriptions
        // this subscriber is already subscribed to this list
        $has_subscription = true;

    else :
        
        // did not find $list_id in $subscriptions
        // this subscriber is not yet subscribed to this list
        
    endif;

    // return the result
    return $has_subscription;
}

// 6.2
// hint: retrieves a subscriber_id from an email address
function slb_get_subscriber_id( $email ){
    
    $subscriber_id = 0;

    try {
        
        // check if subscriber already exists
        $subscriber_query = new WP_Query( 
           array(
               'post_type' => 'slb_subscriber',
               'posts_per_page' => 1,
               'meta_key' => 'slb_email',
               'meta_query' => array(
                   array(
                    'key' => 'slb_email',
                    'value' => $email, // or whatever it is you are using here
                    'compare' => '=',       
                   ),
                ),
           )  
        );

        // if the subscriber exists...
        if( $subscriber_query->have_posts() ) :
                
            // get the subscriber id
            $subscriber_query->the_post();
            $subscriber_id = get_the_ID();
            
        endif;
        
    } catch( Exception $e) {
        
        // a PHP error occured
    }

    // reset the WordPress post object
    wp_reset_query();

    return (int)$subscriber_id;
}

// 6.3
// hint: returns an array of list_id's
function slb_get_subscriptions( $subscriber_id ) {
    
    $subscriptions = array();

    // get subscriptions (returns array of list objects)
    $lists = get_field( slb_get_acf_key( 'slb_subscriptions' ), $subscriber_id );
    
    // if $lists returns something
    if ( $lists ) :
        
        // if $lists is an array and there is one or more items
        if( is_array( $lists ) && count( $lists) ) :
            
            // build subscriptions: array of list id's
            foreach( $lists as $list ) :
                $subscriptions[] = (int)$list->ID;
            endforeach;
        elseif( is_numeric( $lists ) ) :
            
            // single result returned
            $subscriptions[] = $lists;
        endif;
        
    endif;

    return (array)$subscriptions;
}

// 6.4
// hint: transform result to json string
function slb_return_json( $php_array ) { 
    
    // encode result as json string
    $json_result = json_encode( $php_array );
 
    // return result
    die( $json_result );

    // stop all other processing
    exit;    
}

// 6.5
// hint: gets the unique act field key from the field name 
function slb_get_acf_key( $field_name ) {
    
 $field_key = $field_name;

    switch( $field_name ) {

        case 'slb_fname' :
            $field_key = 'field_625986f2d899d';
            break;
        case 'slb_lname' :
            $field_key = 'field_6259873cd899e';
            break;
        case 'slb_email' :
            $field_key = 'field_625987ab273b4';
            break;
        case 'slb_subscriptions' :
            $field_key = 'field_62598806273b5';
            break;
        case 'slb_enable_reward' :
            $field_key = 'field_626b5ea4134fe';
            break;
        case 'slb_reward_title' :
            $field_key = 'field_626b5fb9134ff';
            break;
        case 'slb_reward_file' :
            $field_key = 'field_626b605ce70c2';
            break;
    }
    
    return $field_key;
}

// 6.6
// hint: returns an array of subscriber data including subscriptions
function slb_get_subscriber_data( $subscriber_id ) {
    
    // setup subscriber_data
    $subscriber_data = array();

    // get subscriber object
    $subscriber = get_post( $subscriber_id );

    // if subscriber object is valid
    if ( isset( $subscriber->post_type ) && $subscriber->post_type == 'slb_subscriber' ) :

        $fname = get_field( slb_get_acf_key( 'slb_fname'), $subscriber_id);
        $lname = get_field( slb_get_acf_key( 'slb_lname'), $subscriber_id);
    
        // build subscriber data for return
        $subscriber_data = array(
            'name' => $fname . ' ' . $lname,
            'fname' => $fname,
            'lname' => $lname,
            'email' => get_field( slb_get_acf_key( 'slb_email' ), $subscriber_id),
            'subscriptions' => slb_get_subscriptions( $subscriber_id )
        );
        
    endif;

    // return subscriber_data
    return $subscriber_data;
}

// 6.7
// hint: returns html for a page selector
function slb_get_page_select( $input_name = "slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="") {
 
    // get WP pages
    $pages = get_pages(
        array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'parent' => $parent,
            'status' => array('draft','publish'),
        )
    );

    // setup our select html
    $select = '<select name="' . $input_name . '" ';

    // if $input_id was passed in
    if ( strlen( $input_id ) ) :
        
        // add an input id to our select html
        $select .= 'id="' . $input_id . '" '; 
    endif;

    // setup our first option
    $select .= '><option value="">- Select One -</option>';

    // loop over all the pages
    foreach ( $pages as &$page ) :
         
        // get the page id as our default option value
        $value = $page->ID;

        // determine which page attribute is the desired value field
        switch( $value_field ) {
            case 'slug' :
                $value = $page->post_name;  
                break;
            case 'url' :
                $value = get_page_link( $page->ID );
                break;
            default:
                $value = $page->ID;
        }

        // check if this option is the currently selected option
        $selected = '';
        if( $selected_value == $value ) :
            $selected = ' selected="selected" ';
        endif;

        // build our option html
        $option = '<option value="' . $value . '" '. $selected . '>';
        $option .= $page->post_title;
        $option .='</option>';

        // append our option to the select html
        $select .= $option;
        
    endforeach;
        
    // close our  select html tag
    $select .= '</select>';

    // return our new select
    return $select;
}

// 6.8
// hint: returns default option value as an associative array
function slb_get_default_options() {
    
    $defaults = array();

    try {
        
        // get front page id
        $front_page_id = get_option( 'page_on_front');

        // setup default email footer
        $default_email_footer = '
            <p>
                Sincerely, <br/><br/>
                The ' . get_bloginfo( 'name' ) . ' Team<br/>
                <a href="' . get_bloginfo( 'url') . '">' . get_bloginfo( 'url') . '</a>
            </p>
        ';

        // setup defaults array
        $defaults = array(
          'slb_manage_subscription_page_id' => $front_page_id,
          'slb_confirmation_page_id' => $front_page_id,
          'slb_reward_page_id' => $front_page_id,
          'slb_default_email_footer' => $default_email_footer,
          'slb_download_limit' => 3,
        ); 

    } catch( Exception $e ) {
        
        // php error
    }

    // return defaults
    return $defaults;
}

// 6.9
// hint: returns the requested page option value or it's default
function slb_get_option( $option_name ) {
    
    // setup our return variable
    $option_value = '';

    try {

        // get default option values
        $defaults = slb_get_default_options();

        // get the requested option
        switch( $option_name ) {
            
            case 'slb_manage_subscription_page_id' :
                // subscription page id
                $option_value = ( get_option('slb_manage_subscription_page_id')) ? 
                get_option('slb_manage_subscription_page_id') : $defaults[ 'slb_manage_subscription_page_id'];
                break;
            case 'slb_confirmation_page_id' :
                // confirmation page id
                $option_value = ( get_option('slb_confirmation_page_id')) ? 
                get_option('slb_confirmation_page_id') : $defaults[ 'slb_confirmation_page_id'];
                break;
            case 'slb_reward_page_id' :
                // reward page id
                $option_value = ( get_option('slb_reward_page_id')) ? 
                get_option('slb_reward_page_id') : $defaults[ 'slb_reward_page_id'];
                break;
            case 'slb_default_email_footer' :
                // email footer
                $option_value = ( get_option('slb_default_email_footer')) ? 
                get_option('slb_default_email_footer') : $defaults[ 'slb_default_email_footer'];
                break;
            case 'slb_download_limit' :
                // reward download limit
                $option_value = ( get_option('slb_download_limit')) ? 
                get_option('slb_download_limit') : $defaults[ 'slb_download_limit'];
                break;
        }
        
    } catch( Exception $e ) {
            
    }
    
    // return option value or it's default
    return $option_value;
}

// 6.10
// hint: gets the current options and returns values in associative array
function slb_get_current_options() {

    // setup our return variable
    $current_options = array();

    try {
        
        // build our current options associative array
        $current_options = array(
              'slb_manage_subscription_page_id' => slb_get_option('slb_manage_subscription_page_id'),
              'slb_confirmation_page_id' => slb_get_option('slb_confirmation_page_id'),
              'slb_reward_page_id' => slb_get_option('slb_reward_page_id'),
              'slb_default_email_footer' => slb_get_option('slb_default_email_footer'),
              'slb_download_limit' => slb_get_option('slb_download_limit'),
        );
    } catch( Exception $e ) {
        
        // php error
    }

    // return current options
    return $current_options;
}

// 6.11
// hint: generates an html for managing subscriptions
function slb_get_manage_subscriptions_html( $subscriber_id) {
    
    $output = '';
    
    try {

        // get array of list_ids for this subscriber
        $lists = slb_get_subscriptions( $subscriber_id );

        // get the subscriber data
        $subscriber_data = slb_get_subscriber_data( $subscriber_id );

        // set the title
        $title = $subscriber_data['fname'] .'\'s Subscriptions';

        $nonce = wp_nonce_field( 'slb-update-subscriptions_' . $subscriber_id, '_wpnonce', true, false);

        // build out output html
        $output = '
            <form id="slb_manage_subscriptions_form" class="slb-form" method="post"
            action="/wp-admin/admin-ajax.php?action=slb_unsubscribe">

            ' . $nonce . '
            
            <input type="hidden" name="subscriber_id" value="' . $subscriber_id . '">
            
            <h3 class="slb-title">'. $title .'</h3>';

            if ( !count( $lists ) ) :
                
                $output .= "<p>There are no active subscriptions.</p>";

            else :
                
                $output .= '<table>
                    <tbody>';
                    
                    // loop over lists
                    foreach ( $lists as &$list_id ):
                    
                        $list_object = get_post( $list_id );    

                        $output .= '<tr>
                            <td>' .
                                $list_object->post_title .
                            '</td>
                            <td>
                                <label>
                                    <input type="checkbox" name="list_ids[]" value="' . $list_object->ID .'"/> UNSUBSCRIBE
                                </label>
                            </td>
                        </tr>';

                    endforeach;
                    
                    unset( $list_id); // break the reference with the last element
                
                // close up our output html <tbody>
                $output .= '</tbody>
                </table>
                
                <p><input type="submit" value="Save Changes" /></p>';
            endif;

            $output .= '</form>';
    
    } catch ( Exception $e) {
        
    }

    // return output
    return $output;
}

// 6.12
// hint: returns an array of email template data IF the template exists
function slb_get_email_template( $subscriber_id, $email_template_name, $list_id ) {

    // setup return variable
    $template_data = array();

    // create new array to store email templates
    $email_templates = array();

    // get list object
    $list = get_post( $list_id );

    // get subscriber object
    $subscriber = get_post( $subscriber_id );

    if( !slb_validate_list( $list ) || !slb_validate_subscriber( $subscriber ) ) :

        // the list or the subscriber is not valid

    else :

        // get subscriber data
        $subscriber_data = slb_get_subscriber_data( $subscriber_id );

        // get unique manage subscription link
        $manage_subscriptions_link = slb_get_manage_subscriptions_link( $subscriber_data[ 'email' ], $list_id );
        
        // get default email header
        $default_email_header = '
            <p>
                Hello, '. $subscriber_data[ 'fname' ] . ' 
            </p>
        ';

        // get default email footer
        $default_email_footer =slb_get_option( 'slb_default_email_footer' );

        // setup unsubscribe text
        $unsubscribe_text = '
            <br /><br />
            <hr />
            <p><a href="' . $manage_subscriptions_link .'">Click here to unsubscribe</a> from this or any other email list.</p>';
            
        // get reward
        $reward = slb_get_list_reward( $list_id );

        // setup reward text
        $reward_text = '';       

        // if reward exists
        if( $reward !== false ) :

            // setup the appropriate reward text
            switch( $email_template_name ) {
                
                case 'new_subscription' :
                    // set reward text
                    $reward_text = '<p>After confirming your subscription, we will send you a link for a FREE DOWNLOAD of '.
                    $reward['title'] . '.</p>';
                    break;
                case 'subscription_confirmed' :
                    // get download limit
                    $download_limit = slb_get_option( 'slb_download_limit' );
                    // generate new download link
                    $download_link = slb_get_reward_link( $subscriber_id, $list_id );
                    // set the reward text
                    $reward_text = '<p>Here is your <a href="'. $download_link .'">UNIQUE DOWNLOAD LINK</a> for ' . 
                    $reward['title'] . '. This link will expire after '. $download_limit  .' downloads.</p>';
                    break;
            }
            
        endif;

        // setup email templates

            // get unique opt-in link
            $optin_link = slb_get_optin_link( $subscriber_data[ 'email' ], $list_id );

            // template: new_subscription
            $email_templates[ 'new_subscription' ] = array(
              'subject' => 'Thank you for subscribing to ' . $list->post_title . '! Please confirm your subscription.',
              'body' => '
                    ' . $default_email_header . '
                    <p>Thank you for subscribing to ' . $list->post_title .'!</p>
                    <p>Please <a href="' . $optin_link . '">click here to confirm your subscription.</a></p>
                    ' . $reward_text . $default_email_footer . $unsubscribe_text,
            );

            // template: subscription_confirmed
            $email_templates[ 'subscription_confirmed' ] = array(
              'subject' => 'You are now subscribed to ' . $list->post_title . '!',
              'body' => '
                    ' . $default_email_header . '
                    <p>Thank you for confirming your subscription. You are now subscribed to ' . $list->post_title .'!</p>
                    ' . $reward_text . $default_email_footer . $unsubscribe_text,
            );
                     
    endif;

    // if the requested email template exists
    if ( isset( $email_templates[ $email_template_name] ) ) :

        // add template data to return variable
        $template_data = $email_templates[ $email_template_name ];
        
    endif;

    // return template_data
    return $template_data;
    
}

// 6.13 
// hint: validates whether the post object exists and that it's a validate list post_type
function slb_validate_list( $list_object ) {
    
    $list_valid = false;

    if ( isset( $list_object->post_type) && $list_object->post_type == 'slb_list' ) :

        $list_valid = true;
    
    endif;

    return $list_valid;
}

// 6.14
// hint: validates whether the post object exists and that it's a validate subscriber post_type
function slb_validate_subscriber( $subscriber_object ) {
    
    $subscriber_valid = false;

    if ( isset( $subscriber_object->post_type) && $subscriber_object->post_type == 'slb_subscriber' ) :

        $subscriber_valid = true;
    
    endif;

    return $subscriber_valid;
}

// 6.15
// hint: returns a unique link for managing a particular users subscriptions
function slb_get_manage_subscriptions_link( $email, $list_id=0 ) {
    
    $link_href = '';

    try {

        $page = get_post( slb_get_option( 'slb_manage_subscription_page_id') );
        $slug = $page->post_name;

        $permalink = get_permalink( $page );

        // get character to start query string
        $startquery = slb_get_querystring_start( $permalink );

        $link_href = $permalink . $startquery . 'email=' . urlencode($email) . '&list=' . $list_id;
    
    } catch( Exception $e ) {
    
        //$link_href = $e->getMessage();
    }

    return esc_url( $link_href );
}

// 6.16
// hint: returns the appropriate character for the begining of a querystring
function slb_get_querystring_start( $permalink ) { 
    
    // setup our default return variable
    $querystring_start = '&';

    // if ? is not found in the permalink
    if ( strpos( $permalink, '?') == false ) :
        $querystring_start = '?';
    endif;

    return $querystring_start;
}

// 6.17
// hint: returns a unique link for opting into an email list
function slb_get_optin_link( $email, $list_id=0 ) {
    
    $link_href = '';

    try {

        $page = get_post( slb_get_option( 'slb_confirmation_page_id') );
        $slug = $page->post_name;
        $permalink = get_permalink( $page );

        // get character to start query string
        $startquery = slb_get_querystring_start( $permalink );

        $link_href = $permalink . $startquery . 'email=' . urlencode( $email ) . '&list=' . $list_id;

    } catch ( Exception $e) {
        
        // $link_href = $e-> getMessage();
    }

    return esc_url( $link_href );
}

// 6.18
// hint: returns html for message
function slb_get_message_html( $message, $message_type ) {
    
    $output = '';
    
    try {
        
        $message_class  ='confirmation';
        
        switch( $message_type ) {
            case 'warning':
                $message_class = 'slb-warning';
                break;
            case 'error':
                $message_class = 'slb-error';
                break;
            default :
                $message_class = 'slb-confirmation';
                break;
        }
        
        $output .= '
            <div class="slb-message-container">
                <div class="slb-message '. $message_class . '">
                    <p>' . $message . '</p>
                </div>
            </div>';
            
    } catch ( Exception $e ) {
        
    }

    return $output;
}

// 6.19
// hint: returns false if list has no reward or returns the object containing file and title if it does
function slb_get_list_reward( $list_id ) {
    
    // setup return data
    $reward_data = false;

    // get enable_reward value
    $enable_reward = ( get_field( slb_get_acf_key('slb_enable_reward'), $list_id ) ) ? true : false;

    // if reward is enabled for this list
    if ( $enable_reward ) :
        
        // get reward file
        $reward_file = ( get_field( slb_get_acf_key('slb_reward_file'), $list_id)) ? 
            get_field( slb_get_acf_key( 'slb_reward_file'), $list_id) : false;

        // get reward title
        $reward_title = ( get_field( slb_get_acf_key('slb_reward_title'), $list_id)) ? 
            get_field( slb_get_acf_key( 'slb_reward_title'), $list_id) : 'Reward';

        
        // if reward_file is a valid array
        if( is_array( $reward_file )) :
            
            // setup return data
            $reward_data = array(
              'file' => $reward_file,
              'title' => $reward_title, 
            );
        endif;
        
    endif;

    // return $reward_data
    return $reward_data;
}

// 6.20
// hint: returns a unique link for downloading a reward file
function slb_get_reward_link( $subscriber_id, $list_id ) {
    
    $link_href = '';

    try {
        
        $page = get_post(  slb_get_option( 'slb_reward_page_id' ) );
        $slug = $page->post_name;
        $permalink = get_permalink( $page );

        // generate unique uid for reward link
        $uid = slb_generate_reward_uid( $subscriber_id, $list_id );

        // get list reward
        $reward = slb_get_list_reward( $list_id );

        // if an attachment id was returned
        if( $uid && $reward !== false ) :
        
            // add reward link to database
            $link_added = slb_add_reward_link( $uid, $subscriber_id, $list_id, $reward['file']['id'] );

            // if link was added successfully
            if( $link_added === true ) :
                
                // get character to start querystring
                $startquery = slb_get_querystring_start( $permalink );

                // build reward link
                $link_href = $permalink . $startquery . 'reward=' . urldecode( $uid );
                
            endif;
            
        endif;
        
    } catch ( Exception $e ) {
        
        // $link_href = $e->getMessage();
    }

    // return reward link
    return esc_url( $link_href );
}

// 6.21
// hint: generates a unique number
function slb_generate_reward_uid( $subscriber_id, $list_id ) {
    
    // setup our return variable
    $uid = '';
    
    // get subscriber post object
    $subscriber = get_post( $subscriber_id );

    // get list post object
    $list = get_post( $list_id );
    
    // if subscriber and list are valid
    if ( slb_validate_subscriber( $subscriber ) && slb_validate_list( $list ) ) :
        
        // get list reward
        $reward = slb_get_list_reward( $list_id );

        // if reward is not equal to false
        if( $reward !== false ) :
            
            // generate a unique id
            $uid = uniqid( 'slb', true );
            
        endif;
        
    endif;

    return $uid;
}

// 6.22
// hint: return false if list has no reward or returns the object containing file and title if it does
function slb_get_reward( $uid ) {

    global $wpdb;
    
    // setup return data
    $reward_data = false;

    // reward links download table name
    $table_name = $wpdb->prefix . "slb_reward_links";

    // get list id from reward link
    $list_id = $wpdb->get_var(
        $wpdb->prepare(
            "
              SELECT list_id
              FROM $table_name
              WHERE uid = %s
            ",
            $uid
        )
    );
    
    // get downloads from reward link
    $downloads = $wpdb->get_var(
        $wpdb->prepare(
            "
              SELECT downloads
              FROM $table_name
              WHERE uid = %s
            ",
            $uid
        )
    );

    // get reward data
    $reward = slb_get_list_reward( $list_id );

    // if reward was found
    if( $reward !== false) :

        // set reward data
        $reward_data = $reward;

        // add dowloads to reward data
        $reward_data['downloads'] = $downloads;
        
    endif;

    // return $reward_data
    return $reward_data;
}

// 6.23
// hint: returns an array of subscriber_id's
function slb_get_list_subscribers( $list_id = 0) {
    
    // setup return variable
    $subscribers = false;

    // get list object
    $list = get_post( $list_id );
    
    if( slb_validate_list( $list ) ) :
        
        // query all subscribers from post this list only
        $subscribers_query = new WP_QUERY(
          array(
              'post_type' => 'slb_subscriber',
              'published' => true,
              'posts_per_page' => -1,
              'orderby' => 'post_date',
              'order' => 'DESC',
              'status' => 'publish',
              'meta_query' => array(
                  array(
                      'key' => 'slb_subscriptions',
                      'value' => ':"' . $list->ID. '"',
                      'compare' => 'LIKE'
                  )
              )
          )  
        );
        
    elseif ( $list_id == 0) :

        // query all subscribers from all lists
        $subscribers_query = new WP_QUERY(
          array(
              'post_type' => 'slb_subscriber',
              'published' => true,
              'posts_per_page' => -1,
              'orderby' => 'post_date',
              'order' => 'DESC',
          )  
        );
    
    endif;

    // if $subscriber_query isset and query returns results
    if ( isset( $subscribers_query) && $subscribers_query->have_posts() ) :

        // set subscriber array
        $subscribers = array();

        // loop over results
        while ( $subscribers_query->have_posts() ):

            // get the post object
            $subscribers_query->the_post();

            $post_id = get_the_ID();

            // append result to subscribers array
            array_push( $subscribers, $post_id );
            
        endwhile;
        
    endif;

    // reset wp query/postdata
    wp_reset_query();
    wp_reset_postdata();

    return $subscribers;
}

// 6.24
// hint: returns the amount of subscribers in the list
function slb_get_list_subscriber_count( $list_id=0 ) {
    
    // setup return variable
    $count = 0;

    // get array of subscribers ids
    $subscribers = slb_get_list_subscribers( $list_id );

    // if array was returned
    if ( $subscribers !== false ) :

        // update count
        $count = count( $subscribers );

    endif;
    
    // return result
    return $count;
}

// 6.25
// hint: returns a unique link for downloading a subscribers csv
function slb_get_export_link( $list_id=0 ) {
    
    $link_href = 'admin-ajax.php?action=slb_download_subscribers_csv&list_id=' . $list_id;

    // return unique download link
    return esc_url($link_href);
    
} 

// 6.26
// hint: converts csv file into an array
function slb_csv_to_array( $filename='', $delimiter=',') {

    // this is an important setting!
    ini_set( 'auto_detect_line_endings', true );

    // if the file doesn't exist or the file is not readable return false
    if( !file_exists( $filename ) || !is_readable( $filename ))
        return FALSE;

    // setup our return data
    $return_data = array();
    
    // if we can open and read the file
    if (($handle = fopen($filename, "r")) !== FALSE ) {
        
        $row = 0;

        // while data exists loop over data
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
            // count the number of items in this data
            $num = count($data);
            // increment our row variable
            $row++;
            // setup our row data array
            $row_data = array();
            // loop over all items and append them to our row data
            for ($c=0; $c < $num; $c++) {
                // if this is the first row set it up as our header
                if( $row == 1) :
                    $header[] = $data[$c];
                else :
                    // all rows greater than 1
                    // add row data item
                    $return_data[$row-2][$header[$c]] = $data[$c];
                endif;
            }
        }

        // close our file
        fclose($handle);
    }

    // return the new data as a php array
    return $return_data;
} 

// 6.27
// hint: returns html formatted for WP admin notices
function slb_get_admin_notice( $message, $class) {
    
    // setup our return variable
    $output = '';

    try {
        
        // create output html
        $output = '
            <div class="' . $class . '">
                <p>' . $message . '</p>
            </div>
        ';
        
    } catch ( Exception $e ) {
        
        // php error
    }

    // return output
    return $output;
}

// 6.28
// hint: get's an array of plugin option data (group and settings) so as to save it all in one place
function slb_get_options_settings() {
    
    // setup our return data
    $settings = array(
        'group' => 'slb_plugin_options',
        'settings' => array(
            'slb_manage_subscription_page_id',
            'slb_confirmation_page_id',
            'slb_reward_page_id',
            'slb_default_email_footer',
            'slb_download_limit',
        ),
    );

    // return option data
    return $settings;
}


/* !7. CUSTOM POST TYPES */

// 7.1 
// subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_subscriber.php');

// 7.2 
// lists
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_list.php');


/* !8. ADMIN PAGES */

// 8.1
// hint: dashboard admin page
function slb_dashboard_admin_page() {

    // get our export link
    $export_href = slb_get_export_link();

    $output = '
        <div class="wrap">

            <h2>Snappy List Builder</h2>

            <p>The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom
    download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv</p>

            <p><a href="' . $export_href . '" class="button button-primary">Export All Subscriber Data</a></p>

        </div>';

    echo $output;
}

// 8.2 
// hint: import subscribers admin page
function slb_import_admin_page() {

    // enqueue special scripts required for our file import field
    wp_enqueue_media();

    echo ('
        
        <div class="wrap" id="import_subscribers">
            
            <h2>Import Subscribers</h2>

            <form id="import_form_1">
            
                <table class="form-table">
                
                    <tbody>
                    
                        <tr>
                            <th scope="row"><label for="slb_import_file">Import CSV</label></th>
                            <td>

                                <div class="wp-uploader">
                                    <input type="text" name="slb_import_file_url" class="file-url regular-text" accept="csv">
                                    <input type="hidden" name="slb_import_file_id" class="file-id" value="0">
                                    <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload">
                                </div>
                                
                                <p class="description" id="slb_import_file-description">Expects a CSV file containing a 
                                "Name" (First, Last, or Full) and "Email Address".</p>
                            </td>
                        </tr>
                        
                    </tbody>
                    
                </table>
                
            </form>

            <form id="import_form_2" method="post" action="/wp-admin/admin-ajax.php?action=slb_import_subscribers">
            
                <table class="form-table">
                
                    <tbody class="slb-dynamic-content">
                    
                    </tbody>

                    <tbody class="form-table show-only-on-valid" style="display: none">

                        <tr>
                            <th scope="row"><label>Import To List</label></th>
                            <td>
                                <select name="slb_import_list_id">');

                                    // get all our email lists
                                    $lists = get_posts(
                                        array(
                                            'post_type'     => 'slb_list',
                                            'status'        => 'publish',
                                            'post_per_page' => -1,
                                            'orderby'       => 'post_title',
                                            'order'         => 'ASC',        
                                        )
                                    );

                                    // loop over each email list
                                    foreach( $lists as &$list ):
                                    
                                        // create the select option for that list
                                        $option = '
                                            <option value="' . $list->ID . '">
                                            ' . $list->post_title . '
                                            </option>';

                                        // echo the new option
                                        echo $option;
                                            
                                    endforeach;
                                
                                
                                echo(' </select>
                                <p class="description"></p>
                            </td>
                        </tr>
                    
                    </tbody>
                
                </table>

                <p class="submit show-only-on-valid" style="display:none"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import"></p>
                
            </form>
            
        </div>
    ');
}

// 8.3 
// hint: plugin options admin page
function slb_options_admin_page() {

    // get the default values for our options
    $options = slb_get_current_options();
   
    echo('<div class="wrap">

        <h2>Snappy List Builder Options</h2>

        <form action="options.php" method="post">');

            // outputs a unique nounce for our plugin options
            settings_fields('slb_plugin_options');

            // generates a unique hidden field with our form handling url
            do_settings_fields('slb_plugin_options', '');
        
           echo('
           
           <table class="form-table">
                <tbody>
                    
                    <tr>
                        <th scope="row"><label for="slb_manage_subscription_page_id">Manage Subscription Page</label></th>
                        <td>
                            '. slb_get_page_select( 'slb_manage_subscription_page_id', 'slb_manage_subsciption_page_id', 0, 'id', $options['slb_manage_subscription_page_id']) . '
                            
                            <p class="description" id="slb_manage_subscription_page_id-description">This is the page where Snappy List 
                            Builder will send subscribers to confirm their subscriptions. <br/>
                            IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_manage_subscriptions]</strong>.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="slb_confirmation_page_id">Opt-In Page</label></th>
                        <td>
                            '. slb_get_page_select( 'slb_confirmation_page_id', 'slb_confirmation_page_id', 0, 'id', $options['slb_confirmation_page_id']) . '
                            
                            <p class="description" id="slb_confirmation_page_id-description">This is the page where Snappy List 
                            Builder will send subscribers to confirm their subscriptions. <br/>
                            IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_confirm_subscription]</strong>.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="slb_reward_page_id">Download Reward Page</label></th>
                        <td>
                            '. slb_get_page_select( 'slb_reward_page_id', 'slb_reward_page_id', 0, 'id', $options['slb_reward_page_id']) . '
                            
                            <p class="description" id="slb_reward_page_id-description">This is the page where Snappy List 
                            Builder will send subscribers to retrieve their reward downloads. <br/>
                            IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_download_reward]</strong>.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="slb_default_email_footer">Email Footer</label></th>
                        <td>');
                        
                            // wp_editor will act funny if it's stored in a string, so we run it like this...
                            wp_editor( $options['slb_default_email_footer'], 'slb_default_email_footer', array( 'textarea_rows'=>8 ) );
                            
                        echo('<p class="description" id="slb_default_email_footer-description">The default text that appears
                        at the end of email generated by this plugin.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="slb_download_limit">Reward Download Limit</label></th>
                        <td>
                            <input type="number" name="slb_download_limit" value="' . $options['slb_download_limit'] . '" class="" />
                            <p class="description" id="slb_download_limit-description">The amount of download a reward link
                            will allow before expiring.</p>
                        </td>
                    </tr>
                    
                </tbody>
            </table>');

            // outputs the WP submit button HTML
            @submit_button();
            
            /*<p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </p>*/
        echo('</form>
    </div>');
}


/* !9. SETTINGS */

// 9.1 
// hint: registers all our plugin options
function slb_register_options() {

    // get plugin options settings
    $options = slb_get_options_settings();

    // loop over settings
    foreach( $options['settings'] as $setting ) :

        // register this setting
        register_setting( $option['group'], $setting);
    endforeach;
}



/* !10. MISC */

// 10.1
// hint: add subscriber metaboxes to the admin page
function slb_add_subscriber_metaboxes( $post ) {
    
    add_meta_box(
        'slb-subscriber-details', // ID
        'Subscriber Details', // Title
        'slb_subscriber_metabox', // a Function
        'slb_subscriber', // Post type
        'normal', // Priority
        'default' // Styling
    );
}

//add_action( 'add_meta_boxes_slb_subscriber', 'slb_add_subscriber_metaboxes');

// 10.2
// hint: fill in data for the subscriber
function slb_subscriber_metabox() {

    global $post;

    $post_id = $post->ID;
    
    wp_nonce_field( basename( __FILE__ ), 'slb_subscriber_nonce');

    $first_name = (!empty(get_post_meta($post_id, 'slb_first_name', true))) ? get_post_meta( $post_id, 'slb_first_name', true) : '';
    $last_name = (!empty(get_post_meta($post_id, 'slb_last_name', true))) ? get_post_meta( $post_id, 'slb_last_name', true) : '';
    $email = (!empty(get_post_meta($post_id, 'slb_email', true))) ? get_post_meta( $post_id, 'slb_email', true) : '';
    $lists = (!empty(get_post_meta($post_id, 'slb_list', false))) ? get_post_meta( $post_id, 'slb_list', false) : [];

    /*
    echo '<br/>'. $first_name;
    echo '<br/>'. $last_name;
    echo '<br/>'. $email;
    echo '<br/>'. var_dump($lists);
    exit;
    */
?>

<style>
.slb-field-row {
    display: flex;
    flex-flow: row nowrap;
    flex: 1 1;
}

.slb-field-container {
    position: relative;
    flex: 1 1;
    margin-right: 1em;
}

.slb-field-container label {
    font-weight: bold;
}

.slb-field-container label span {
    color: red;
}

.slb-field-container ul {
    list-style: none;
    margin-top: 0;
}

.slb-field-container ul {
    font-weight: normal;
}
</style>
<div class="slb-field-row">
    <div class="slb-field-container">
        <p>
            <label for="fName">First Name <span>*</span></label><br />
            <input type="text" name="slb_first_name" id="fName" required="required" class="widefat"
                value="<?php echo $first_name; ?>">
        </p>
    </div>

    <div class=" slb-field-container">
        <p>
            <label for="lName">Last Name <span>*</span></label><br />
            <input type="text" name="slb_last_name" id="lName" required="required" class="widefat"
                value="<?php echo $last_name; ?>">
        </p>
    </div>
</div>

<div class=" slb-field-row">
    <div class="slb-field-container">
        <p>
            <label for="email">Email Address <span>*</span></label><br />
            <input type="email" name="slb_email" id="email" required="required" class="widefat"
                value="<?php echo $email; ?>">
        </p>
    </div>
</div>

<div class="slb-field-row">
    <div class="slb-field-container">
        <label>Lists</label><br />
        <ul>

            <?php
                global $wpdb; // pull in the WordPress database object
                
                $list_query = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='slb_list' AND post_status IN ('draft', 'publish')");

                if( !is_null( $list_query )) {
                    
                    foreach( $list_query as $list ) {

                        $checked = ( in_array($list->ID, $lists) ) ? 'checked="checked"' : '';
                        
                        echo '<li><label><input type="checkbox" name="slb_list[]" value="'. $list->ID .'"'. $checked .' />'. $list->post_title .'</label></li>';
                    }
                }
            ?>
        </ul>
    </div>

</div>
</div>
<?php
}

function slb_save_slb_subscriber_meta( $post_id, $post) {
    
    // Verify nonce
    if( !isset($_POST['slb_subscriber_nonce']) || !wp_verify_nonce( $_POST['slb_subscriber_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }

    // Get the post type object
    $post_type = get_post_type_object( $post->post_type );

    // Check if the current user as permission to edit the post
    if ( !current_user_can( $post_type->cap->edit_post, $post_id)) {
        return $post_id;
    }

    // Get the posted data and sanitize it
    $first_name = ( isset($_POST['slb_first_name'])) ? sanitize_text_field( $_POST['slb_first_name'] )  : '';
    $last_name = ( isset($_POST['slb_last_name'])) ? sanitize_text_field( $_POST['slb_last_name'] )  : '';
    $email = ( isset($_POST['slb_email'])) ? sanitize_text_field( $_POST['slb_email'] )  : '';
    $lists = ( isset($_POST['slb_list']) && is_array($_POST['slb_list'])) ? (array)$_POST['slb_list'] : [];

    /*
    echo '<br/>'. $first_name;
    echo '<br/>'. $last_name;
    echo '<br/>'. $email;
    echo '<br/>'. var_dump($lists);
    exit;
    */
    
    // update post meta
    update_post_meta( $post_id, 'slb_first_name', $first_name);
    update_post_meta( $post_id, 'slb_last_name', $last_name);
    update_post_meta( $post_id, 'slb_email', $email );
    
    // delete the existing list meta for this post
    delete_post_meta( $post_id , 'slb_list' );

    // add new list meta
    if (!empty($lists)) {
        foreach( $lists as $index=>$list_id ) {
        
            // add list relational meta value
            add_post_meta( $post_id, 'slb_list', $list_id, false ); // NOTE: NOT unique meta key
        }
    }
}

add_action('save_post', 'slb_save_slb_subscriber_meta', 10, 2);

// add action to change post title
function slb_edit_post_change_title(){
    
    global $post;
    
    if ($post->post_type == 'slb_subscriber') {
        
        add_filter(
            'the_title',
            'slb_subscriber_title',
            100,
            2
        );
    }
}

//add_action( 'admin_head-edit.php','slb_edit_post_change_title');

// function is called with in slb_edit_post_change_title to change post title to person's name
function slb_subscriber_title( $title, $post_id) {
    
    $new_title = get_post_meta( $post_id, 'slb_first_name', true) .' '. get_post_meta( $post_id, 'slb_last_name', true);

    return $new_title;
}

define('SLB_ACF_URL', plugin_dir_url( __FILE__) . '/lib/advanced-custom-fields/');

function slb_acf_settings_url( $url ) {
    return SLB_ACF_URL;
}

function slb_acf_show_admin( $show_admin ) {

    global $slb_show_acf_admin;
    return $slb_show_acf_admin;
}

function slb_older_acf_warning( $views ) {
    
    global $acf;
      
    $acf_ver = (float) $acf->settings[ 'version' ];
    $acf_ver_req = 5.12;

    if ( $acf_ver < $acf_ver_req ) {
        echo '<p style="color:red;">
            <strong>You\'re using an older version of the Advanced Custom Fields plugin (version: '. $acf_ver . ').<br/>
            Some features of Snappy List Builder may not work unless you update to version ' . $acf_ver_req  . ' or deactivate this plugin.</strong>
        </p>';
    }
    
    return $views;
    return $views;
}