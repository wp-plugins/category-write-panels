<?php
/*
Plugin Name: Category Write Panels
Plugin URI: http://www.seo-jerusalem.com/home/seo-friendly-web-development/wordpress-category-write-panels-plugin/
Description: Automaticly creates seperate write and edit panels for each category
Version: 1.0.2
Author: SEO Jerusalem
Author URI: http://www.seo-jerusalem.com
*/


// rearange the admin menu to add the custom panels
add_action('admin_head', 'cwp_do_panels');
// get the post category
add_action('admin_head-post-new.php', 'cwp_postcat');
add_action('admin_head-post.php', 'cwp_postcat');
add_action('admin_head-edit.php', 'cwp_postcat');
// modified category box
add_action('do_meta_boxes', 'cwp_categories');


function cwp_do_panels($hook) {
	global $menu, $submenu, $categories;
	//print_r($menu);
	

	// move second menu to bottom to make room
	$menu[1] = $menu[4];
	unset($menu[4]);
	// move posts menu to after pages
	$menu[24] = $menu[5];
	unset($menu[5]);
	// change posts menu to categories
	$menu[24][4] = 'menu-top';
	$menu[24][0] = 'Categories';
	// move menus to clear room for categories
	$menu[22] = $menu[10];
	unset($menu[10]);
	$menu[23] = $menu[15];
	unset($menu[15]);
	// give pages menu style for first item
	$menu[20][4] = 'open-if-no-js menu-top menu-top-first';

	
	// remove post parts from menu
	unset($submenu['edit.php'][5]);
	unset($submenu['edit.php'][10]);
	unset($submenu['edit.php'][15]);
	
	// get categories
	$categories = get_categories('hide_empty=0');
	$newcats = array();

	foreach ($categories as $cat) {
		if ($cat->category_parent == 0) {
			$newcats[] = $cat;
		}
	}


	// the position we can start at in the $menu array
	$count = 2;
	
	foreach ($newcats as $cat) {			
		if ($count == 2) {
			$class = 'open-if-no-js menu-top menu-top-first';
		} else if( ($count -1) == count($newcats) ) {
			$class = 'menu-top  menu-top-last';
		} else {
			$class = 'menu-top';
		}
	
		$menu[$count] = array(
			$cat->name,
			'edit-' . $cat->slug, 
			'edit-' . $cat->slug,
			'',
			$class,
			'menu-pages-' . $cat->slug,
			'div'			
		);
		//localization (menu titles)		
		$edit_title = __('Edit','category_panels');
		$add_title = __('Add New','category_panels');
		
		$submenu['edit-' . $cat->slug][0] = array(
			$edit_title,
			'edit_posts',
			'edit.php?cat=' . $cat->term_id
		);
			
		$submenu['edit-' . $cat->slug][1] = array(
			$add_title,
			'edit_posts',
			'post-new.php?cat=' . $cat->term_id
		);			
			
		$count++;
		
	}
	
	// add seperator
	$menu[$count] = array(
		'',
		'edit_posts',
		'separator1.5',
		'',
		'wp-menu-separator'
	);

	// reorder menu based on keys	
	ksort($menu);
	
}


function cwp_postcat() {
	// figure out what category we are dealing with
	global $cwp_postcat, $post, $cwp_postcatname, $title;
       if (is_numeric($_GET['cat'])) {
               $cwp_postcat = $_GET['cat'];
       } else {
               $categories = get_categories();
               foreach ($categories as $cat) {
                       if ($cat->category_parent == '0') {
                               $topcats[] = $cat->term_id;
                       }
               }
               $postcats = wp_get_post_categories($post->ID);
               foreach ($postcats as $pc) {
                       if (in_array($pc, $topcats)) {
                               $cwp_postcat = $pc;
                               break;
                       }
               }
       }
	if (empty($cwp_postcat)) {
		$cwp_postcat = (int) get_option('default_category');
	}

	// set page title	
	$cwp_postcatname = get_cat_name($cwp_postcat);
	$title = $title . ' &raquo; ' . $cwp_postcatname;
	
	//echo $cwp_postcat;
	
	echo '<style type="text/css">
		#category-' . $cwp_postcat . ' label, #category-' . $cwp_postcat . ' input {
			display: none;
		}
		#category-' . $cwp_postcat . ' {
			padding-left: 0;
			padding-right: 0;
		}
		#category-' . $cwp_postcat . ' ul {
			margin-left: 0 !important;
			margin-right: 0 !important;
		}
		#category-' . $cwp_postcat . ' ul label, #category-' . $cwp_postcat . ' ul input {
			display: inline;
		}		
		</style>';
}


