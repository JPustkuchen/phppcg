<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload(){
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'	=>	APPLICATION_PATH
		));
		return $moduleLoader;
	}
	
	protected function _initCache(){
		$options = $this->getOptions();		
		if(!empty($options['cache']['enabled'])){	
			//CACHE
			$frontendOptions = array(
			   'lifetime' => $options['cache']['lifetime'],
			   'debug_header' => $options['cache']['debug_header'], // Debug-Ausgabe aktiv?
			   'regexps' => array(
			       // cache den gesamten IndexController
			       '^/$' => array('cache' => true),
			   )
			);
			
			$backendOptions = array(
			    'cache_dir' => $options['cache']['cache_dir']
			);
			
			// erhalte ein Zend_Cache_Frontend_Page Objekt
			$cache = Zend_Cache::factory('Page',
			                             'File',
			                             $frontendOptions,
			                             $backendOptions);
			$cache->start();
			
			return $cache;
		}
	}
	
	protected function _initLayout(){
		// Initialise Zend_Layout's MVC helpers
		$layoutConfig = new Zend_Config_Ini(APPLICATION_PATH.'/configs/layout.ini', 'layout_default');
		$layout = Zend_Layout::startMvc($layoutConfig);

		Zend_Registry::set('Zend_Layout', $layout); 
		Zend_Registry::set('Zend_View', $layout->getView()); 
		     	
		// Inhalte initialisieren
		//TODO - Evtl. besser in Modul auslagern
		$contentsConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/contents.xml', 'site');
		Zend_Registry::set('contentsConfig', $contentsConfig); 
		/* ------------------ SEITENSPEZIFISCHE INHALTE AB HIER ------------------------- */
		
     	/* ------------------ SEITENSPEZIFISCHE INHALTE BIS HIER ------------------------- */
     	
     	return $layout;
	}
	
	/**
	* @since 221109
	* @author JP
	*/
	protected function _initViewHelpers(){
		$layout=Zend_Registry::get('Zend_Layout');
		$view=$layout->getView();
		
		//Werte aus Konfiguration laden
		$siteConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/site.xml', 'site');
		Zend_Registry::set('siteConfig', $siteConfig);
		$view->assign('siteConfig', $siteConfig->toArray());
		
		
		//JQuery Helper
		if(!empty($siteConfig->plugins->jquery->enabled)){
			$view->addHelperPath("../library/ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
			if($view->jQuery()->isEnabled()){
				$view->jQuery()->setLocalPath($siteConfig->scripts->jquery);
				$view->jQuery()->setUiLocalPath($siteConfig->scripts->jqueryUi);
			}
		}
						
		//Titel
		$view->headTitle($siteConfig->sitename)
			 ->setSeparator($siteConfig->titleSeperator);
		
		//DocType
		$view->doctype($siteConfig->meta->doctype);
		
		//MetaDaten
		$view->headMeta()->appendHttpEquiv('Content-Type', $siteConfig->meta->contentType);  
		$view->headMeta()->appendHttpEquiv('language', $siteConfig->meta->language);  
		$view->headMeta()->appendHttpEquiv('content-language', $siteConfig->meta->contentLanguage);
				
		if(!empty($siteConfig->meta->description)){		
			$view->headMeta()->appendHttpEquiv('description', $siteConfig->meta->description);
		}
		
		if(!empty($siteConfig->meta->keywords)){
			$view->headMeta()->appendHttpEquiv('keywords', $siteConfig->meta->keywords);
		}
		
		//FavIcon
		if(!empty($siteConfig->meta->favicon)){
			$view->headLink()->headLink(array('rel' => 'shortcut icon',
			                                  'href' => $siteConfig->meta->favicon,
			                                  'type' => 'image/x-icon'),
			                                  'PREPEND');
        }
		
		//Styles	
		if(!empty($siteConfig->stylesheets)){	
			foreach($siteConfig->stylesheets as $stylesheet){
				//TODO - Hier besser mit localStylesheets und baseUrl arbeiten, geht aber evtl. nicht weil baseUrl noch nicht gesetzt?
				$view->headLink()->appendStylesheet($stylesheet);
			}
		}
		
		//Scripts		
		if(!empty($siteConfig->scripts)){
			foreach($siteConfig->scripts as $script){
				//TODO - Hier besser mit localStylesheets und baseUrl arbeiten, geht aber evtl. nicht weil baseUrl noch nicht gesetzt?
				$view->headScript()->appendFile($script);
			}	
		}
		
		//Inlinescripts	
		if(!empty($siteConfig->inlineScripts)){
			foreach($siteConfig->inlineScripts as $inlineScript){
				$view->headScript()->appendScript($inlineScript);
			}
		}
		
		/* ------------------ SEITENSPEZIFISCHE INHALTE AB HIER ------------------------- */

     	/* ------------------ SEITENSPEZIFISCHE INHALTE BIS HIER ------------------------- */
     	
     	return $view;
	}
	/*	
	protected function _initNavigation(){
		$navigationConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml', 'nav');
		$navigation = new Zend_Navigation($navigationConfig);
		
		$footerNavigationConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml', 'footernav');
		$footerNavigation = new Zend_Navigation($footerNavigationConfig);
				
		$topNav = Zend_Registry::set('mainNavigation', $navigation);
		$sideNav = Zend_Registry::set('footerNavigation', $footerNavigation);
				
		Zend_Registry::get(Zend_View)->navigation($navigation);
		
		/* ------------------ SEITENSPEZIFISCHE INHALTE AB HIER ------------------------- */

     	/* ------------------ SEITENSPEZIFISCHE INHALTE BIS HIER ------------------------- */
     	/*return $navigation;
	}*/
}