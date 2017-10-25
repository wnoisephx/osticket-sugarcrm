<?php
require_once INCLUDE_DIR . 'class.plugin.php';
class SugarCRMPluginConfig extends PluginConfig {

    // return a list of all the configurable options
    function getOptions() {
        return array(
            'conf' => new SectionBreakField(array(
                'label' => 'SugarCRM API',
                'hint' => 'Information needed to access the SugarCRM API',
            )),
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
            'search' => new SectionBreakField(array(
                'label' => 'Search Modules',
                'hint' => 'Modules to Search',
            )),
            'search-contacts' => new BooleanField(array(
                'label' => 'Search Contacts',
                'default' => true,
                'configuration' => array(
                    'desc' => 'SugarCRM Contacts will be searched when creating users',
                ),
            )),
            'search-accounts' => new BooleanField(array(
                'label' => 'Search Accounts',
                'default' => false,
                'configuration' => array(
                    'desc' => 'SugarCRM Accounts will be searched when creating users',
                ),
            )),
            'sync' => new SectionBreakField(array(
                'label' => 'Synchronization Modes',
                'hint' => 'Synchronization modes for Accounts and Contacts can be enabled independently',
            )),
            'sync-contacts' => new BooleanField(array(
                'label' => 'Syncronize Contacts',
                'default' => true,
                'configuration' => array(
                    'desc' => 'Enable creation of new Users into SugarCRM Contacts',
                ),
            )),
            'sync-accounts' => new BooleanField(array(
                'label' => 'Syncronize Accounts',
                'default' => true,
                'configuration' => array(
                    'desc' => 'Enable creation of new Organizations into SugarCRM Accounts',
                ),
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

