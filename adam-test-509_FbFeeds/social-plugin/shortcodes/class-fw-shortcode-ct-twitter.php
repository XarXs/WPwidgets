<?php


class Fw_Ct_Twitter extends WPBakeryShortCode
{
    function __construct()
    {
        add_action( 'init', array( $this, 'ct_tw_mapping' ) );
        add_shortcode("ct_twitter", array( $this, 'shortcode_html' ) );

        //add_action('init', array($this, 'reg_short'));
    }

    public  function reg_short(){
        add_shortcode("ct_twitter", array( $this, 'shortcode_html' ) );
    }
    public function ct_tw_mapping(){
// Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }

        // Map the block with vc_map()
        vc_map(
            array(
                'name' => __('CT Twitter Feeds', 'text-domain'),
                'base' => 'ct_twitter',
                'description' => __('Displaying twitter feeds', 'text-domain'),
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
                        'description' => 'Write page ID from which you want display feeds',
                        'param_name' => 'id',
                        'value' => ''
                    ),

                    array(
                        'type' => 'checkbox',
                        'heading' => 'Embede Images',
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
                        'heading' => 'Cache',
                        'description' => 'How often widget has got to refresh (in seconds)? Default it is 300 seconds',
                        'param_name' => 'cache',
                        'value' => 300
                    )
                ),
            )
        );
    }   //end of method

    public function  is_there_img ( $posttext ){
        if( strpos($posttext, 'pic') === false) {
            return false;
        }
        return true;
    }   //end of method

    public function  want_post( $posttext ){
        // is it wanted post ?
        if( strpos( $posttext, "u-dir js-ellipsis" ) !== false) {
            return false;
        }
        return true;
    }

    public function parse_scraps( $posttext ){
        //deleting trash
        $string = '';
        $pi = strpos( $posttext, '>' ) + 1 ;

        $string .= substr( $posttext, 0, $pi );
        $string2 = str_replace( $string, "", $posttext );

        //deleting image href; I don't want to display href
        $string3 = $string2;

        if( $this->is_there_img( $posttext )){
            $pichfref = '';
            $i = strrpos($posttext, '<a href="https://t.co/' );

            $firstoffset = strlen( $posttext ) - $i;
            $pichfref .= substr( $posttext, $i, $firstoffset );
            $string3 = str_replace( $pichfref, "", $string2 );
        }

        return $string3;
    }   //end of method

    public function  get_img_src( $posttext ){
        $the_site = "https://";
        $the_tag = "div";
        $the_class = "AdaptiveMedia-photoContainer js-adaptive-photo ";

        if( !$this->is_there_img($posttext) ){
            return;
        }
        $i = strpos($posttext, 'pic');
        $firstoffset = strlen( $posttext ) - $i;
        $the_site .= substr($posttext, $i, $firstoffset);

        $html = file_get_contents($the_site);
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $html_img = '<img class="ct-tweet-img" src="';
        foreach ($xpath->query('//'.$the_tag.'[contains(@class,"'.$the_class.'")]/img') as $item) {

            $img_src = $item->getAttribute('src');
        }
        $html_img .= $img_src . '"></img>';
        $ahref ='<a href="' . $the_site . '">' . $html_img . '</a>';
        return $ahref;
    }   //end of method

    public function get_tagname( $json ){
        $tagname = $json ['user'];
        $tagname = substr( $tagname, 1);
        return $tagname;
    }   //end of method

    public function get_avatar( $json ){
        $avatar = $json ['img'];
        $avatar = '<img  class="ct-tw-av-img" src="' . $avatar . '"></img>';
        return $avatar;
    }   //end of method

    public function  html_post ( $json, $tagname, $avatar, $twhref, $embed_images ){
        //feed text
        $texthtml = $json[ 'html' ];
        $textparsed = $this->parse_scraps( $texthtml);

        //feed date
        $time = $json['date'];
        $date = date("M d, y", $time);

        //feed
        $posthtml = '<hr class="ct-feed-line"><div class="ct-tweet-feed">
                     <div class="ct-brand-logo">
                     <i class="fa fa-twitter"></i>
                     </div>';

        $head = '<div class="ct-post-header">
        <div class="ct-tweet-avatar">
        <a class="ct-tweet-author" href="' . $twhref . '">' . $avatar . '</a>
        </div>
        <div class="ct-username">
        <a class="ct-author-link" href="' . $twhref . '">
        <span class="ct-tagname">@' . $tagname . '</span>
        </a>
        </div>
        </div>';

        $posttext = '<div class="ct-post-content"><p class="ct-tweet-text">' . $textparsed . '</p>';

        //feed img - if exist
        $img_src = '';
        $img = '';
        if( $embed_images ){
            $img_src .= $this->get_img_src( $json['text'] );
            $img .= '<div class="ct-tweet-img">' . $img_src . '</div>';
        }

        $twdata = '<div class="ct-tweet-date" id="ct-hover"><time class="ct-tweet-post-date">' . $date . '</time></div>';
        $posthtml .= $head . $posttext . $img . '</div>' . $twdata . '</div>';
        return $posthtml;

    }   //end of method

    public function shortcode_html($atts) {
        extract(shortcode_atts( array(
            'style' => 'light',
            'embed_images'=> '',
            'limit' => 21,
            'length_limit' => 100,
            'cache' => 300,
            'id' => ''
        ), $atts));

        $pluginpath = plugins_url('adam-test-2/assets/css/');
        $style = $style . '.css';
        wp_register_style( 'ct_stylesheet2', $pluginpath . $style);
        wp_enqueue_style( 'ct_stylesheet2' );

        $string = '';
        if($id !== ''){

            $output = get_transient('tweeter_feeds');
            if($output === false){
                $str = user_tweets( $id );
                $json = json_decode( $str, true );

                //echo '<pre>' . print_r($json, true) . '</pre>';

                /**
                foreach ( $json as $key => $value ){
                echo $key . '=> ' . $value . '<br />';
                }
                 * */
                //getting tagname and avatar
                $tagname = $this->get_tagname( $json[ 'tweets' ][ 0 ] );
                $avatar = $this->get_avatar( $json[ 'tweets' ][ 0 ]);
                $twhref = 'https://twitter.com/' . $tagname;

                //creating widget structure
                $header = '<div class="ct-tweet-widget-header">
                <h1 class="ct-widget-headline">Tweets
                <span class="ct-tag-name"> by <a href="' . $twhref . '" class="twitter-timeline-link"> @' . $tagname . '</a></span>
                </h1>
                </div>';

                $postsbody = '<div class="ct-widget-body">
                <ol class="ct-widget-posts">';

                $string .= '<div class="ct-tweet-widget"><div class="ct-tweet-ac-widget">' . $header . $postsbody;
                //creating post structure
                foreach ( $json['tweets'] as $key => $value ){
                    if($key >= $limit ) break;
                    if( !$this->want_post( $json['tweets'][$key]['html'] ) ){
                        $limit++;
                        continue;
                    }
                    $string .= '<li class="ct-widget-post" id="ct-hover">';
                    $string .= $this->html_post( $json['tweets'][$key], $tagname, $avatar, $twhref, $embed_images );
                    $string .= '</li>';
                }
                $footer = '<div class="ct-widget-footer">
                <hr class="ct-feed-line">
                <span class="ct-footer">
                <a href="' . $twhref . '" class="twitter-timeline-link ct-footer">View on Twitter</a>
                </span>
                </div>';
                $string .= '</ol></div>' . $footer . '</div></div>';
                $output = $string;
                set_transient( 'tweeter_feeds', $string, $cache);

            }   //end of if statement - creating new content
            //release final product
            return $output;
        }
        return;
    }   //end of method
}   //end of class

new Fw_Ct_Twitter();