<?php
/**
 * Plugin Name: List Authors Shortcode
 *
 */

class listAuthors
{

    private $errorWPMSNotEnabled = "[ListAuthors] Sorry, You need to enable Multisite before you can use this plugin.";
    private $sqlGetAuthorsID = "SELECT ID, user_nicename from *|table|* ORDER BY user_nicename ASC LIMIT %d";
    private $sqlGetAuthorsPosts =
        "SELECT ID FROM *|table|* WHERE post_status='publish' AND post_type='post' AND post_author=%d ORDER BY ID DESC LIMIT %d";

    private $TABLE_IDENTIFIER = '*|table|*';


    /**
     * Constuctor: Initializes the shortcode with WP
     *
     * to-do: Input params (AKA, "limit 20 users..." or "limit 20 posts per user...")
     */
    public function __construct()
    {
        //initialize the shortcode. If a shortcode is used, call listAuthorsRunner()
        add_shortcode('ListAuthors', array($this, 'listAuthorsRunner'));

        //If there isn't a display setup yet, pull the default
        if (!function_exists('createListAuthorDisplay')) {
            require_once('ListAuthorDisplay.php');
            add_action('wp_enqueue_scripts', 'enqueueDefaultListAuthorDisplayStyles');
        }
    }

    /**
     * This is the "heart" of the plugin. First gets the users, then their posts, and finally builds the HTML.
     *
     * @return string HTML output
     */
    public function listAuthorsRunner()
    {
        //bad things happen if you're not on a multisite.
        if (!is_multisite()) {
            return $this->errorWPMSNotEnabled;
        }

        $theAuthorsIDs = $this->getAuthorIDs(); //get the authors
        $postsByAuthors = $this->getPostsByAuthorsIDs($theAuthorsIDs);

        print_r($postsByAuthors);

        //echo or return the HTML
        return createListAuthorDisplay($postsByAuthors); //using a return for future expansion
    }

    /**
     * Find $limit users and get their ID and "Friendly name" (how their name appears on the site)
     *
     * @param int $limit Max number of users to return
     * @return array
     */
    private function getAuthorIDs($limit = 50)
    {
        global $wpdb;
        $authorIDs = $wpdb->get_results(
            $wpdb->prepare(
                $this->getTheTable($this->sqlGetAuthorsID, $wpdb->users),
                $limit
            )
        );
        return $authorIDs;
    }

    /**
     * Find $limit posts by the author from an array of post
     *
     * @param null $listOfUserIDs
     * @param int $limit
     * @return array
     */
    private function getPostsByAuthorsIDs($listOfUserIDs = null, $limit = 5)
    {
        global $wpdb;
        $listOfPostsByAuthorID = array();

        foreach ($listOfUserIDs as $singleUserID) {

            // Get user data
            $currentAuthor = get_userdata($singleUserID->ID);
            //hi
            //get the table name, start with the table prefix
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
                    $singleUserID->ID,
                    $limit
                )
            );

            $postsDetails = array();
            foreach ($posts as $singlePost) {
                $postsDetails[] = $this->getSinglePostDetails(
                    $singlePost,
                    $currentAuthor->primary_blog
                );
            }
            $listOfPostsByAuthorID[$singleUserID->ID] = $postsDetails;

        }

        return $listOfPostsByAuthorID;
    }

    /**
     * This function gets the details of the array of posts based on an array of post IDs
     *
     * @param int $blogID We need to know the ID of the blog
     * @param array $arrayOfPostIDs the array of posts.
     * @return array
     */
    private function getSinglePostDetails($postID, $blogID = 1)
    {
        return get_blog_post($blogID, $postID);
    }

    /**
     * To my dismay, the $wpdb->prepare() wraps %s in single quotes. Typically, this isn't a problem, but I need to send
     * in the table to the query. This function replaces *|table|* with the correct table.
     *
     * @param string $sql
     * @param string $table
     * @return strind The SQL statement.
     */
    private function getTheTable($sql = '', $table = '')
    {
        return str_replace(
            $this->TABLE_IDENTIFIER,
            $table,
            $sql
        );
    }

}

//Run this shortcode
new listAuthors();