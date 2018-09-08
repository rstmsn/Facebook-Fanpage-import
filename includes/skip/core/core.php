<?php
/**
 * Skip startup class
 *
 * This class loads all necessary files, functions and variables to get the functions and all necessary files included.
 *
 * @package Skip
 * @since 1.0
 * @ignore
 */
namespace skip\v1_0_0;
 
class Init{ 
	
	/**
	 * The plugin version
	 */
	const VERSION 	= '1.0.0';
	
	/**
	 * Minimum required WP version
	 */
	const MIN_WP 	= '4.1.0';
	
	/**
	 * Minimum required PHP version
	 */
	const MIN_PHP 	= '5.3.3';
	
	/**
	 * Actual Layout
	 */
	var $layout;
	
	/**
	 * All layouts
	 */
	var $layouts = array();
	
	/**
	 * Default Layout
	 */
	var $default_layout = 'bootstrap';
	
	/**
	 * Starting up!
	 * 
	 * This starts the Skip Framework.
	 * 
	 * @param array $args Options for starting Skip.
	 * @since 1.0
	 */
	public function __construct( $args = array() ){
		global $skip_javascripts, $skip_used_dialog;
	
		$skip_javascripts = array();
		$skip_used_dialog = FALSE;
		
		$defaults = array(
			'components' => array( 
				'tabs', 
				'accordion',
				'autocomplete',
				'button',
				'checkbox',
				'color',
				'editor',
				'file',
				'wp-file',
				'radio',
				'select',
				'text',
				'textarea',
			),
		);

		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		$this->components = $components;
		
		$this->constants();
		$this->includes();
		
		if( is_admin() ):
			add_action( 'admin_head', array( $this, 'css' ), 100 );
			add_action( 'admin_footer', array( $this, 'js' ), 100 );
			add_action( 'admin_print_footer_scripts', array( $this, 'echo_javascripts' ), 100 );
		else:
			add_action( 'wp_enqueue_scripts', array( $this, 'css' ), 100 );
			add_action( 'wp_footer', array( $this, 'echo_javascripts' ), 100 );
			add_action( 'wp_enqueue_scripts', array( $this, 'js' ), 100 );
		endif;
	}
	
	/**
	 * Enqueue CSS
	 * @package Skip
	 * @since 1.0
	 */
	public function css(){
		global $skip_used_dialog;
		
		wp_dequeue_style( 'wp-jquery-ui-dialog' );

		wp_register_style( $this->layout[ 'name' ], $this->layout[ 'css' ] );
	    wp_enqueue_style( $this->layout[ 'name' ] );
	
	
		wp_register_style( 'colorpicker', SKIP_URL_PATH . 'includes/js/jquery/colorpicker/jquery.colorpicker.css' );
	    wp_enqueue_style( 'colorpicker' );
		
		wp_register_style( 'skip-styles', SKIP_URL_PATH . 'includes/css/skip.css' );
	    wp_enqueue_style( 'skip-styles' );
	}
	
	/**
	 * Enqueue JS
	 * @package Skip
	 * @since 1.0
	 */
	public function js(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-button' );
		
		wp_enqueue_script(
			'skip',
			SKIP_URL_PATH . 'includes/js/skip.js',
			array('jquery'),
			'1.0.0',
			TRUE
		);
		
		// Accordion
		if( in_array( 'accordion', $this->components ) ):
			wp_register_script( 'jquery-cookies', SKIP_URL_PATH . 'includes/js/jquery/skip-cookies.js' );
			wp_enqueue_script( 'jquery-cookies' );
			
			wp_enqueue_script( 'jquery-ui-accordion' );
		endif;
		
		// Tabs
		if( in_array( 'tabs', $this->components ) ):
			wp_register_script( 'jquery-cookies', SKIP_URL_PATH . 'includes/js/jquery/skip-cookies.js' );
			wp_enqueue_script( 'jquery-cookies' );
			
			wp_enqueue_script( 'jquery-ui-tabs' );
		endif;
		
		// Autocomplete
		if( in_array( 'color', $this->components ) ):
			wp_enqueue_script( 'jquery-ui-autocomplete' );
		endif;
		
		// Color
		if( in_array( 'color', $this->components ) ):
			wp_register_script( 'jquery-colorpicker', SKIP_URL_PATH . 'includes/js/jquery/colorpicker/jquery.colorpicker.js' );
			wp_enqueue_script( 'jquery-colorpicker' );
		endif;
		
		// File		
		if( in_array( 'wp-file', $this->components )  ):
			wp_enqueue_media();
			wp_enqueue_script( 'thickbox' );
		endif;
	}
	
