<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of makeConnectionAliveAjaxAction
 *
 * @author jathu
 */
class makeConnectionAliveAjaxAction extends sfAction {
    
     public function execute($request) {
        echo 'success';
        return sfView::NONE;
    }

}

?>
