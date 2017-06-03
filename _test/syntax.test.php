<?php

/**
 * Tests for the sytax component of the simplemap plugin
 *
 * @group plugin_simplemap
 * @group plugins
 */
class simplemap_syntax_test extends DokuWikiTest {
    protected $pluginsEnabled = array('simplemap');

    public static function parseMatch_testdata () {
        return array(
            array(
                '{{simplemap>osm?lat=50.234&long=13.123}}',
                array(
                    'type' => 'osm1',
                    'lat' => '50.234',
                    'long' => '13.123',
                ),
                'simple example'
            ),
        );
    }

    /**
     * @dataProvider parseMatch_testdata
     *
     * @param $input
     * @param $expected_output
     * @param $msg
     */
    public  function test_parseMatch($input, $expected_output, $msg) {
        // arrange
        /** @var syntax_plugin_simplemap $syntax */
        $syntax = plugin_load('syntax', 'simplemap');

        // act
        $actual_output = $syntax->parseMatch($input);

        // assert
        $this->assertEquals($expected_output, $actual_output, $msg);
    }
}