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

	function test_find_related() {
		$post_ids		 = array();
		$post_ids[ 0 ]	 = ep_create_and_sync_post( array( 'post_title' => 'Bacon Lettuce Tomato Sandwiches', 'post_content' => 'The best sandwich out there contains bacon.' ) );
		$post_ids[ 1 ]	 = ep_create_and_sync_post( array( 'post_title' => 'Pasta Salad', 'post_content' => 'Great for any barbeque, pasta salad takes the cake.' ) );
		$post_ids[ 2 ]	 = ep_create_and_sync_post( array( 'post_title' => 'Footlong Submarine Sandwich', 'post_content' => 'Enjoy this sandwich with bacon, turkey, ham or meatballs' ) );
		$post_ids[ 3 ]	 = ep_create_and_sync_post( array( 'post_title' => 'BBQ Spare Ribs', 'post_content' => 'Smoke pork ribs are delicious smothered in barbeque sauce.' ) );
		$post_ids[ 4 ]	 = ep_create_and_sync_post( array( 'post_title' => 'Grilled Cheese', 'post_content' => 'The ultimate compliment to tomato soup is the grilled cheese sandwich.' ) );

		ep_refresh_index();

		// Find some related sandwiches...
		$related = ep_find_related( $post_ids[ 0 ], 2 );
		$results = array( $related[ 0 ]->ID, $related[ 1 ]->ID );
		$this->assertContains( $post_ids[ 2 ], $results );
		$this->assertContains( $post_ids[ 4 ], $results );
		
		// Find some barbeque...
		$related = ep_find_related( $post_ids[ 3 ], 2 );
		$results = array( $related[ 0 ]->ID, $related[ 1 ]->ID );
		$this->assertContains( $post_ids[ 1 ], $results );
	}

}

if ( !function_exists( 'ep_create_and_sync_post' ) ) {

	/**
	 * Create a WP post and "sync" it to Elasticsearch. We are mocking the sync
	 *
	 * @param array $post_args
	 * @param array $post_meta
	 * @param int $site_id
	 * @since 0.9
	 * @return int|WP_Error
	 */
	function ep_create_and_sync_post( $post_args = array(), $post_meta = array(), $site_id = null ) {
		if ( $site_id != null ) {
			switch_to_blog( $site_id );
		}

		$post_types			 = ep_get_indexable_post_types();
		$post_type_values	 = array_values( $post_types );

		$args = wp_parse_args( $post_args, array(
			'post_type'		 => $post_type_values[ 0 ],
			'post_status'	 => 'publish',
			'post_title'	 => 'Test Post ' . time(),
		) );

		$post_id = wp_insert_post( $args );

		// Quit if we have a WP_Error object
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( !empty( $post_meta ) ) {
			foreach ( $post_meta as $key => $value ) {
				// No need for sanitization here
				update_post_meta( $post_id, $key, $value );
			}
		}

		// Force a re-sync
		wp_update_post( array( 'ID' => $post_id ) );

		if ( $site_id != null ) {
			restore_current_blog();
		}

		return $post_id;
	}

}