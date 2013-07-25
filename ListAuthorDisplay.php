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

    function createListAuthorDisplay($arrayOfPostsByAuthor = array())
    {
        if (empty($arrayOfPostsByAuthor)) {
            return 'Sorry, no author found.';
        }

        /**
         *  I'm going to keep this mostly procedural code. This is for ease of modifying.
         *
         * NOTE!
         * If you don't like the way this gets displayed:
         * 1.) copy this file into your theme's directory
         * 2.) Make your changes to the copy
         * 3.) In your functions.php, add
         *          require_once('/path/to/your/version/of/ListAuthorDisplay.php');
         */

        //---START THIS------------------------
        ?>
        <div class="list-author-snippet-wrapper"></div>
        <?php foreach ($arrayOfPostsByAuthor as $author => $arrayOfPostsDetails): ?>

        <ul class="list-author-snippet-single-author">
            <li>User: <?php echo $author; ?> </li>
            <li>Recent Posts:
                <ul>

                    <?php foreach ($arrayOfPostsDetails as $singlePost) : ?>

                        <li><?php echo $singlePost->post_title; ?></li>

                    <?php endforeach; ?>

                </ul>
            </li>

        </ul>


    <?php endforeach; ?>
        </div>

        <?php //---STOP EDITING----------------------
    }

}//end if