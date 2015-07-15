<?php

class EP_RP_TestSingleSite extends WP_UnitTestCase {

	function setUp() {

		parent::setUp();

		$this->admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );

		wp_set_current_user( $this->admin_id );

		if ( !ep_elasticsearch_alive() ) {
			$this->fail( 'Error connecting to Elasticsearch with ElasticPress.' );
		}

		ep_delete_index();

		ep_put_mapping();

		ep_activate();

		EP_WP_Query_Integration::factory()->setup();
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

}
