<?php
/**
 * DokuWiki Plugin simplemap (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <mic.grosse@googlemail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_simplemap extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 50;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{simplemap>.*?}}',$mode,'plugin_simplemap');
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
    public function handle($match, $state, $pos, Doku_Handler $handler){
        $data = $this->parseMatch($match);

        return $data;
    }

    //  {{simplemap>osm?lat=50.234&long=13.123}}
    public function parseMatch($match) {
        $match = substr($match, strlen('{{simplemap>'), -strlen('}}'));
        list($type, $query) = explode('?', $match, 2);
        $data['type'] = $type;

        $data = array_reduce(explode('&', $query), function($carry, $item) {
            list($key, $value) = explode('=', $item, 2);
            $carry[$key] = $value;
            return $carry;
        }, $data);

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
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;

        $long = $data['long'];
        $lat = $data['lat'];

        $src = 'http://www.openstreetmap.org/export/embed.html?bbox=' . ($long - 0.004) .'%2C' . ($lat - 0.002) . '%2C' . ($long + 0.004) . '%2C' . ($lat + 0.002) . '&amp;layer=mapnik';
        $src .= "&marker=$lat%2C$long";

        $html = '<iframe width="425" height="350" src="' . $src . '"></iframe>';
        $link = "<a href=\"https://www.openstreetmap.org/#map=14/$lat/$long\" target=\"_blank\">" . $this->getLang('view larger map') . "</a>";
        $renderer->doc .= $html . '<br>' . $link;

        return true;
    }
}

// vim:ts=4:sw=4:et:
