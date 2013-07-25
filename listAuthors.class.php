<?php
/**
 * Plugin Name: List Authors Shortcode
 *
 */

class listAuthors
{

    private $errorWPMSNotEnabled = "[ListAuthors] Sorry, You need to enable Multisite before you can use this plugin.";
    private $sqlGetAuthorsID = "SELECT ID, user_nicename from *|table|* ORDER BY %s LIMIT %d";
    private $sqlGetAuthorsPosts =
        "SELECT ID FROM *|table|* WHERE post_status='publish' AND post_type='post' AND post_author=%d ORDER BY ID DESC LIMIT 5";

    private $TABLE_IDENTIFIER = '*|table|*';
    private $TIME_FORMAT = "%m/%d/%Y at %l:%M %p";


    public function __construct()
    {
        //initialize the shortcode. If a shortcode is used, call listAuthorsRunner()
        add_shortcode('ListAuthors', array($this, 'listAuthorsRunner'));
    }

    public function listAuthorsRunner()
    {
        //bad things happen if you're not on a multisite.
        if (!is_multisite()) {
            return $this->errorWPMSNotEnabled;
        }

        $theAuthorsIDs = $this->getAuthorIDs();
        $postsByAuthors = $this->getPostsByAuthorsIDs($theAuthorsIDs);

        print_r(
            $postsByAuthors
        );
        return 'Hello, World';
    }

    private function getAuthorIDs($orderBy = "RAND()", $limit = 50)
    {
        global $wpdb;
        $authorIDs = $wpdb->get_results(
            $wpdb->prepare(
                $this->getTheTable($this->sqlGetAuthorsID, $wpdb->users),
                $orderBy,
                $limit
            )
        );
        return $authorIDs;
    }

    private function getPostsByAuthorsIDs($listOfUserIDs = null)
    {
        global $wpdb;
        $listOfPostsByAuthorID = array();

        foreach ($listOfUserIDs as $singleUserID) {

            // Get user data
            $currentAuthor = get_userdata($singleUserID->ID);

            $table = $wpdb->base_prefix;
            //if this is the main site, the table we need to query wont have the "%d_" in the table name.
            if ($currentAuthor->primary_blog == 1) {
                $table .= 'posts';
            } else {
                $table .= $currentAuthor->primary_blog . '_posts';
            }

            $posts = $wpdb->get_col(
                $wpdb->prepare(
                    $this->getTheTable($this->sqlGetAuthorsPosts, $table),
                    $singleUserID->ID
                )
            );

            $listOfPostsByAuthorID[$singleUserID->user_nicename] = $posts;

        }
        //end foreach

        return $listOfPostsByAuthorID;
    }


    private function getTheTable($sql = '', $table = '')
    {
        return str_replace(
            $this->TABLE_IDENTIFIER,
            $table,
            $sql
        );
    }

}

new listAuthors();