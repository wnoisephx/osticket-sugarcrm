<?php

require_once (INCLUDE_DIR . 'class.plugin.php');
require_once (INCLUDE_DIR . 'class.signal.php');
require_once (INCLUDE_DIR . 'class.app.php');
require_once (INCLUDE_DIR . 'class.dispatcher.php');
require_once (INCLUDE_DIR . 'class.dynamic_forms.php');
require_once (INCLUDE_DIR . 'class.osticket.php');

class SugarCRMBackend {
    static $config;
    static $__config;
    private $CRMap;

    static function setConfig($config) {
        $CRMapi = new SugarRestAPI;
        $config = $config->getInfo();
        $__config = $config;
        $CRMapi->SetLoginInfo($config['$url'],$config['$username'],$config['$password'],'osticket');
$CRMapi->PrintPrivateState2();
    }

    function getConfig() {
        return $__config;
    }
}

class SugarCRMClientAuthentication extends ExternalUserAuthenticationBackend {
    static $name = "SugarCRM";
    static $id = "sugar.client";

    function __construct($config) {
        $this->config = $config;
    	$this->_sugarcrm = new SugarCRMBackend($config);
    }
}
 
require_once ('config.php');
//require_once ('sugarcrmapi.php');

class SugarCRMPlugin extends Plugin {
    var $config_class = 'SugarCRMPluginConfig';

    function bootstrap() {
        require_once ('sugarapi.php');
        UserAuthenticationBackend::register(new SugarCRMClientAuthentication($this->getConfig()));
    }
}

require_once(INCLUDE_DIR.'UniversalClassLoader.php');
use Symfony\Component\ClassLoader\UniversalClassLoader_osTicket;
$loader = new UniversalClassLoader_osTicket();
$loader->registerNamespaceFallbacks(array(
    dirname(__file__).'/lib'));
$loader->register();
?>
