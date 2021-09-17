<?php
defined( 'ABSPATH' ) or exit;

/**
 * Plugin Name: WP Parser - Deleter
 * Description: An add-on for phpdoc-parser that deletes previously-generated documentation that is no longer updated by parser runs.
 * Plugin URI: https://github.com/csalzano/phpdoc-deleter
 * Author: Corey Salzano
 * Author URI: https://breakfastco.xyz
 * Version: 1.0.0
 * License: GPLv2 or later
 */

class Breakfast_WP_Parser_Deleter
{
		
	/**
	 * item_ids
	 * 
	 * An array that holds the IDs of posts that are parsed.
	 *
	 * @var array
	 */
	var $item_ids;

	public function add_hooks()
	{
		//As phpdoc-parser imports items, keep track of all the post IDs
		add_action( 'wp_parser_import_item', array( $this, 'save_item_id' ), 10, 3 );

		//After runs, delete posts that were not touched and then empty terms
		add_action( 'wp_parser_ending_import', array( $this, 'delete_untouched_items' ) );
	}

	public function delete_untouched_items()
	{
		if( empty( $this->item_ids ) )
		{
			return;
		}

        $post_types_to_clean = apply_filters( 'phpdoc_deleter_post_types', array(
            'wp-parser-function',  //wp-content\plugins\phpdoc-parser\lib\class-plugin.php:33
            'wp-parser-method',
            'wp-parser-class',
            'wp-parser-hook',
        ) );

        $posts_to_delete = get_posts( array(
            'post_type'      => $post_types_to_clean,
            'posts_per_page' => -1,
            'post__not_in'   => $this->item_ids,
        ) );

        foreach( $posts_to_delete as $post )
        {
            wp_delete_post( $post->ID, apply_filters( true, 'phpdoc_deleter_force_delete' ) );
        }

        //delete empty terms from taxonomies
        $taxonomies_to_clean = apply_filters( 'phpdoc_deleter_taxonomies', array(
            'wp-parser-namespace',
            'wp-parser-package',
            'wp-parser-since',
            'wp-parser-source-file',            
        ) );

        foreach( $taxonomies_to_clean as $taxonomy )
        {
            $terms_to_delete = get_terms( array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'count'      => true,
            ) );
            foreach( $terms_to_delete as $term )
            {
                if( 0 == $term->count )
                {
                    wp_delete_term( $term->term_id, $taxonomy );
                }                
            }
        }
	}
	
	/**
	 * save_item_id
	 *
	 * @param  int   $post_id   Optional; post ID of the inserted or updated item.
	 * @param  array $data PHPDoc data for the item we just imported
	 * @param  array $post_data WordPress data of the post we just inserted or updated
	 * @return void
	 */
	public function save_item_id( $post_id, $data, $post_data )
	{
		$this->item_ids[] = $post_id;
	}
}
$phpdoc_deleter_023947234 = new Breakfast_WP_Parser_Deleter();
$phpdoc_deleter_023947234->add_hooks();
