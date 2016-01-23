<?php
#2015 feb. 03, version 1
//based on https://gist.github.com/gregrickaby/6915797
/**
 * Must-Use Functions
 * 
 * A class filled with functions that will never go away upon theme deactivation.
 * 
 * @package WordPress
 * @subpackage GRD
 */
class GRD_Functions {

	public function __construct() {
		//based on https://wordpress.org/plugins/comment-popularity/faq/
		add_filter( 'hmn_cp_allow_guest_voting', '__return_true' );
		add_filter( 'hmn_cp_allow_negative_comment_weight', '__return_true' );
		add_action('wp_head', 'flheader');


	}
function flheader() {

        #echo '<link href="https://plus.google.com/106050687627577379782/" rel="publisher" />';
}
}
$GRD_Functions = new GRD_Functions();
?>