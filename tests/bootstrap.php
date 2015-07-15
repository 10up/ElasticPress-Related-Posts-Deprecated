<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	$host = getenv( 'EP_HOST' );
	if ( empty( $host ) ) {
		$host = 'http://localhost:9200';
	}

	define( 'EP_HOST', $host );

	$elastic_press_file = dirname( dirname( dirname( __FILE__ ) ) ) . '/elasticpress/elasticpress.php';

	if ( ! file_exists( $elastic_press_file ) ) {
		die( 'ElasticPress is not present. Please make sure ElasticPress is installed and available' );
	}

	require dirname( dirname( dirname( __FILE__ ) ) ) . '/elasticpress/elasticpress.php'; // Load up ElasticPress
	
	require dirname( dirname( __FILE__ ) ) . '/elasticpress-related-posts.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
