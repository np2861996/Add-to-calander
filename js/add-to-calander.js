jQuery(document).ready( function($){

    $(function() {
        $(".calender-button a").on("click", function(e) {
           // $(this).parent().next(".card-reveal")
           // $(".calender-button .wrap").addClass("active");
           $(this).next(".wrap").addClass("active")
            e.stopPropagation()
        });
        $(document).on("click", function(e) {
            if ($(e.target).is(".calender-button .wrap") === false) {
            $(".calender-button .wrap").removeClass("active");
            }
        });
        });

})


jQuery(document).ready( function() {

    function removeTags(str) {
        if ((str===null) || (str===''))
            return false;
        else
            str = str.toString();
              
        // Regular expression to identify HTML tags in
        // the input string. Replacing the identified
        // HTML tag with a null string.
        return str.replace( /(<([^>]+)>)/ig, '');
    }

    jQuery(".add_to_calander_download").click( function(e) {
       e.preventDefault(); 
       location1 = jQuery(this).attr("data-location1");
       details = jQuery(this).attr("data-details");
       begin = jQuery(this).attr("data-begin");
       end = jQuery(this).attr("data-end");
       summary = jQuery(this).attr("data-name");
       
       createDownloadICSFile(
        'UTC',
         new Date( begin),
         new Date(end),
         summary,
         removeTags(details),
         location1
     );  

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

       

    });
 });