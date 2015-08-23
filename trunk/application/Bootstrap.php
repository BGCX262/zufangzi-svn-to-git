<?php

class Bootstrap {
	
	public function __construct($configSection) {
		$rootDir = dirname ( dirname ( __FILE__ ) );
		define ( 'APPLICATION_PATH', realpath ( dirname ( __FILE__ ) . '/../application' ) );
		define ( 'APPLICATION_ENV', (getenv ( 'APPLICATION_ENV' ) ? getenv ( 'APPLICATION_ENV' ) : 'production') );

		set_include_path ( implode ( PATH_SEPARATOR, array (realpath ( APPLICATION_PATH . '/../library' ), realpath ( APPLICATION_PATH . '/../library/Swift/lib/classes' ), realpath ( APPLICATION_PATH . '/../data' ), get_include_path () ) ) . ":" . realpath ( APPLICATION_PATH . '/models' ) . ":" . realpath ( APPLICATION_PATH . '/forms' ) );

		
		include_once ('Zend/Loader/Autoloader.php');
		$loader = Zend_Loader_Autoloader::getInstance ();
		$loader->setFallbackAutoloader ( true );
		$loader->suppressNotFoundWarnings ( false );
		// start using session
		Zend_Session::start();
		
		// Initialise Zend_Layout's MVC helpers
		Zend_Layout::startMvc ( array ('layoutPath' => APPLICATION_PATH . '/layouts/scripts' ) );
		
		// load configuration
		Zend_Registry::set ( 'configSection', $configSection );
		$config = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', $configSection );
		Zend_Registry::set ( 'config', $config );
		
		Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH .'/controllers/helpers');

		$view = new Zend_View;
		$view->addHelperPath(APPLICATION_PATH .'/views/helpers');
		
		date_default_timezone_set ( $config->date_default_timezone );
		
		// set up database
		//$db = Zend_Db::factory ( $config->resources->db->adapter, $config->resources->db->params->toArray () );
		//Zend_Db_Table::setDefaultAdapter ( $db );
		//Zend_Registry::set ( 'db', $db );
		//$db->query ( "SET NAMES utf8" );

		$options = array(
    			Zend_Db::ALLOW_SERIALIZATION => false
		);
 
		$params = array(
		    'host'           => $config->resources->db->params->hostname,
		    'username'       => $config->resources->db->params->username,
		    'password'       => $config->resources->db->params->password,
		    'dbname'         => $config->resources->db->params->dbname,
		    'charset'        => $config->resources->db->params->charset,
		    'options'        => $options
		);
 
		$db = Zend_Db::factory($config->resources->db->adapter, $params);
		Zend_Db_Table::setDefaultAdapter ( $db );
		Zend_Registry::set ( 'db', $db );
		$db->query ( "SET NAMES utf8" );
		
		$languageFile = '../application/configs/cn_form.php';
		Zend_Registry::set ( 'languageFile', $languageFile );
	}
	
	public function configureFrontController() {
		// setup controller
		$frontController = Zend_Controller_Front::getInstance ();
		$frontController->throwExceptions ( false ); //->registerPlugin(new Zend_Controller_Plugin_ErrorHandler());
		$frontController->setControllerDirectory ( '../application/controllers' );
		
		// Define route rules
		$router = $frontController->getRouter ();
		$router->addRoute (
			'list', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/list/:page', 
				array ('city' => 'stockholm',
						'page' => 1, 
						'controller' => 'bulletin', 
						'action' => 'list' ) 
			)
		);
		$router->addRoute (
			'view', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/view/:id', 
				array ('id' => -1,
						'controller' => 'bulletin', 
						'action' => 'view',
						'city' => 'stockholm' ) 
			)
		);
		
		$router->addRoute (
			'createadvertisingagency', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/createadvertisingagency/:next', 
				array ('city' => 'stockholm',
						'next' => '',
						'controller' => 'bulletin', 
						'action' => 'createadvertisingagency' ) 
			)
		);
		$router->addRoute (
			'create', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/create', 
				array ('city' => 'stockholm', 
						'controller' => 'bulletin', 
						'action' => 'create' ) 
			)
		);
		$router->addRoute (
			'test', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/test', 
				array ('city' => 'stockholm', 
						'controller' => 'bulletin', 
						'action' => 'test' ) 
			)
		);
		$router->addRoute (
			'update', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/update', 
				array ('city' => 'stockholm', 
						'controller' => 'bulletin', 
						'action' => 'update' ) 
			)
		);
		$router->addRoute (
			'admin', 
			new Zend_Controller_Router_Route ( 
				':city/bulletin/admin/:id', 
				array ('id' => -1,
						'controller' => 'bulletin', 
						'action' => 'admin',
						'city' => 'stockholm' ) 
			)
		);
		$router->addRoute (
			'localhelp', 
			new Zend_Controller_Router_Route ( 
				':city/localhelp', 
				array ('city' => 'stockholm',
						'controller' => 'bulletin', 
						'action' => 'localhelp' ) 
			)
		);
		$router->addRoute (
			'index', 
			new Zend_Controller_Router_Route ( 
				'index', 
				array ('controller' => 'index', 
						'action' => 'index' ) 
			)
		);
		$router->addRoute (
			'help', 
			new Zend_Controller_Router_Route ( 
				'help', 
				array ('controller' => 'index', 
						'action' => 'help' ) 
			)
		);
		$router->addRoute (
			'about', 
			new Zend_Controller_Router_Route ( 
				'about', 
				array ('controller' => 'index', 
						'action' => 'about' ) 
			)
		);
		$router->addRoute (
			'contact', 
			new Zend_Controller_Router_Route ( 
				'contact', 
				array ('controller' => 'index', 
						'action' => 'contact' ) 
			)
		);
		$router->addRoute (
			'links', 
			new Zend_Controller_Router_Route ( 
				'links', 
				array ('controller' => 'index', 
						'action' => 'links' ) 
			)
		);
		$router->addRoute (
			'guestbook', 
			new Zend_Controller_Router_Route ( 
				'guestbook/:page', 
				array ('controller' => 'index', 
						'action' => 'guestbook',
						'page' => 1 ) 
			)
		);
		$router->addRoute (
			'newcity', 
			new Zend_Controller_Router_Route ( 
				'newcity', 
				array ('controller' => 'index', 
						'action' => 'newcity' ) 
			)
		);
		$router->addRoute (
			'home', 
			new Zend_Controller_Router_Route ( 
				'', 
				array ('controller' => 'index', 
						'action' => 'index' ) 
			)
		);
	}
	
	public function runApp() {
		$this->configureFrontController ();
		// run!
		$frontController = Zend_Controller_Front::getInstance ();
		$frontController->dispatch ();
	}
}

