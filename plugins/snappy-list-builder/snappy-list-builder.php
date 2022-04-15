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
        1.1 - registers all our custom shortcodes on init 

    2. SHORTCODES
        2.1 - registers all our custom shortcodes slb_register_shortcodes()
        2.2 - returns a html string for an email capture form slb_form_shortcode()

    3. FILTERS

    4. EXTERNAL SCRIPTS

    5. ACTIONS

    6. HELPERS

    7. CUSTOM POST TYPES

    8. ADMINN PAGES

    9. SETTINGS

    10. MISC.

*/

/* !1. HOOKS */
// hint: registers all our custom shortcodes on init
add_action( 'init', 'slb_register_shortcodes');

/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function slb_register_shortcodes() {
    add_shortcode( 'slb_form', 'slb_form_shortcode' );
}

// 2.2
// hint: returns a html string for an email capture form
function slb_form_shortcode( $args, $content="") {
    
    // setup our output variable - the form html
    $output = '
    
        <div class="slb">
        
            <form id="slb_form" name="slb_form" class="slb-form" method="post">
            
                <p class="slb-input-container">
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


/* !4. EXTERNAL SCRIPTS */


/* !5. ACTIONS */


/* !6. HELPERS */


/* !7. CUSTOM POST TYPES */


/* !8. ADMIN PAGES */



/* !9. SETTINGS */



/* !10. MISC */
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

add_action( 'add_meta_boxes_slb_subscriber', 'slb_add_subscriber_metaboxes');

function slb_subscriber_metabox() {
    
    wp_nonce_field( basename( __FILE__ ), 'slb_subscriber_nonce');
        
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
            <input type="text" name="slb_first_name" id="fName" required="required" class="widefat">
        </p>
    </div>

    <div class=" slb-field-container">
        <p>
            <label for="lName">Last Name <span>*</span></label><br />
            <input type="text" name="slb_last_name" id="lName" required="required" class="widefat">
        </p>
    </div>
</div>

<div class=" slb-field-row">
    <div class="slb-field-container">
        <p>
            <label for="email">Email Address <span>*</span></label><br />
            <input type="email" name="slb_email" id="email" required="required" class="widefat">
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
                        echo '<li><label><input type="checkbox" name="slb_list[]" value="'. $list->ID .'" />'. $list->post_title .'</label></li>';
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

    echo '<br/>'. $first_name;
    echo '<br/>'. $last_name;
    echo '<br/>'. $email;
    echo '<br/>'. $lists;
    exit;
    
}