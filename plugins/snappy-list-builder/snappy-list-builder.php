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
        
    2. SHORTCODES
        2.1 - slb_register_shortcodes() registers all our custom shortcodes 
        2.2 - slb_form_shortcode( $args, $content="" ) returns a html string for an email capture form

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
   
    6. HELPERS
        6.1 - slb_subscriber_has_subscription( $subscriber_id, $list_id ) returns true or false
        6.2 - slb_get_subscriber_id( $email ) retrieves a subscriber_id from an email address
        6.3 - slb_get_subscriptions( $subscriber_id ) returns an array of list_id's
        6.4 - slb_return_json( $php_array ) transform result to json string
        6.5 - slb_get_acf_key( $field_name ) gets the unique act field key from the field name
        6.6 - slb_get_subscriber_data( $subscriber_id ) returns an array of subscriber data including subscriptions

    7. CUSTOM POST TYPES
        7.1 - subscribers
        7.2 - lists

    8. ADMIN PAGES
        8.1 - slb_dashboard_admin_page() dashboard admin page
        8.2 - slb_import_admin_page() import subscribers admin page
        8.3 - slb_options_admin_page() plugin options admin page 

    9. SETTINGS

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

// 1.5
// hint: load external files to public website
add_action('wp_enqueue_scripts', 'slb_public_scripts');

// 1.6
// hint: Advance Custom Field settings (for version 5.8.9 or higher)
add_filter( 'acf/settings/url', 'slb_acf_settings_url');
add_filter( 'acf/settings/show_admin', 'slb_acf_show_admin');
add_action('views_edit-slb_subscriber', 'slb_older_acf_warning');
add_action('views_edit-slb_list', 'slb_older_acf_warning');

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

/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function slb_register_shortcodes() {
    add_shortcode( 'slb_form', 'slb_form_shortcode' );
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
        
            <form id="slb_form" name="slb_form" class="slb-form"
            action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post"> 

                <input type="hidden" name="slb_list" value="' . $list_id .'">';

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
        $list_id = (int)$_POST['slb_list'];
        
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
                    
                    // save new subscription
                    $subscription_saved = slb_add_subscription( $subscriber_id, $list_id );
                    
                    // if subscription was saved successfully
                    if( $subscription_saved ):
                        
                        // subscription saved!
                        $result[ 'status' ] = 1;
                        $result[ 'message' ] = 'Subscription saved';
                        
                    else :
                        // return detailed error
                        $result[ 'error' ] = 'Unable to save subscription.';
                        
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
    
 $field_key = field_name;

    switch( $field_name ) {

        case 'slb_fname' :
            $field_key = "field_625986f2d899d";
            break;
        case 'slb_lname' :
            $field_key = "field_6259873cd899e";
            break;
        case 'slb_email' :
            $field_key = "field_625987ab273b4";
            break;
        case 'slb_subscriptions' :
            $field_key = "field_62598806273b5";
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

    $output = '
        <div class="wrap">

            <h2>Snappy List Builder</h2>

            <p>The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom
    download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv</p>

        </div>';

    echo $output;
}

// 8.2 
// hint: import subscribers admin page
function slb_import_admin_page() {
   
    $output = '
        <div class="wrap">
        
            <h2>Import Subscribers</h2>

            <p>Page description... </p>
        </div>
    ';

    echo $output;
}

// 8.3 
// hint: plugin options admin page
function slb_options_admin_page() {
   
    $output = '
        <div class="wrap">
        
            <h2>Snappy List Builder Options</h2>

            <p>Page description... </p>
        </div>
    ';

    echo $output;
}


/* !9. SETTINGS */



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
}