<?php
/**
 * Created by JetBrains PhpStorm.
 * User: davidkryzaniak
 * Date: 20/07/2013
 * Time: 11:55
 *
 * This will contain all the HTML/CSS for the plugin. It's separate so it can be overloaded.
 */
if (!class_exists('ListAuthorDisplay')) {

    class ListAuthorDisplay
    {

        private static $msgNoElements = 'Sorry, no author found.';

        public function __construct($array = array())
        {
            if (empty($array)) {
                return $this->msgNoElements;
            }

        }

    }
    //end class

}//end if