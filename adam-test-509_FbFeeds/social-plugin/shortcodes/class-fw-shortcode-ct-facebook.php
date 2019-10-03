<?php

class Fw_Ct_Facebook extends WPBakeryShortCode
{
    function __construct()
    {
        add_action('init', array($this, 'ct_fb_mapping'));
        add_shortcode("ct_facebook", array( $this, 'shortcode_html' ) );

    }

    public function ct_fb_mapping(){
// Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        // Map the block with vc_map()
        vc_map(
            array(
                'name' => __('CT Facebook Feeds', 'text-domain'),
                'base' => 'ct_facebook',
                'description' => __('Displaying facebook feeds', 'text-domain'),
                'category' => __('CT Test Plugins', 'text-domain'),
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => 'Style',
                        'description'=> 'Select widget coloristic style',
                        'param_name' => 'style',
                        'value' => array( '', 'dark', 'light' )
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'Page ID',
                        'description' => 'Your Facebook Fan Paged ID',
                        'param_name' => 'id',
                        'value' => ''
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'Access Token',
                        'description' => 'Access Token is needed to conect with Facebook API and so on to take page posts content',
                        'param_name' => 'access_token',
                        'value' => ''
                    ),

                    array(
                        'type' => 'checkbox',
                        'heading' => 'Embed Images',
                        'description' => 'Check for embeding images to feeds',
                        'param_name' => 'embed_images',
                        'value' => 'false'
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'Posts Limit',
                        'description' => 'Limit of displaying posts',
                        'param_name' => 'limit',
                        'value' => 3
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'Char Limit',
                        'description' => 'Limit of displaying chars per post',
                        'param_name' => 'length_limit',
                        'value' => 100
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => 'Cache',
                        'description' => 'How often widget has got to refresh (in seconds)? Default it is 300 seconds',
                        'param_name' => 'cache',
                        'value' => 300
                    )
                ),
            )
        );
    }   //end of method

    public function get_avatar ( $json ){
        $img = '<img class="ct-fb-avatar" src="' . $json . '" alt="">';
        return $img;
    }

    public function  set_href_int_text( $json, $charlimit ){
        $textraw = $json[ 'message' ];
        //finding begining of href if exist
        $begin = strpos( $textraw, 'https://');
        //finding end of href
        $end = $begin;
        for( ; $end < strlen( $textraw ); $end++ ){
            if($textraw[$end] ===' ') break;
        };
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $textraw, $match);
        $insertstr ='<a href="' . $match[0][0] . '" class="fb-timeline-link">';
        //print_r($match[0]);
        $str = '';
        if( strlen( $textraw ) < $charlimit ){
            $str .= substr($textraw, 0, ( $begin - 1 ) ) . $insertstr . substr($textraw, $begin, $end) . '</a>' . substr($textraw, $end);
        }else{
            $cutlink = 0;
            if( $charlimit > $begin ){
                $str .= substr($textraw, 0, ( $begin - 1 ) ) . $insertstr . substr($textraw, $begin, $charlimit) . '</a>';
            } else{
                $str .= substr($textraw, 0, $charlimit ) . '...' . $insertstr . substr($match[0][0], 0, 11) . '...</a>';
            }
        }

        return $str;
    }

    public function  html_post ( $json, $embed_images, $charlimit, $avatar, $username, $id ){
        //feed text
        $textraw = $this->set_href_int_text( $json, $charlimit );
        //feed
        $posthtml = '<hr class="ct-fb-feed-line"><div class="ct-fb-feed">
                     <div class="ct-fb-brand-logo">
                     <i class="fa fa-facebook"></i>
                     </div>';

        $head = '<div class="ct-fb-post-header">
        <div class="ct-fb-avatar">' . $avatar . '
        </div>
        <div class="ct-fb-username">
        <a class="ct-fb-author-link" href="https://facebook.com/' . $id . '">
        <span class="ct-fb-name">@' . $username . '</span>
        </a>
        </div>
        </div>';

        $posttext = '<a href="https://facebook.com/' . $json['id'] . '" class="ct-fb-text"><div class="ct-fb-post-content"><p class="ct-fb-text">' . $textraw . '</p>';

        //feed img - if exist
        $img = '';
        if( $embed_images  && $json['full_picture'] !=''){
            $img .= '<img class="ct-fb-img" src="' . $json['full_picture'] . '" alt="">';
        }
        $date = date("M d, y", $json['created_time']);
        $twdata = '<div class="ct-fb-date" id="ct-fb-hover"><time class="ct-fb-post-date">' . $date . '</time></div>';
        $posthtml .= $head . $posttext . $img . '</div></a>' . $twdata . '</div>';
        return $posthtml;

    }   //end of method

    public function shortcode_html($atts) {
        extract(shortcode_atts( array(
            'style' => 'light',
            'embed_images'=> '',
            'limit' => 10,
            'length_limit' => 100,
            'cache' => 300,
            'id' => '',
            'access_token' => ''
        ), $atts));

        $pluginpath = plugins_url('adam-test-2/assets/css/');
        $style = $style . 'fb' . '.css';

        wp_register_style( 'ct_stylesheet3', $pluginpath . $style);
        wp_enqueue_style( 'ct_stylesheet3' );

        $string = '';
        if($id !== ''){
            $output = get_transient('facebook_feeds');
            if($output === false){

                $url = 'https://graph.facebook.com/' . $id . '?fields=id,name,feed.limit(' . $limit . '){full_picture,message,picture,created_time},picture{url}&transport=cors&access_token='.$access_token.'';
                $json = file_get_contents( $url );
                $obj = json_decode($json, true);
                $avatar = $this->get_avatar( $obj['picture']['data']['url']);
                $username = $obj['name'];
                //creating widget structure
                $header = '<div class="ct-fb-widget-header">
                <h1 class="ct-fb-widget-headline">Posts
                <span class="ct-fb-tag-name"> by<a href="" class="fb-timeline-link"> @' . $username . '</a></span>
                </h1>
                </div>';

                $postsbody = '<div class="ct-fb-widget-body">
                <ol class="ct-fb-widget-posts">';

                $string .= '<div class="ct-fb-widget"><div class="ct-fb-ac-widget">' . $header . $postsbody;
                //creating post structure
                foreach ( $obj['feed']['data'] as $key => $value ){

                    $string .= '<li class="ct-fb-widget-post" id="ct-fb-hover">';
                    $string .= $this->html_post( $obj['feed']['data'][$key], $embed_images, $length_limit, $avatar, $username, $id );
                    $string .= '</li>';
                }

                $footer = '<div class="ct-fb-widget-footer">
                <hr class="ct-fb-feed-line">
                <span class="ct-fb-footer">
                <a href="https://facebook.com/' . $id . '" class="fb-timeline-link ct-fb-footer">View on Facebook</a>
                </span>
                </div>';
                $string .= '</ol></div>' . $footer . '</div></div>';
                $output = $string;
                set_transient( 'facebook_feeds', $string, $cache);

            }   //end of if statement - creating new content
            //release final product
            return $output;
        }
        return;
    }   //end of method

}   //end of class

new Fw_Ct_Facebook();