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