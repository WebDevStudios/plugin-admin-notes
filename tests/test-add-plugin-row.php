<?php

class WDSPP_Add_plugin_row_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WDSPP_Add_plugin_row') );
	}

	function test_class_access() {
		$this->assertTrue( wds_plugin_police()->add-plugin-row instanceof WDSPP_Add_plugin_row );
	}
}