	/**
	 * Printing Skip Javascripts
	 * @since 1.0
	 */
	public function echo_javascripts(){
		global $skip_javascripts;
		
		if( count( $skip_javascripts ) > 0 ):
			echo '<!-- skip JS //-->';
			echo '<script type="text/javascript">';
			echo 'jQuery(document).ready(function($){';
			echo implode( $skip_javascripts );
			echo '});';
			echo '</script>';
			echo '<!-- End skip JS //-->';
		endif;
	}
	
	
	/**
	 * Setting constants
	 * @since 1.0
	 */
	private function constants(){
		if( !defined( 'SKIP_VERSION') ) define( 'SKIP_VERSION',	self::VERSION );
		if( !defined( 'SKIP_PATH') ) define( 'SKIP_PATH', $this->get_path() );
		if( !defined( 'SKIP_URL_PATH') ) define( 'SKIP_URL_PATH', $this->get_url_path() );
		if( !defined( 'SKIP_DELIMITER') ) define( 'SKIP_DELIMITER', '&' );
	}

	/**
	* Getting URL Path of Skip
	* @since 1.0
	*/
	private function get_url_path(){
		$sub_path = substr( SKIP_PATH, strlen( ABSPATH ), ( strlen( SKIP_PATH ) ) );
		$script_url = get_bloginfo( 'wpurl' ) . '/' . $sub_path;
		return $script_url;
	}
	
	/**
	* Getting URL Path of Skip
	* @since 1.0
	*/
	private function get_path(){
		$sub_path = substr( dirname(__FILE__), strlen( ABSPATH ), ( strlen( dirname(__FILE__) ) - strlen( ABSPATH ) - 4 ) );
		$script_path = ABSPATH . $sub_path;
		return $script_path;
	}
	
