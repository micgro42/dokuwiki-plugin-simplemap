<?php

declare(strict_types=1);

namespace dokuwiki\plugin\simplemap\test;

use Doku_Handler;
use dokuwiki\test\mock\Doku_Renderer;
use syntax_plugin_simplemap;
use DokuWikiTest;

/**
 * Tests for the syntax component of the simplemap plugin
 *
 * @group plugin_simplemap
 * @group plugins
 */
class SyntaxTest extends DokuWikiTest {
    protected $pluginsEnabled = ['simplemap'];

    public static function parseMatchTestDataProvider () {
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
     * @dataProvider parseMatchTestDataProvider
     *
     * @param $input
     * @param $expectedOutput
     * @param $msg
     */
    public function testParseMatch($input, $expectedOutput, $msg) {
        // arrange
        /** @var syntax_plugin_simplemap $syntax */
        $syntax = plugin_load('syntax', 'simplemap');

        // act
        $actualOutput = $syntax->handle($input, 5, 1, new Doku_Handler());

        // assert
        self::assertEquals($expectedOutput, $actualOutput, $msg);
    }

    public function testRendererXHTML() {
        /** @var syntax_plugin_simplemap $syntax */
        $syntax = plugin_load('syntax', 'simplemap');
        $testData = [
            'type' => 'osm',
            'lat' => '50.234',
            'long' => '13.123',
        ];
        $mockRenderer = new Doku_Renderer();

        $actualStatus = $syntax->render('xhtml', $mockRenderer, $testData);

        self::assertTrue($actualStatus);
        $expectedHTML = '<iframe width="425" height="350" src="https://www.openstreetmap.org/export/embed.html?bbox=13.119%2C50.232%2C13.127%2C50.236&amp;layer=mapnik&amp;marker=50.234%2C13.123"></iframe><br><a href="https://www.openstreetmap.org/#map=14/50.234/13.123" target="_blank">View Larger Map</a>';
        self::assertSame($expectedHTML, $mockRenderer->doc);
    }

    public function testRendererMeta() {
        /** @var syntax_plugin_simplemap $syntax */
        $syntax = plugin_load('syntax', 'simplemap');
        $testData = [
            'type' => 'osm',
            'lat' => '50.234',
            'long' => '13.123',
        ];
        $mockRenderer = new Doku_Renderer();

        $actualStatus = $syntax->render('meta', $mockRenderer, $testData);

        self::assertFalse($actualStatus);
        self::assertSame('', $mockRenderer->doc);
    }
}
