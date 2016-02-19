### Wordpress Cycle Gallery Plugin
* Author: eutelnet
* Tags: gallery, wp, carousel, cycle
* Requires at least: 3.8
* Tested up to: 4.3.1

Customizable Wordpress Gallery Plugin buit upon Malsup cycle2 with lightcase support

#### Installation

1. Upload the entire 'etl-wp-gallery' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Customize view on plugin settings page

4. on single post of choosen post types add echo `do_shortcode('[etl-gallery id="'. get_the_ID() .'"]')` where you want gallery to appear

##### @TODO
```
http://kenwheeler.github.io/slick/ instead of cycle2
```
