<?php
namespace Grav\WP;

class Analytics {
	/**
	 * Add Google Tag Manager code to the head and body.
	 * `do_action('global_head_top_content')` needs to be at the top of your <head> section
	 * `do_action('global_body_top_content')` needs to be at the top of your <body> section
	 *
	 * @param string $gtm_id The Google Tag Manager ID in `GTM-XXXXXX` format
	 *
	 * @author DF
	 */
	public static function add_gtm_code($gtm_id) {
		if (!$gtm_id) {
			return;
		}

		add_action('global_head_top_content', function() use (&$gtm_id) {
			?>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','<?php echo esc_attr($gtm_id); ?>');</script>
			<!-- End Google Tag Manager -->
			<?php
		});

		add_action('global_body_top_content', function() use (&$gtm_id) {
			?>
			<!-- Google Tag Manager (noscript) -->
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager (noscript) -->
			<?php
		});
	}
}
