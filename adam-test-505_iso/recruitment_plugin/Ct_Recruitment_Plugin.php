<?php

class Ct_Recruitment_Plugin
{

    public function init() {
        add_action( 'init', array( $this, 'ct_rec_create_post_type' ) );
        add_action('init', array( $this,'register_shortcode') );
        add_action('add_meta_boxes', array( $this, 'ct_teammember_metabox' ) );
        add_action('save_post', array( $this, 'ct_teammember_metabox_save_postdata' ) );
        add_action('wp_enqueue_scripts', array( $this, 'callback_for_setting_up_scripts'),20 );
    }
    public function callback_for_setting_up_scripts() {
        // JS
        $pluginpath = plugins_url('adam-test/');

        wp_register_script( 'ct_isotope', $pluginpath . 'isotope/isotope.pkgd.min.js' );
        wp_enqueue_script( 'ct_isotope' );

        wp_register_script( 'ct_popup', $pluginpath . 'magni/jquery.magnific-popup.min.js' );
        wp_enqueue_script( 'ct_popup' );


        wp_register_script( 'img_loader', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js' );
        wp_enqueue_script( 'img_loader' );

        wp_register_script( 'adam-test', $pluginpath . 'scripts.js' );
        wp_enqueue_script('adam-test' );

        // CSS
        wp_register_style( 'adam-test', $pluginpath . 'style/stylesheet.css' );
        wp_enqueue_style( 'adam-test' );

        wp_register_style( 'ct_popup', $pluginpath . 'magni/magnific-popup.css' );
        wp_enqueue_style( 'ct_popup' );

    }

    public function register_shortcode(){
        add_shortcode( 'ct_team', array( $this,'ct_recruitment_shortcode' ) );
    }

    public function ct_rec_create_post_type() {
        // Set UI labels for Custom Post Type
        $usingtheme = "twentyseventeen";
        $labels = array(
            'name'                => _x( 'Team Member', 'Post Type General Name', $usingtheme ),
            'singular_name'       => _x( 'Team Member', 'Post Type Singular Name', $usingtheme ),
            'menu_name'           => __( 'Team Members', $usingtheme ),
            'all_items'           => __( 'All Team Members', $usingtheme ),
            'view_item'           => __( 'View Team Member', $usingtheme ),
            'add_new_item'        => __( 'Add New Team Member', $usingtheme ),
            'add_new'             => __( 'Add New', $usingtheme ),
            'edit_item'           => __( 'Edit Team Member', $usingtheme ),
            'update_item'         => __( 'Update Team Member', $usingtheme ),
            'search_items'        => __( 'Search Team Member', $usingtheme ),
            'not_found'           => __( 'Not Found', $usingtheme ),
            'not_found_in_trash'  => __( 'Not found in Trash', $usingtheme ),
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => __( 'team_member', $usingtheme ),
            'description'         => __( '', $usingtheme ),
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'thumbnail'),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            //'taxonomies'          => array( 'genres' ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

        // Registering your Custom Post Type
        register_post_type( 'team_member', $args );
    }

    public function ct_teammember_metabox() {
        add_meta_box(
            'ct_tm_mbox_id',           // Unique ID
            'Team Member Meta Box',  // Box title
            array( $this, 'ct_teammember_metabox_html' ),  // Content callback, must be of type callable
            'team_member'            // Post type
        );
    }

    public function ct_teammember_metabox_html($post) {
        $fname = get_post_meta($post->ID, '_ct_teammember_meta_first', true);
        $lname = get_post_meta($post->ID, '_ct_teammember_meta_last', true);
        echo 'First Name: <input type="text" id="firstname" name="firstname" value="' . esc_textarea( $fname ) . '">';
        echo 'Last Name: <input type="text" id="lastname" name="lastname" value="' . esc_textarea( $lname ) . '">';
    }

    public function ct_teammember_metabox_save_postdata($post_id)
    {
        if (array_key_exists('firstname', $_POST)) {
            update_post_meta(
                $post_id,
                '_ct_teammember_meta_first',
                $_POST['firstname']
            );
        }

        if (array_key_exists('lastname', $_POST)) {
            update_post_meta(
                $post_id,
                '_ct_teammember_meta_last',
                $_POST['lastname']
            );
        }
    }

    public function ct_recruitment_shortcode($atts){

        extract(shortcode_atts( array('limit' => -1,
            'order'=> 'ASC'), $atts));
        $args = array(
            'posts_per_page' => $limit,
            'post_type' => 'team_member',
            'order' => $order,
            'orderby' => 'meta_value',
            'meta_key' => '_ct_teammember_meta_first'
        );
        $string = '<div class="grid js-grid">';

        $query = new WP_Query( $args );
        if( $query->have_posts() ) {

            while( $query->have_posts()) {


                $query->the_post();
                $img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                $string .= '<div class="grid-item"><a class="member js-member" href="' .esc_url($img_url).'" rel="lightbox">' . get_the_post_thumbnail() . '</a></div>' ;
            }
            $string .= '</div>';
        }
        wp_reset_postdata();
        return $string;
    }

}