<?php

/**
 * DokuWiki Plugin simplemap (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <mic.grosse@googlemail.com>
 */

declare(strict_types=1);

use dokuwiki\Extension\SyntaxPlugin;

final class syntax_plugin_simplemap extends SyntaxPlugin
{
    /**
     * @return string Syntax mode type
     */
    public function getType(): string
    {
        return 'substition';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType(): string
    {
        return 'block';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort(): int
    {
        return 50;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode): void
    {
        $this->Lexer->addSpecialPattern('{{simplemap>.*?}}', $mode, 'plugin_simplemap');
    }

    /**
     * Handle matches of the simplemap syntax
     *
     * @param string          $match   The match of the syntax
     * @param int             $state   The state of the handler
     * @param int             $pos     The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler): array
    {
        return $this->parseMatch($match);
    }

    //  {{simplemap>osm?lat=50.234&long=13.123}}
    private function parseMatch(string $match): array
    {
        $match = substr($match, strlen('{{simplemap>'), -strlen('}}'));
        [$type, $query] = explode('?', $match, 2);
        parse_str($query, $data);
        $data['type'] = $type;

        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data): bool
    {
        if ($mode != 'xhtml') {
            return false;
        }

        $long = $data['long'];
        $lat = $data['lat'];

        $iframeEndpoint = 'https://www.openstreetmap.org/export/embed.html';
        $iframeQueryParams = [
            'bbox' => ($long - 0.004) . ',' . ($lat - 0.002) . ',' . ($long + 0.004) . ',' . ($lat + 0.002),
            'layer' => 'mapnik',
            'marker' => "$lat,$long"
        ];
        $src = $iframeEndpoint . '?' . http_build_query($iframeQueryParams);

        $iframeHtml = '<iframe width="425" height="350" src="' . $src . '"></iframe>';
        $linkHref = "https://www.openstreetmap.org/#map=14/$lat/$long";
        $link = "<a href=\"$linkHref\" target=\"_blank\">" . $this->getLang('view larger map') . "</a>";
        $renderer->doc .= $iframeHtml . '<br>' . $link;

        return true;
    }
}

// vim:ts=4:sw=4:et:
