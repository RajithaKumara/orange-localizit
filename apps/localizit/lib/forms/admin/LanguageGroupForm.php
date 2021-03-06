<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LanguageGroupForm
 *
 * @author waruni
 */
class LanguageGroupForm extends BaseForm {
   

    public function configure() {

        $this->setWidgets(array(
            'group_name' => new sfWidgetFormInputText(),
        ));

        $this->setValidators(array(
            'group_name' => new sfValidatorString(array(), array('required' => 'Group name required.')),
        ));

        $this->widgetSchema->setNameFormat('add_language_group[%s]');
        $post_validator = new sfValidatorAnd();
    }

}

