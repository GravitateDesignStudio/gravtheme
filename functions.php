<?php

require_once('library/grav.php'); // core functions (don't remove)
add_editor_style("library/css/editor-styles.css"); // editor styles for tinymce
// add_image_size( 'thumb-600', 600, 150, true );  // custom image size example





/************* SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function grav_register_sidebars() {

    register_sidebar(array(
    	'id' => 'sidebar1',
    	'name' => 'Sidebar 1',
    	'description' => 'The first (primary) sidebar.',
    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
    	'after_widget' => '</div>',
    	'before_title' => '<h4 class="widgettitle">',
    	'after_title' => '</h4>',
    ));
    
  } 




/******** MENUS, ROLES & CAPABILITIES *******************/
/* remove some menus from the dashboard we don't need (for all users) */
function remove_menus () {
global $menu;
	$restricted = array(__('Posts'), __('Links'), __('Comments'));
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
}
//add_action('admin_menu', 'remove_menus'); // uncomment to enable






/* Add Gforms capabilities to a role */
/* $role = get_role( 'ROLE_NAME_GOES_HERE' );
$role->add_cap( 'gravityforms_edit_forms' );
$role->add_cap( 'gravityforms_delete_forms' );
$role->add_cap( 'gravityforms_create_form' );
$role->add_cap( 'gravityforms_view_entries' );
$role->add_cap( 'gravityforms_edit_entries' );
$role->add_cap( 'gravityforms_delete_entries' );
$role->add_cap( 'gravityforms_view_settings' );
$role->add_cap( 'gravityforms_edit_settings' );
$role->add_cap( 'gravityforms_export_entries' );
$role->add_cap( 'gravityforms_view_entry_notes' );
$role->add_cap( 'gravityforms_edit_entry_notes' );*/








/* 
    remove some roles we don't need 
    (only run this if you need it.  only needs to be run once)
    use "reset_role('ROLE_NAME');" to restore the role
    
*/
/*$wp_roles = new WP_Roles();
$wp_roles->remove_role("author");
$wp_roles->remove_role("subscriber");
$wp_roles->remove_role("editor");
$wp_roles->remove_role("contributor");*/













/************* COMMENT LAYOUT *********************/
		
// Comment Layout
function grav_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?>>
		<article id="comment-<?php comment_ID(); ?>" class="comment-block">
			<header class="comment-author vcard">
				<?php echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>
				<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
				<time><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s'), get_comment_date(),  get_comment_time()) ?></a></time>
				<?php edit_comment_link(__('(Edit)'),'  ','') ?>
			</header>
			<?php if ($comment->comment_approved == '0') : ?>
       			<div class="help">
          			<p><?php _e('Your comment is awaiting moderation.') ?></p>
          		</div>
			<?php endif; ?>
			<section class="comment_content">
				<?php comment_text() ?>
			</section>
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</article>
    <!-- </li> is added by wordpress automatically -->
<?php
} 









/************* SEARCH FORM LAYOUT *****************/

// Search Form
function grav_wpsearch($form) {
    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
    <label class="screen-reader-text" for="s">' . __('Search for:', 'bonestheme') . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search the Site..." />
    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
    </form>';
    return $form;
} 