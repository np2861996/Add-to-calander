<?php
/**
 * Plugin Name: Add to calander
 * Plugin URI: https://github.com/abcd/read-me-later
 * Description: This plugin allow you to add calander events
 * Version: 1.0.0
 * Author: Nikhil Patel
 * Text Domain: addtocalander
 * Author URI: https://github.com/efgh/
 * License: GPL3
 * PHP version 7.2
 * 
 * @category Calander
 * @package  Addtocalander
 * @author   Display Name <Nikhil@test.com>
 * @license  nikhil GPL3
 * @link     https://github.com/efgh
 **/

/*
// Documentation#

 Add plugin and activate it.

 add shortcut for button of Add to calander
 
 Example:
 [add_to_calendar_link 
 name="A Special Event" 
 begin="2023-03-04 07:30:00" 
 end="2023-12-10 08:40:00" 
 location="4RGV+WQG, Bhestan, Surat, Gujarat" 
 details="description" ]

 It Contains different arguments, please follow format stictly
 
 Example:

 name="A Special Event"

=> You can add any name.

begin="2023-07-17 16:00:00" #Add only UTC Time

=> Add satrt date in same formate date 

end="2023-12-10 19:10:00" #Add only UTC Time

=> Add end date in same formate date 

ctz="Asia/Kolkata"

=> Add timezone in same formate

location="612 Wharf Ave. Hoboken, New Jersey"

=> Add location or address

details="description" 

=> Add description as ou want.
*/

/**
 *  Implements Register_Rml_scripts.
 *
 * @return string
 **/
function Register_Rml_scripts()
{
    wp_register_script(
        'atc-script', 
        plugins_url('js/add-to-calander.js', __FILE__), array('jquery'), null, true
    );
    wp_register_style(
        'atc-style', 
        plugin_dir_url(__FILE__).'css/add-to-calander.css'
    );

    wp_enqueue_style('atc-style');
    wp_enqueue_script('atc-script');
}
    add_action('wp_enqueue_scripts',  'Register_Rml_scripts');   

 
/**
 * Implements Make_Add_To_Calendar_link.
 * 
 * @param createicsfile $atts comment about this variable
 *
 * @file
 *
 *                                 Description of what this
 *                                 module (or file) is doing.
 *
 * @return string
 * echo make_google_calendar_link("A Special Event", 1429518000, 1429561200, "612 
 * Wharf Ave. Hoboken, New Jersey", 
 * "Descriptions require imagination juice");
 **/
function Make_Add_To_Calendar_link($atts)
{
    $params = array('&dates=', '/', '&details=', '&location=', 
    '&ctz=', '&sf=true&output=xml');
    $url = 'https://www.google.com/calendar/render?action=TEMPLATE&text=';
    //$arg_list = func_get_args();
    $arg_list = array(0 => $atts["name"], 1 => $atts["begin"], 2 => $atts["end"], 
    3 => $atts["details"], 4 => $atts["location"], 5 => 'UTC' );
    // print_r($arg_list);

       
    for ($i = 0; $i < count($arg_list); $i++) {
        $current = $arg_list[$i];

        //if(is_int($current)) {
        if ($i == 1 || $i == 2) {    
            $current = date(strtotime($current));
            $t = new DateTime('@' . $current, new DateTimeZone('UTC'));
            $current = $t->format('Ymd\THis\Z');
            unset($t);
        } else {
               
            $current = $current;
        }
        $url .= (string) $current . $params[$i];
    }

    //ICS - Start
    $ictdatetimebegin = strtotime($atts["begin"]);
    $ictdatetimeend = strtotime($atts["end"]);
    $ictdatetimebegin = date('m d, Y  H:i:s', $ictdatetimebegin);
    $ictdatetimeend = date('m d, Y  H:i:s', $ictdatetimeend);

        

    if (array_key_exists('Applectc-button', $_POST)) {
        createicsfile(
            $atts["name"], $atts["begin"], $atts["end"], 
            $atts["details"], $atts["location"]
        );
    } else if (array_key_exists('Outlookctc-button', $_POST)) {
        createicsfile(
            $atts["name"], $atts["begin"], $atts["end"], 
            $atts["details"], $atts["location"]
        );
    }
        
    return ' <div class="calender-button">
                        <a title="Add to Calendar" class="addeventatc" 
                        href="javascript:void(0)" >Add to Calendar</a>
                        <div class="wrap">
                            <div class="btn"><a href="'.$url.'" 
                            target="_blank">Google</a></div>
                            <form method="post">
                                <input type="submit" name="Applectc-button"
                                        class="button ict-btn apple-bnt" 
                                        value="Apple" />
                                        <input type="submit" name="Outlookctc-button"
                                        class="button ict-btn outlook-bnt" 
                                        value="Outlook" />
                                        
                            </form>
                        </div>
                </div>';
       
}
add_shortcode('add_to_calendar_link', 'Make_Add_To_Calendar_link'); 
//Sample link, navigate to it while logged into your Google account
//If you aren't logged in, it should redirect properly upon login

/**
 * Implements createicsfile.
 * 
 * @param createicsfile $name      comment about this variable
 * @param createicsfile $datebegin comment about this variable
 * @param createicsfile $dateend   comment about this variable
 * @param createicsfile $details   comment about this variable
 * @param createicsfile $location  comment about this variable
 * 
 * @file
 *
 *                                 Description of what this
 *                                 module (or file) is doing.
 *
 * @return string
 * echo make_google_calendar_link("A Special Event", 1429518000, 1429561200, "612 
 * Wharf Ave. Hoboken, New Jersey", 
 * "Descriptions require imagination juice");
 **/
function createicsfile($name,$datebegin,$dateend,$details,$location)
{

    include 'ICS.php';

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=invite.ics');
        

    $ics = new ICS(
        array(
        'location' => $location,
        'description' => $details,
        'dtstart' => $datebegin,
        'dtend' => $dateend,
        'summary' => $name
        )
    );

        echo $ics->to_string($location);

}
?>
