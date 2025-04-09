<?php

// registered jquery and style
add_action('init', 'register_script_ticker');
function register_script_ticker() {
    if(! isset($_GET['action'])){
        //wp_register_script( 'jquery_ticker', 'https://code.jquery.com/jquery-3.4.1.min.js' );
        wp_register_script( 'tweenmax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js' );
        wp_register_script( 'custom_js', plugins_url('/ipv4-ticker/js/ticker.js'));
        wp_register_style( 'new_style', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
    }
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style_ticker');
function enqueue_style_ticker(){
    if(! isset($_GET['action'])){
        wp_enqueue_style( 'new_style' );
        wp_enqueue_script('jquery_ticker');
        wp_enqueue_script('tweenmax');
        wp_enqueue_script('custom_js');
        } else {
            echo 'Gogi';
            //'<script>console.log("TEST GIL"); jQuery(document).ready(function() { jQuery(".ticker-main").remove() }) </script>';
        }
}


function load_data_ipv4()
{
    $obj = (object)array();
    $post_data = array(
        'limit' => 25,
        'offset' => 0,
        'filter' => $obj

    );

    $post_data2 = json_encode($post_data);
    $response = wp_remote_post('https://y1dq7hifob.execute-api.eu-west-1.amazonaws.com/prod/api/priorSales', array(
            'method' => 'POST',
            'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
            'body' => $post_data2
        )
    );
    if($response instanceof WP_Error ) {
        return;
    }

    $array = json_decode($response['body'], true);


    echo '<div class="ticker-main">';
    echo '<a class="icon-wrapper-link" target="_blank" href="https://ipv4.global"><img class="the-icon" src="'.plugins_url('/ipv4-ticker/icon2.png').'" alt="IPV4 Icon"></a>';
    echo '<div class="ticker-desc">Prior Sales:</div>';
    echo '<a class="tickerwrapper auctions-ref-link" target="_blank" href="https://auctions.ipv4.global/prior-sales">';
    //echo '<div class="">';
    echo '<ul class="prior-list">';

    foreach ($array as $items) {
        if (is_array($items)) {
            foreach ($items as $key => $item) {
                echo '<li class="list-item">';
                $m = 0;

                foreach ($item as $k => $v) {

                    if ($m == 2) {
                        echo '<span class="the-items-new' . $m . '">/' . $v . " </span>";
                    } elseif ($m == 3) {
                        echo '<span class="the-items-new' . $m . '">' . $v . "</span>";
                    } elseif ($m == 5) {
                        echo '<span class="the-items-new' . $m . '"> $' . $v . "</span>";
                    }
                    $m++;


                }
                echo '</li>';

            }
        }


    }
    echo '</ul>';
    //echo '</div>';
    echo '</a>';
    echo '<input style="margin: auto; display: block; margin-top: 10px; direction: rtl" type="range" id="slider" value="35" min="5" max="65" step="1" />';
    echo '</div>';


}
