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
        

    2. SHORTCODES
        2.1 - registers all our custom shortcodes slb_register_shortcodes()
        2.2 - returns a html string for an email capture form slb_form_shortcode()

    3. FILTERS
        3.1 - slb_subscriber_column_headers()
        3.2 - slb_subscriber_column_data()
        3.2.2 - slb_register_custom_admin_titles()
        3.2.3 - slb_custom_admin_titles()
        3.3 - slb_list_column_headers()
        3.4 - slb_list_column_data()    
         
    4. EXTERNAL SCRIPTS

    5. ACTIONS

    6. HELPERS

    7. CUSTOM POST TYPES

    8. ADMINN PAGES

    9. SETTINGS

    10. MISC.

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
    
    // setup our output variable - the form html
    $output = '
    
        <div class="slb">
        
            <form id="slb_form" name="slb_form" class="slb-form"
            action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post"> 

                <input type="hidden" name="slb_list" value="' . $list_id .'">
            
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

// 3.1 
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
// hint: handles custom admin title "title" column data for post types without titles
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
function slb_list_column_headers ( $columns ) {
    
    // creating custom column header data
    $columns = array (
        'cb' => '<input type="checkbox" />',
        'title' => __('List Name'),
    );

    // returning new columns names
    return $columns;
}


// 3.4
function slb_list_column_data ( $column, $post_id ) {
    
    // setup our return text
    $output = '';
    
    switch ( $column )  {
        
        case 'example' :
            /*
            // get the custom name data
            $fname = get_field( 'slb_fname', $post_id );
            $lname = get_field( 'slb_lname', $post_id );
            $output .= $fname . ' ' . $lname;
            */
            break;
    }

    // echo the output
    echo $output;
}



/* !4. EXTERNAL SCRIPTS */


/* !5. ACTIONS */

// 5.1 
// hint: saves subscription data to an existing or new subscriber
function slb_save_subscription() {
    
    // setup default result data
    $result = array(
        'status' => 0,
        'message' => 'Subscription was not saved',
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

        // attempt to create/save subscriber
        $subscriber_id = slb_save_subscriber( $subscriber_data );

        // if subscriber was saved successfully $subscriber_id will be greater than 0
        if ( $subscriber_id ) :
            
            // if subscriber already has this subscription
            if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ) :
                
                // get the list object
                $list = get_post( $list_id );

                // return detailed error
                $result['message'] .= esc_attr( $subscriber_data['email'] . ' is already subscribed to ' . $list->post_title . '.');

            else :
                
                // save new subscription
                $subscription_saved = slb_add_subscription( $subscriber_id, $list_id );
                
                // if subscription was saved successfully
                if( $subscription_saved ):
                    
                    // subscription saved!
                    $result[ 'status' ] = 1;
                    $result[ 'message' ] = 'Subscription saved';
                    
                endif;
                
            endif;
            
        endif;
        
    } catch ( Exception $e ) {
    
    }

    // return result as json
    slb_return_json( $result );
}


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

//add_action( 'add_meta_boxes_slb_subscriber', 'slb_add_subscriber_metaboxes');

function slb_subscriber_metabox() {

    global $post;

    $post_id = $post->ID;
    
    wp_nonce_field( basename( __FILE__ ), 'slb_subscriber_nonce');

    $first_name = (!empty(get_post_meta($post_id, 'slb_first_name', true))) ? get_post_meta( $post_id, 'slb_first_name', true) : '';
    $last_name = (!empty(get_post_meta($post_id, 'slb_last_name', true))) ? get_post_meta( $post_id, 'slb_last_name', true) : '';
    $email = (!empty(get_post_meta($post_id, 'slb_email', true))) ? get_post_meta( $post_id, 'slb_email', true  ) : '';
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