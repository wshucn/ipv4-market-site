<!DOCTYPE html>
<html lang='en' itemscope itemtype="http://schema.org/WebPage">
<head>
<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">

<meta name='viewport' content='width=device-width,initial-scale=1,viewport-fit=cover'>
<meta name='msapplication-tap-highlight' content='no'>
<meta name='HandheldFriendly' content='True'>

<meta name='format-detection' content='telephone=no'>
<meta name='pinterest' content='nohover'>
<script>

(function(h,o,t,j,a,r){

h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};

h._hjSettings={hjid:1735543,hjsv:6};

a=o.getElementsByTagName('head')[0];

r=o.createElement('script');r.async=1;

r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;

a.appendChild(r);

})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');

</script>

<?php wp_site_icon(); ?>

<?php
// We're about to run wp_head().
$post_id  = get_queried_object_id();
$post_obj = get_post( $post_id );
if ( ! empty( $post_obj ) ) {
	// Process the post content first so that shortcodes can preload things in <head>

	// Processing the content will clear any WooCommerce notices, which will not display
	// when the page content is later echoed via the_content(). We need to store wc_notices
	// and re-set them after the content has been filtered.

	if ( class_exists( 'woocommerce' ) ) {
		$notices = WC()->session->get( 'wc_notices', array() );
	}

	// This is not outputting anything, but allows shortcodes to set up things in wp_head.
	// Namely, <preload> tags.
	do_shortcode( get_the_content() );

	if ( class_exists( 'woocommerce' ) ) {
		WC()->session->set( 'wc_notices', $notices );
	}
	unset( $post_id, $post_obj, $notices );
}

// Now we can run wp_head() since the shortcodes have set up the preloads.
wp_head();
?>

<!-- Global site tag (gtag.js) - Google Ads: 1000913911 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-1000913911"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'AW-1000913911');
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WLSVZG7');</script>
<!-- End Google Tag Manager -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-100090286-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-100090286-1');
</script>

<!-- Xandr Universal Pixel - Initialization (include only once per page) -->
<script>
!function(e,i){if(!e.pixie){var n=e.pixie=function(e,i,a){n.actionQueue.push({action:e,actionValue:i,params:a})};n.actionQueue=[];var a=i.createElement("script");a.async=!0,a.src="//acdn.adnxs.com/dmp/up/pixie.js";var t=i.getElementsByTagName("head")[0];t.insertBefore(a,t.firstChild)}}(window,document);
pixie('init', '9b5cbb05-dd2f-47d4-a77d-52b3d817a0d7');
</script>

<!-- Xandr Universal Pixel - PageView Event -->
<script>
pixie('event', 'PageView');
</script>
<noscript><img src="https://ib.adnxs.com/pixie?pi=9b5cbb05-dd2f-47d4-a77d-52b3d817a0d7&e=PageView&script=0" width="1" height="1" style="display:none"/></noscript>
<script>!function () {var reb2b = window.reb2b = window.reb2b || [];if (reb2b.invoked) return;reb2b.invoked = true;reb2b.methods = ["identify", "collect"];reb2b.factory = function (method) {return function () {var args = Array.prototype.slice.call(arguments);args.unshift(method);reb2b.push(args);return reb2b;};};for (var i = 0; i < reb2b.methods.length; i++) {var key = reb2b.methods[i];reb2b[key] = reb2b.factory(key);}reb2b.load = function (key) {var script = document.createElement("script");script.type = "text/javascript";script.async = true;script.src = "https://s3-us-west-2.amazonaws.com/b2bjsstore/b/" + key + "/reb2b.js.gz";var first = document.getElementsByTagName("script")[0];first.parentNode.insertBefore(script, first);};reb2b.SNIPPET_VERSION = "1.0.1";reb2b.load("3961Y0HE0RNG");}();</script>

</head>
<?php
$body_attrs = array(
	'class'     => buildClass( get_body_class() ),
	'style'     => 'overflow-x: hidden',
);
echo buildAttributes( $body_attrs, 'body' );
?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WLSVZG7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php wp_body_open();

// Output the WooCommerce Store Notice
if ( function_exists( 'woocommerce_demo_store' ) ) {
	woocommerce_demo_store();
}
?>
