<?php

class WDSPP_Plugin_police_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WDSPP_Plugin_police') );
	}

	function test_class_access() {
		$this->assertTrue( wds_plugin_police()->plugin-police instanceof WDSPP_Plugin_police );
	}

  function test_cpt_exists() {
    $this->assertTrue( post_type_exists( 'wdspp-plugin-police' ) );
  }
}
