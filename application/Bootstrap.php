<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_AutoLoader(
            array('namespace'=>'','basePath'=>APPLICATION_PATH)
        );
        return $moduleLoader;
    }

    function _initViewHelpers()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('CSCGiripuram');
        Zend_Session::start();
        $_SESSION["page_url"] = "http://localhost/unicent/public/";
        $dbconfig = $this->getOption('dbconfig');
        Zend_Registry::set('dbconfig', $dbconfig);
    }

}
