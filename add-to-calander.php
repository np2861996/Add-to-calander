<?php
/**
 * Plugin Name: Add to calander
 * Plugin URI: https://github.com/abcd/read-me-later
 * Description: This plugin allow you to add blog posts in read me later lists using Ajax
 * Version: 1.0.0
 * Author: Nikhil Patel
 * Author URI: https://github.com/efgh/
 * License: GPL3
 */

 /*
#Documentation#

 Add plugin and activate it.

 add shortcut for button of Add to calander
 
 Example:[google_calendar_link name="A Special Event" begin="2023-07-17 16:00:00" end="2023-12-10 19:10:00" ctz="Asia/Kolkata" location="612 Wharf Ave. Hoboken, New Jersey" details="description" ]

 It Contains different arguments, please follow format stictly
 
 Example:

 name="A Special Event"

=> You can add any name.

begin="2023-07-17 16:00:00"

=> Add satrt date in same formate date 

end="2023-12-10 19:10:00"

=> Add end date in same formate date 

ctz="Asia/Kolkata"

=> Add timezone in same formate

location="612 Wharf Ave. Hoboken, New Jersey"

=> Add location or address

details="description" 

=> Add description as ou want.
 */

 /**
    * Register plugin styles and scripts
    */
    function register_rml_scripts() {
        wp_register_script( 'atc-script', plugins_url( 'js/add-to-calander.js', __FILE__ ), array('jquery'), null, true );
        wp_register_style( 'atc-style', plugin_dir_url( __FILE__ ).'css/add-to-calander.css'  );

        wp_enqueue_style( 'atc-style' );
        wp_enqueue_script( 'atc-script' );
    }
    add_action( 'wp_enqueue_scripts',  'register_rml_scripts' );   

 
    //Call this with the shown parameters (make sure $time and $end are integers and in Unix timestamp format!)
    //Get a link that will open a new event in Google Calendar with those details pre-filled
    function make_google_calendar_link($atts) {
        $params = array('&dates=', '/', '&details=', '&location=', '&ctz=', '&sf=true&output=xml');
        $url = 'https://www.google.com/calendar/render?action=TEMPLATE&text=';
        //$arg_list = func_get_args();
        $arg_list = array(0 => $atts["name"], 1 => $atts["begin"], 2 => $atts["end"], 3 => $atts["details"], 4 => $atts["location"], 5 => $atts["ctz"] );
       // print_r($arg_list);
        for ($i = 0; $i < count($arg_list); $i++) {
            $current = $arg_list[$i];

            //if(is_int($current)) {
            if($i == 1 || $i == 2)
            {    
              $current = date('Ymd\THis',strtotime($current));
            }
            else {
               
                $current = $current;
            }
            $url .= (string) $current . $params[$i];
        }

        //ICS - Start
        $ictdatetimebegin = strtotime($atts["begin"]);
        $ictdatetimeend = strtotime($atts["end"]);
        $ictdatetimebegin = date('m d, Y  H:i:s',$ictdatetimebegin);
        $ictdatetimeend = date('m d, Y  H:i:s',$ictdatetimeend);

        { ?>
            <script type="text/javascript">
                /**
                    * Create and download a file on click
                    * @params {string} filename - The name of the file with the ending
                    * @params {string} filebody - The contents of the file
                    */
                    function download(filename, fileBody) {
                        var element = document.createElement('a');
                        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(fileBody));
                        element.setAttribute('download', filename);

                        element.style.display = 'none';
                        document.body.appendChild(element);

                        element.click();

                        document.body.removeChild(element);
                    }

                    /**
                    * Returns a date/time in ICS format
                    * @params {Object} dateTime - A date object you want to get the ICS format for.
                    * @returns {string} String with the date in ICS format
                    */
                    function convertToICSDate(dateTime) {
                        const year = dateTime.getFullYear().toString();
                        const month = (dateTime.getMonth() + 1) < 10 ? "0" + (dateTime.getMonth() + 1).toString() : (dateTime.getMonth() + 1).toString();
                        const day = dateTime.getDate() < 10 ? "0" + dateTime.getDate().toString() : dateTime.getDate().toString();
                        const hours = dateTime.getHours() < 10 ? "0" + dateTime.getHours().toString() : dateTime.getHours().toString();
                        const minutes = dateTime.getMinutes() < 10 ? "0" +dateTime.getMinutes().toString() : dateTime.getMinutes().toString();

                        return year + month + day + "T" + hours + minutes + "00";
                    }

                    /**
                    * Creates and downloads an ICS file
                    * @params {string} timeZone - In the format America/New_York
                    * @params {object} startTime - Vaild JS Date object in the event timezone
                    * @params {object} endTime - Vaild JS Date object in the event timezone
                    * @params {string} title
                    * @params {string} description
                    * @params {string} venueName
                    * @params {string} address
                    * @params {string} city
                    * @params {string} state
                    */
                    function createDownloadICSFile(timezone, startTime, endTime, title, description, venueName ) {
                        //body structure
                        const icsBody = 'BEGIN:VCALENDAR\n' +
                        'VERSION:2.0\n' +
                        'PRODID:Calendar\n' +
                        'CALSCALE:GREGORIAN\n' +
                        'METHOD:PUBLISH\n' +
                        'BEGIN:VTIMEZONE\n' +
                        'TZID:' + timezone + '\n' +
                        'END:VTIMEZONE\n' +
                        'BEGIN:VEVENT\n' +
                        'SUMMARY:' + title + '\n' +
                        'UID:@Default\n' +
                        'SEQUENCE:0\n' +
                        'STATUS:CONFIRMED\n' +
                        'TRANSP:TRANSPARENT\n' +
                        'DTSTART;TZID=' + timezone + ':' + convertToICSDate(startTime) + '\n' +
                        'DTEND;TZID=' + timezone + ':' + convertToICSDate(endTime)+ '\n' +
                        'DTSTAMP:'+ convertToICSDate(new Date()) + '\n' +
                        'LOCATION:' + venueName + '\n' +
                        'DESCRIPTION:' + description + '\n' +
                        'END:VEVENT\n' +
                        'END:VCALENDAR\n';

                        download(title + '.ics', icsBody);
                    }

                    function ict() {
                    //document.getElementById('downloadICS').addEventListener('click', () => {
                        createDownloadICSFile(
                            "<?php echo  $atts["ctz"] ?> ",
                            new Date( " <?php echo  $ictdatetimebegin ?> "),
                            new Date(" <?php echo  $ictdatetimeend ?> "),
                            "<?php echo  $atts["name"] ?> ",
                            "<?php echo  $atts["details"] ?> ",
                            "<?php echo  $atts["location"] ?> "
                        );  
                    };
        
            </script>
        
            <?php
        }

        return ' <div class="calender-button">
                        <a title="Add to Calendar" class="addeventatc" href="javascript:void(0)" >Add to Calendar</a>
                        <div class="wrap">
                            <div class="btn"><a href="'.$url.'" target="_blank">Google</a></div>
                            <div class="btn"><a href="javascript:void(0)" onclick="ict()" class="downloadICS" >Apple</a></div>
                            <div class="btn"><a href="javascript:void(0)" onclick="ict()" class="downloadICS" >Outlook</a></div>
                        </div>
                </div>';


       
    }
    add_shortcode('google_calendar_link', 'make_google_calendar_link'); 
    //Sample link, navigate to it while logged into your Google account
    //If you aren't logged in, it should redirect properly upon login
    //echo make_google_calendar_link("A Special Event", 1429518000, 1429561200, "612 Wharf Ave. Hoboken, New Jersey", "Descriptions require imagination juice");
   