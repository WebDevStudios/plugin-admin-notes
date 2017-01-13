<?php

class WDSPP_Dynamic_form_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WDSPP_Dynamic_form') );
	}

	function test_class_access() {
		$this->assertTrue( wds_plugin_police()->dynamic-form instanceof WDSPP_Dynamic_form );
	}
}
