<?php
/**
 * Created by JetBrains PhpStorm.
 * User: davidkryzaniak
 * Date: 20/07/2013
 * Time: 11:55
 *
 * This will contain all the HTML/CSS for the plugin. It's separate so it can be overloaded.
 */
if (!function_exists('createListAuthorDisplay')) {

    /*
     * This is a simple function that handles the display of the
     *
     * Don't like way this plugin looks? Copy this function in to your functions.php and make all the changes you'd like!
     */
    function createListAuthorDisplay($arrayOfPostsByAuthor = array())
    {
        if (empty($arrayOfPostsByAuthor)) {
            return 'Sorry, no author found.';
        }?>

        <style>
            .author-gravitar-image {
                float: left;
                padding-right: 1em
            }

            .list-author-single {
                clear: both
            }

            .list-author-single .author-nice-name {
                font-size: 1.2em;
                font-weight: 700
            }

            .list-author-single ul.list-of-posts {
                margin-left: 20px
            }
        </style>

        <div class="list-author-shortcode-wrapper"></div>
        <?php foreach ($arrayOfPostsByAuthor as $author => $arrayOfPostsDetails): ?>
        <?php $user_info = get_userdata($author); ?>
        <div class="list-author-single">
        <div class="author-gravitar-image">
            <img <?php echo get_avatar($author, 100); ?>
        </div>
        <div class="author-recent-posts">
            <div class="author-nice-name"><?php echo $user_info->user_nicename; ?></div>
            <div class="">Recent Posts:</div>
            <ul class="list-of-posts">
                <?php foreach ($arrayOfPostsDetails as $singlePost) : ?>
                    <li><?php echo $singlePost->post_title; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
        </div>

    <?php
    }

}//end if
