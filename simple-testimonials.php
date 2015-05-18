<?php
/*
  Plugin Name: Simple Testimonials
  Description: Super simple random testimonial outputter for wordpress
  Author: Alex Clarke
  Version: 0.3
*/

//REGISTER STYLES
function st_register_styles() {
    // register  + enqueue
    if (!is_admin()) {
        wp_register_style('st_styles', plugins_url('assets/simple-testimonials.min.css', __FILE__));
        wp_enqueue_style('st_styles');
    }
}

//CREATE SHORTCODE (FOOTER)
function st_function($type='st_function') {
    $args = array('post_type' => 'simple_testimonials', 'posts_per_page' => 1, 'orderby' => 'rand');
    $loop = new WP_Query($args);
    if ($loop->have_posts()) {
        while ($loop->have_posts()) {
            $loop->the_post(); ?>

            <?php 

            $count_posts = wp_count_posts('simple_testimonials'); 
            $published_posts = $count_posts->publish; 

            ?>


            <div class="st-container cf wrap">
            
                <?php the_content(); ?>

                <h4><?php the_title(); ?>

                    <?php if( $published_posts > 1 ) {
                        echo '<a href="btn" href="#">See More</a>';
                    } ?>

                </h4>

            </div>

        <?php
        }
    }
}

//CREATE SHORTCODE (IN-PAGE)
function st_function_onpage( $atts ) {

    extract( shortcode_atts( array(
        'category' => '',
        'per_slide' => 1,
    ), $atts ) );

    $args = array(
        'post_type' => 'simple_testimonials',
        'taxonomy' => 'simple_testimonials_categories',
        'term' => $category
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()) {

        $output = '<div class="st-container-inpage per_slide-' .$per_slide.'"><ul>';

        if ($per_slide > 1){

            // if more than 1 per slide

            $i = 1;

            $output .= '<li>';

            while ($loop->have_posts()) {

                $loop->the_post();

                $content = get_the_content();

                $output .= '<blockquote class="st-item">'.
                           $content.
                           '</blockquote>';

                if($i % $per_slide == 0 ) {

                    $output .= '</li><li>';

                }

                $i++;

            }

            $output .= '</li>';

        } else {

            // if only 1 per slide

            while ($loop->have_posts()) {

                $output .= '<li>';

                $loop->the_post();

                $content = get_the_content();

                $output .= '<blockquote class="st-item">'.
                           $content.
                           '</blockquote>';

                $output .= '</li>';

            }

        }

        $output .= '</ul></div>';
    }

    return $output;

}

//CREATE TESTIMONIAL SHORTCODE + POST TYPE
function st_init() {
    add_shortcode('random-testimonial', 'st_function');
    add_shortcode('onpage-testimonials', 'st_function_onpage');
    add_shortcode('st-shortcode', 'st_function');
    $cpt_args = array(  
        'public' => false, 
        'show_ui' => true,
        'show_in_menu' => true,
        'has_archive' => false,
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'label' => 'Testimonials', 
        'supports' => array('title', 'editor', 'excerpt'), 
        'menu_icon' => 'dashicons-format-quote', 
        'rewrite' => array('slug' => 'testimonials', 'with_front' => false), 
    );
    register_post_type('simple_testimonials', $cpt_args);
    register_taxonomy( 'simple_testimonials_categories', 
        array('simple_testimonials'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
        array('hierarchical' => true,     /* if this is true, it acts like categories */
            'labels' => array(
                'name' => __( 'Testimonial Categories', 'bonestheme' ), /* name of the custom taxonomy */
                'singular_name' => __( 'Testimonial Category', 'bonestheme' ), /* single taxonomy name */
                'search_items' =>  __( 'Search Testimonial Categories', 'bonestheme' ), /* search title for taxomony */
                'all_items' => __( 'All Testimonial Categories', 'bonestheme' ), /* all title for taxonomies */
                'parent_item' => __( 'Parent Testimonial Category', 'bonestheme' ), /* parent title for taxonomy */
                'parent_item_colon' => __( 'Parent Testimonial Category:', 'bonestheme' ), /* parent taxonomy title */
                'edit_item' => __( 'Edit Testimonial Category', 'bonestheme' ), /* edit custom taxonomy title */
                'update_item' => __( 'Update Testimonial Category', 'bonestheme' ), /* update title for taxonomy */
                'add_new_item' => __( 'Add New Testimonial Category', 'bonestheme' ), /* add new title for taxonomy */
                'new_item_name' => __( 'New Custom Testimonial Name', 'bonestheme' ) /* name title for taxonomy */
            ),
            'show_admin_column' => true, 
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'testimonial-category' ),
        )
    );
}

//ADD ALL THE ACTIONS
add_action('init', 'st_init');
add_action('wp_print_styles', 'st_register_styles');
?>