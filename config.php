<?php
require_once INCLUDE_DIR . 'class.plugin.php';
class SugarCRMPluginConfig extends PluginConfig {

    // return a list of all the configurable options
    function getOptions() {
        return array(
            'url' => new TextboxField(array(
                'label' => 'SugarCRM URL',
                'hint' => 'SugarCRM rest api url',
                'configuration' => array('size'=>80, 'length'=>0,'placeholder'=>'http://localhost/sugar/service/v4_1/rest.php'),
            )),
            'username' => new TextboxField(array(
                'label' => 'Username',
                'hint' => 'SugarCRM rest api user name',
                'configuration' => array('size'=>20),
            )),
            'password' => new PasswordField(array(
                'label' => 'password',
                'configuration' => array('size'=>20),
            )),
            'confirm' => new PasswordField(array(
                'label' => 'confirm',
                'configuration' => array('size'=>20),
            )),
        );
    }

    function pre_save(&$config, &$errors) {
        global $msg;

        if (!$errors) {
            $msg = 'Configuration updated successfully';
        }
        return true;
     }
}

//     private $application = NULL;

