<?php
/**
 * Facebook Fanpage Import Showdata Component.
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0-beta.7
 * @since   1.0.0
 * @license GPL 2
 *          Copyright 2016 Awesome UG (very@awesome.ug)
 *          This program is free software; you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License, version 2, as
 *          published by the Free Software Foundation.
 *          This program is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *          You should have received a copy of the GNU General Public License
 *          along with this program; if not, write to the Free Software
 *          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FacebookFanpageImportAdmin {
	var $name;

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );
		$this->includes();

		if ( 'status' == get_option( 'fbfpi_insert_post_type' ) ) {
			add_action( 'init', array( $this, 'custom_post_types' ), 11 );
		}
	}

	/**
	 * Including needed Files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		require_once( dirname( __FILE__ ) . '/settings.php' );
	}

	/**
	 * Creates Custom Post Types
	 *
	 * @since 1.0.0
	 */
	public function custom_post_types() {
		$args_post_type = array(
			'labels'      => array(
				'name'          => __( 'Status Messages', 'fbfpi-locale' ),
				'singular_name' => __( 'Status Message', 'fbfpi-locale' )
			),
			'public'      => true,
			'has_archive' => true,
			'supports'    => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'rewrite'     => array(
				'slug'       => 'status-message',
				'with_front' => true
			)
		);

		register_post_type( 'status-message', $args_post_type );
	}
}

$FacebookFanpageImportAdmin = new FacebookFanpageImportAdmin();
