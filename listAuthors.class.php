<?php
/**
 * Plugin Name: List Authors Shortcode
 *
 */

class listAuthors
{

    private $errorWPMSNotEnabled = "[ListAuthors] Sorry, You need to enable Multisite before you can use this plugin.";
    private $timeFormat = "%m/%d/%Y at %l:%M %p";

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

        print_r(
            $this->getAuthorDetails($this->getAuthorIDs())
        );
        return 'Hello, World';
    }

    private function getAuthorIDs($orderBy = "RAND()", $limit = 50)
    {
        global $wpdb;
        $query = "SELECT ID, user_nicename from $wpdb->users ORDER BY %s LIMIT %d";
        $authorIDs = $wpdb->get_results(
            $wpdb->prepare($query, $orderBy, $limit)
        );
        return $authorIDs;
    }

    private function getAuthorDetails($listOfUserIDs = null)
    {
        global $wpdb;

        foreach ($listOfUserIDs as $singleUserID) {

            // Get user data
            $currentAuthor = get_userdata($singleUserID->ID);

            // Get link to author page
            $userURL = get_author_posts_url($currentAuthor->ID);

            // Get blog details for the authors primary blog ID
            //$blogDetails = get_blog_details($currentAuthor->primary_blog);

            //$blog_details->post_count == "1"

            //$updatedOn = strftime($this->timeFormat,strtotime($blogDetails->last_updated));

            $posts = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT ID FROM wp_%d_posts WHERE post_status='publish' AND " .
                    "post_type='post' AND post_author=%d ORDER BY ID DESC LIMIT 5",
                    $currentAuthor->primary_blog,
                    $singleUserID->ID
                )
            );

            print_r(
                $singleUserID->primary_blog
            );

            return true;

        }

    }

    private function getDisplay()
    {

    }

}

$doListAuthors = new listAuthors();