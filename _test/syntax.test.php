<?php

declare(strict_types=1);

namespace dokuwiki\plugin\simplemap\test;

use Doku_Handler;
use syntax_plugin_simplemap;
use DokuWikiTest;

/**
 * Tests for the syntax component of the simplemap plugin
 *
 * @group plugin_simplemap
 * @group plugins
 */
class simplemap_syntax_test extends DokuWikiTest {
    protected $pluginsEnabled = ['simplemap'];

    public static function parseMatch_testdata () {
        return [
            [
                '{{simplemap>osm?lat=50.234&long=13.123}}',
                [
                    'type' => 'osm',
                    'lat' => '50.234',
                    'long' => '13.123',
                ],
                'simple example'
            ],
        ];
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
        $actual_output = $syntax->handle($input, 5, 1, new Doku_Handler());

        // assert
        self::assertEquals($expected_output, $actual_output, $msg);
    }
}
