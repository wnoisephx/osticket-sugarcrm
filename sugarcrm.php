<?php
require_once (INCLUDE_DIR . 'class.plugin.php');
require_once (INCLUDE_DIR . 'class.signal.php');
require_once (INCLUDE_DIR . 'class.app.php');
require_once (INCLUDE_DIR . 'class.dispatcher.php');
require_once (INCLUDE_DIR . 'class.dynamic_forms.php');
require_once (INCLUDE_DIR . 'class.osticket.php');

class SugarCRMBackend {
    static $__config;
    private $CRMapi;

function LogToFile($Message) {
        error_log(date("Y-m-d H:i:s")." - ",3,"./sugarcrm.txt");
        error_log($Message,3,"./sugarcrm.txt");
}

    function __construct($config) {
        $this->__config = $config;
        $this->setConfig($config);
        $this->CRMapi->Login();
    }

    function setConfig($config) {
        $this->__config = $config;
        $this->CRMapi = new SugarRestAPI;
        $cfg = $config->getInfo();
        $this->CRMapi->SetLoginInfo($cfg['url'],$cfg['username'],$cfg['password'],'osticket');
    }

    function getConfig() {
        return $__config;
    }

    // function to search sugar modules
    function searchSugar($query) {
// we should have a check box in the config for if we want to search Contacts, Accounts, or Both (or maybe other sugar modules?)
       return($this->CRMapi->search_by_module($query,array('Contacts','Accounts'),array('id')));
    }

    // get a record by id
    function getRecord($module,$id,$fields) {
         return($this->CRMapi->get_entry($module,$id,$fields));
    }
}

class SugarCRMClientAuthentication extends UserAuthenticationBackend implements AuthDirectorySearch {
    static $name = "SugarCRM";
    static $id = "sugar";
    private static $sugarcrm;

    function __construct($config) {
        $this->config = $config;
    	$this->sugarcrm = new SugarCRMBackend($config);
    }

function LogToFile($Message) {
        error_log(date("Y-m-d H:i:s")." - ",3,"./sugarcrm.txt");
        error_log($Message,3,"./sugarcrm.txt");
}

    // required function - lookup
    function lookup($id) {
        // these are the feilds to return grom getRecord
        $contact_fields = array(
            'name',
            'first_name',
            'last_name',
            'email1',
            'phone_work',
            'phone_mobile',
        );

        $account_fields = array(
            'name',
            'email1',
            'phone_office',
            'phone_alternate',
        );

        $split=explode(':',$id);
        if($split[0]=="Contact") {
	        $user=$this->sugarcrm->getRecord('Contacts',$split[1],$contact_fields);
                $r=array(
                    'name'=>$user->entry_list[0]->name_value_list->name->value,
                    'first'=>$user->entry_list[0]->name_value_list->first_name->value,
                    'last'=>$user->entry_list[0]->name_value_list->last_name->value,
                    'email'=>$user->entry_list[0]->name_value_list->email1->value,
                    'phone'=>$user->entry_list[0]->name_value_list->phone_work->value,
                    'mobile'=>$user->entry_list[0]->name_value_list->phone_mobile->value,
                    'id'=>static::$id.':Contact:'.$id,
                );
        } else if($split[0]=="Account") {
// accounts should be created as osTicket Organizations not users however this will work for now
	        $user=$this->sugarcrm->getRecord('Accounts',$split[1],$account_fields);
                $r=array(
                    'name'=>$user->entry_list[0]->name_value_list->name->value,
                    'first'=>$user->entry_list[0]->name_value_list->name->value,
                    'last'=>'',
                    'email'=>$user->entry_list[0]->name_value_list->email1->value,
                    'phone'=>$user->entry_list[0]->name_value_list->phone_office->value,
                    'mobile'=>$user->entry_list[0]->name_value_list->phone_alternate->value,
                    'id'=>static::$id.':Account:'.$id,
                );
        } else return;

$this->LogToFile("r = ".print_r($r,TRUE)."\n");
        return($r);
    }

    // required function - search 
    function search($query) {
        $r=array();

        // these are the feilds to return from getRecord
        $contact_fields = array(
            'name',
            'first_name',
            'last_name',
            'email1',
            'phone_work',
            'phone_mobile',
        );

        $account_fields = array(
            'name',
            'email1',
            'phone_office',
            'phone_alternate',
        );

        $crmresult=$this->sugarcrm->searchSugar($query);

        // covert the results
        $idx=0;
        foreach($crmresult->entry_list as $ent) {
            foreach($ent->records as $user) {
                if($ent->name=="Contacts") {
	            $info=$this->sugarcrm->getRecord('Contacts',$user->id->value,$contact_fields);

                    // build an array of user objects
                    $r[$idx++]=array(
                        'name'=>$info->entry_list[0]->name_value_list->name->value,
                        'first'=>$info->entry_list[0]->name_value_list->first_name->value,
                        'last'=>$info->entry_list[0]->name_value_list->last_name->value,
                        'email'=>$info->entry_list[0]->name_value_list->email1->value,
                        'phone'=>$info->entry_list[0]->name_value_list->phone_work->value,
                        'mobile'=>$info->entry_list[0]->name_value_list->phone_mobile->value,
                        'id'=>static::$id.':Contact:'.$user->id->value,
                    );
                } else if($ent->name=="Accounts") {
	            $info=$this->sugarcrm->getRecord('Accounts',$user->id->value,$account_fields);

                    // build an array of user objects
                    $r[$idx++]=array(
                        'name'=>$info->entry_list[0]->name_value_list->name->value,
                        'first'=>$info->entry_list[0]->name_value_list->name->value,
                        'last'=>'',
                        'email'=>$info->entry_list[0]->name_value_list->email1->value,
                        'phone'=>$info->entry_list[0]->name_value_list->phone_office->value,
                        'mobile'=>$info->entry_list[0]->name_value_list->phone_alternate->value,
                        'id'=>static::$id.':Account:'.$user->id->value,
                    );
                }
            }
        }  

        // return the results
        return($r);
    }
}
 
require_once ('config.php');
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