function cwp_categories() {
	global $wp_meta_boxes;
	$wp_meta_boxes['post']['side']['core']['categorydiv']['callback'] = 'cwp_categories_meta_box';
}

function cwp_categories_meta_box() {
	global $cwp_postcat, $post, $categories;
		
		if (empty($categories)) {
			$categories = get_categories('hide_empty=0');		
		}
		
		$exclude_trees;
		foreach ($categories as $cat) {
			if ($cat->term_id != $cwp_postcat AND $cat->category_parent == 0) {
				$exclude_trees[] = $cat->term_id;
			}
		}
		$exclude_trees = implode(',', $exclude_trees);
	
	
	?>
	<ul id="category-tabs">
		<li class="tabs"><a href="#categories-all" tabindex="3"><?php _e( 'All Categories' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#categories-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
	</ul>

	<div id="categories-pop" class="tabs-panel" style="display: none;">
		<ul id="categorychecklist-pop" class="categorychecklist form-no-clear" >
	<?php $popular_ids = cwp_popular_terms_checklist('category'); ?>
		</ul>
	</div>

	<div id="categories-all" class="tabs-panel">
		<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
	<?php 
		if (is_numeric($_GET['cat'])) {
			$cats = array($cwp_postcat);		
		} else {
		$cats = wp_get_post_categories($post->ID); 
		}
		if (empty($cats)){
			$cats = array($cwp_postcat);
		}
		wp_category_checklist($post->ID, $cwp_postcat, $cats, $popular_ids) ?>
		</ul>
	</div>

	<?php 
	// quoted out for now bcause the ajax messes everything up
	/* if ( current_user_can('manage_categories') ) : ?>
	<div id="category-adder" class="wp-hidden-children">
		<h4><a id="category-add-toggle" href="#category-add" class="hide-if-no-js" tabindex="3"><?php _e( '+ Add New Category' ); ?></a></h4>
		<p id="category-add" class="wp-hidden-child">
		<label class="screen-reader-text" for="newcat"><?php _e( 'Add New Category' ); ?></label><input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php esc_attr_e( 'New category name' ); ?>" tabindex="3" aria-required="true"/>
		<label class="screen-reader-text" for="newcat_parent"><?php _e('Parent category'); ?>:</label><?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'tab_index' => 3, 'exclude_tree' => $exclude_trees ) ); ?>
		<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php esc_attr_e( 'Add' ); ?>" tabindex="3" />
	<?php	wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
		<span id="category-ajax-response"></span></p>
	</div>
	
<?php
endif; */

}

function cwp_popular_terms_checklist( $taxonomy, $default = 0, $number = 10, $echo = true ) {
	global $post_ID, $cwp_postcat;
	if ( $post_ID )
		$checked_categories = wp_get_post_categories($post_ID);
	else
		$checked_categories = array();
	$categories = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => $number, 'hierarchical' => false, 'child_of' => $cwp_postcat ) );

	$popular_ids = array();
	foreach ( (array) $categories as $category ) {
		$popular_ids[] = $category->term_id;
		if ( !$echo ) // hack for AJAX use
			continue;
		$id = "popular-category-$category->term_id";
		?>

		<li id="<?php echo $id; ?>" class="popular-category">
			<label class="selectit">
			<input id="in-<?php echo $id; ?>" type="checkbox" value="<?php echo (int) $category->term_id; ?>" />
				<?php echo esc_html( apply_filters( 'the_category', $category->name ) ); ?>
			</label>
		</li>

		<?php
	}
	return $popular_ids;
}
?>