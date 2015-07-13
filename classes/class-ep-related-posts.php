<?php
 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class EP_Related_Posts{
	/**
	 * Placeholder method
	 *
	 * @since 0.0.1
	 */
	public function __construct() {}
	
	/**
	 * Return singleton instance of class
	 *
	 * @return EP_Related_Posts
	 * @since 0.0.1
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance  ) {
			$instance = new self();
		}

		return $instance;
	}

}
EP_Related_Posts::factory();