	/**
	 * Includes files for Skip
	 * @since 1.0
	 */	
	private function includes(){
		
		/*
		 * General scripts
		 */
		require_once( SKIP_PATH . '/core/display.php' );
		require_once( SKIP_PATH . '/core/functions/collected.php' );
		
		require_once( SKIP_PATH . '/elements/element.php' );
		require_once( SKIP_PATH . '/elements/html_element.php' );
		
		/*
		 * Form Elements
		 */
		require_once( SKIP_PATH . '/elements/forms/form.php' );
		require_once( SKIP_PATH . '/elements/forms/form-element.php' );
		require_once( SKIP_PATH . '/elements/forms/values.php' );
		
		// Text
		if( in_array( 'text', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/textfield.php' );
		
		// Textarea
		if( in_array( 'textarea', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/textarea.php' );
		
		// Text
		if( in_array( 'textarea', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/editor.php' );
		
		// Autocomplete
		if( in_array( 'autocomplete', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/autocomplete.php' );
		
		// Color
		if( in_array( 'color', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/color.php' );
		
		// File
		if( in_array( 'file', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/file.php' );
		
		// WP File
		if( in_array( 'wp-file', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/wp-file.php' );
		
		// Checkbox
		if( in_array( 'checkbox', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/checkbox.php' );
		
		// Radio
		if( in_array( 'radio', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/radio.php' );
		
		// Select
		if( in_array( 'select', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/select.php' );
		
				// Select
		if( in_array( 'button', $this->components ) )
			require_once( SKIP_PATH . '/elements/forms/button.php' );
		
		// Requies fields
		require_once( SKIP_PATH . '/elements/forms/hidden.php' ); // Needed by other functions
	}
}

/**
 * <pre>skip_start( $args );</pre>
 * 
 * This function initializes all scripts for getting on with Skip. It have to be started
 * if you want to use framework functions. Be sure to start it in the 'init' Actionhook of WordPress.
 * 
 * <b>Initializing Skip</b>
 * <code>
 * function my_skip_init(){
 * 	skip_start();
 * }
 * // Adding Action to init Actionhook of WordPress
 * add_action( 'init', 'my_skip_init' );
 * </code>
 * 
 * <b>Using Layouts</b>
 * 
 * Skip includes several jQuery UI layouts. Most except of 'absolution', 'aristo' and 'bootsrap' 
 * are loaded from ajax.googleapis.com to give a better performance. Default layout is bootstrap.
 * 
 * <code>
 * function my_skip_init(){
 * 	$args[ 'layout' ] = 'aristo'; // Use the aristo Layout
 * 	skip_start( $args );
 * }
 * add_action( 'init', 'my_skip_init' );
 * </code>
 * 
 * List of included Layouts:
 * <ul>
 * <li>absolution</li>
 * <li>aristo</li>
 * <li>bootstrap (default)</li>
 * </ul>
 * 
 * List of external loaded Layouts:
 * <ul>
 * <li>black-tie</li>
 * <li>blitzer</li>
 * <li>cupertino</li>
 * <li>dark-hive</li>
 * <li>dot-luv</li>
 * <li>eggplant</li>
 * <li>excite-bike</li>
 * <li>flick</li>
 * <li>hot-sneaks</li>
 * <li>humanity</li>
 * <li>le-frog</li>
 * <li>mint-chock</li>
 * <li>overcast</li>
 * <li>pepper-grinder</li>
 * <li>redmond</li>
 * <li>smoothness</li>
 * <li>south-street</li>
 * <li>start</li>
 * <li>sunny</li>
 * <li>swanky-purse</li>
 * <li>trontastic</li>
 * <li>ui-darkness</li>
 * <li>ui-lightness</li>
 * <li>vader</li>
 * </ul>
 * 
 * Adding own Layouts:
 * <code>
 * function my_skip_init(){
 *		$args[ 'extra_layouts' ][] = array( 
 * 		'name' => 'myown', 
 * 		'css' => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/overcast/jquery-ui.css'
 * 		);  
 *		$args[ 'layout' ] = 'myown'; // Use the own Layout
 *		skip_start( $args );
 * }
 * add_action( 'init', 'my_skip_init' );
 * </code>
 * Create your own jQueryUI CSS using <a href="http://jqueryui.com/themeroller/" target="_blank">jQueryUI ThemeRoller</a>.
 * 
 * <b>Selecting Components</b>
 * 
 * In normal case all components of the Framework are loaded. If not necessary, 
 * select which components have to be loaded. After that only the components you
 * have chosen will be loaded.
 * 
 * <code>
 * function my_skip_init(){
 * 	$args[ 'components' ] = array( 'tabs', 'text', 'autocomplete' );
 * 	skip_start( $args );
 * }
 * add_action( 'init', 'my_skip_init' );
 * </code>
 * 
 * List of Components:s
 * 
 * <ul>
 * <li>tabs</li>
 * <li>accordion</li>
 * <li>autocomplete</li>
 * <li>button</li>
 * <li>checkbox</li>
 * <li>color</li>
 * <li>editor</li>
 * <li>file</li>
 * <li>radio</li>
 * <li>select</li>
 * <li>text</li>
 * <li>textarea</li>
 * </ul>
 *
 * @param array $args Options for starting Skip.
 * @package Skip
 * @since 1.0
 */
function start( $args = array() ){
	if( !defined( 'SKIP_VERSION' ) )
		$skip = new Init( $args );
}