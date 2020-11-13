<?php

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 *  * All DokuWiki plugins to extend the parser/rendering mechanism
 *   * need to inherit from this class
 *    
 *    */
class syntax_plugin_recent extends DokuWiki_Syntax_Plugin {

    function getInfo(){
        return array(
            'author' => 'iDo',
            'email'  => 'iDo@woow-fr.com',
            'date'   => '12/08/2005',
            'name'   => 'Recent Plugin',
            'desc'   => 'Affiche les recents d\'un wiki',
            'url'    => 'http://www.dokuwiki.org/plugin:recent',

        );

    }
    /**
     *      * What kind of syntax are we?
     *           */
    function getType(){
        return 'substition';

    }
    /**
     *      * Where to sort in?
     *           */
    function getSort(){
        return 105;

    }
    /**
     *      * Connect pattern to lexer
     *           */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern("{{revisions}}",$mode,'plugin_recent');

    }
    /**
     *      * Handle the match
     *           */
    function handle($match, $state, $pos, &$handler){
        return true;

    }    

    /**
     *      * Create output
     *           */
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            global $ID;
            $changes_file = metaFN($ID, ".changes");
            $changes = file_get_contents($changes_file);
            $changes_lines = preg_split("/\r\n|\n\r|\n/", $changes);

            $toPage = "^ Revision date ^ Revision summary ^ Changed by ^\n";

            foreach($changes_lines as $line) {
                $columns = preg_split("/\t/", $line);
                $sum = $columns[5];
                $date = Date("Y-m-d h:i", $columns[0]);
                if ($date) {
                    $loginname = $columns[4];

                    global $auth;
                    $userdata = $auth->getUserData($loginname);
                    $user = $userdata['name'];


                    if ($sum != "" && trim(substr($sum,0, 1)) != "[") {
                        if (trim($sum) == "Approved") {
                            $toPage .= "| @lightgreen:".$date." | @lightgreen:".$sum." | @lightgreen:".$user." |\n";
                        } else {
                            $toPage .= "| ".$date." | ".ucfirst($sum)." | ".$user." |\n";
                        }
                    }
                }
            }

            $renderer->doc .= p_render('xhtml',p_get_instructions($toPage),$notused);

            return true;

        }
        return false;

    }

    function _Rethtml_recent($first=0) {
        ob_start();
        html_recent($first);
        return ob_get_clean();

    }    


}

?>
