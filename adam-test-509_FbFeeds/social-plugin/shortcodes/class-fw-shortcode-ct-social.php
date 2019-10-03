<?php


class Fw_Shortcode_Ct_Social extends WPBakeryShortCode
{
    function __construct()
    {
        add_action('init', array($this, 'ct_social_mapping'));
        add_shortcode( 'ct_social', array( $this,'ct_social_shortcode_html' ) );

        add_action('wp_enqueue_scripts', array( $this, 'init_scripts' ) );
    }

    //initials
    public function init_scripts(){
        $pluginpath = plugins_url('adam-test-2/');
        //styles
        wp_register_style( 'ct_stylesheet', $pluginpath . 'assets/css/stylesheet.css');
        wp_enqueue_style( 'ct_stylesheet' );
    }
    // Element Mapping

    public function ct_social_mapping()
    {

        // Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        // Map the block with vc_map()
        vc_map(
            array(
                'name' => __('CT Social', 'text-domain'),
                'base' => 'ct_social',
                'description' => __('Displaying social icons', 'text-domain'),
                'category' => __('CT Test Plugins', 'text-domain'),
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => 'Style',
                        'description'=> 'Select icons display style',
                        'param_name' => 'style',
                        'value' => array( '', 'horizontal' , 'vertical')
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => 'Icont Type 1',
                        'description'=> 'Select which icon you want display',
                        'param_name' => 'type_1',
                        'value' => array( '', 'facebook', 'twitter')
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'Label 1',
                        'description'=> 'Write text which will appear while cursor pointing an icon',
                        'param_name' => 'label_1',
                        'value' => ''
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'ID 1',
                        'description'=> 'Write page ID which should be redirected to',
                        'param_name' => 'id_1',
                        'value' => ''
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => 'Icont Type 2',
                        'description'=> 'Select icons display style',
                        'param_name' => 'type_2',
                        'value' => array( '', 'twitter', 'facebook' )
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Label 2',
                        'description'=> 'Write text which will appear while cursor pointing an icon',
                        'param_name' => 'label_2',
                        'value' => ''
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'ID 2',
                        'description'=> 'Write page ID which should be redirected to',
                        'param_name' => 'id_2',
                        'value' => ''
                    ),
                ),
            )
        );

    }


    //rest of functions
    public function ct_social_shortcode_html($atts){

        extract(shortcode_atts( array(
            'style' => '',
            'type_1' => '',     //fb or twitter icon
            'type_2' => '',     //fb or twitter icon
            'label_1' => '',    //as title
            'label_2' => '',    //as title
            'id_1' => '',
            'id_2' => ''

        ),
            $atts ));

        //variables
        $fbicon = 'ct-round fa fa-facebook';   // facebook icon; add to <i class= ...
        $twicon = 'ct-round fa fa-twitter';   // twitter icon; add to <i class= ...
        $fbhref = 'https://facebook.com/';
        $twhref = 'https://twitter.com/';

        $vorh = ( $style == 'horizontal') ? 'display: inline-block;' : '';    // vorh : vertical-or-horizontal;

        //html code
        $string = '<div class="ct-social-icons">';

        //what is type_1?
        if($type_1){
            if( $type_1 === 'facebook' ){
                $string .= '<div class="ct-social-facebook" style="' . $vorh . '">'; //open <div
                $string .= '<a class="' . $type_1 . '" href="' . $fbhref . $id_1 . '" title="' . $label_1 . '">';   // open <a
                $string .= '<i title="' . $label_1 . '" class="ct-fb ' . $fbicon . '"></i>';  // <i> tag

            }elseif ( $type_1 === 'twitter'){
                $string .= '<div class="ct-social-twitter" style="' . $vorh . '">'; //open <div
                $string .= '<a class="' . $type_1 . '" href="' . $twhref . $id_1 . '" title="' . $label_1 . '">';   // open <a
                $string .= '<i title="' . $label_1 . '" class="ct-tw ' . $twicon . '"></i>';  // <i> tag
            }   //end of if blocks

            $string .='</a></div>';  //close a & li
        }

        //what is type_2
        if($type_2){
            if( $type_2 === 'facebook' ){
                $string .= '<div class="ct-social-facebook" style="' . $vorh . '">'; //open <div
                $string .= '<a class="' . $type_2 . '" href="' . $fbhref . $id_2 . '" title="' . $label_2 . '">';   // open <a
                $string .= '<i title="' . $label_2 . '" class="ct-fb ' . $fbicon . '"></i>';  // <i> tag
            }elseif ( $type_2 === 'twitter'){
                $string .= '<div class="ct-social-twitter" style="' . $vorh . '">'; //open <div
                $string .= '<a class="' . $type_2 . '" href="' . $twhref . $id_2 . '" title="' . $label_2 . '">';   // open <a
                $string .= '<i title="' . $label_2 . '" class="ct-tw ' . $twicon . '"></i>';  // <i> tag
            }   //end of if blocks
            $string .='</a></div>';  //close a & li
        }

        $string .= '</div>';
        wp_reset_postdata();
        return $string;
    }   //end of function

}//end of class
new Fw_Shortcode_Ct_Social();
