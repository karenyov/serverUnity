<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Storage for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Storage;

use Storage\Service\AccessService;
use Storage\Service\EmailService;
use Storage\Service\PrivilegeService;
use Storage\Service\QuestionService;
use Storage\Service\ReportService;
use Storage\Service\ResourcesService;
use Storage\Service\RolePrivilegeService;
use Storage\Service\RoleService;
use Storage\Service\UserService;
use Storage\Service\CompetenceService;
use Storage\Service\AnswerService;
use Storage\Service\InstitutionService;
use Storage\Service\TeacherService;
use Storage\Service\CompetenceScoreService;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;


class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    public function getServiceConfig() {
    
    	return array(
    			'factories' => array(
    					
    					'Storage\Service\UserService' => function($service) {
    						return new UserService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\AccessService' => function($service) {
    						return new AccessService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\RoleService' => function($service) {
    						return new RoleService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\ResourcesService' => function($service) {
    					   return new ResourcesService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\PrivilegeService' => function($service) {
    					   return new PrivilegeService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\RolePrivilegeService' => function($service) {
    					   return new RolePrivilegeService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\ReportService' => function($service) {
    					return new ReportService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\EmailService' => function($service) {
    					return new EmailService();
    					},
    					'Storage\Service\QuestionService' => function($service) {
    					return new QuestionService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\CompetenceService' => function($service) {
    					return new CompetenceService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\AnswerService' => function($service) {
    					return new AnswerService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\InstitutionService' => function($service) {
    					return new InstitutionService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\TeacherService' => function($service) {
    					return new TeacherService($service->get('Doctrine\ORM\EntityManager'));
    					},
    					'Storage\Service\CompetenceScoreService' => function($service) {
    					return new CompetenceScoreService($service->get('Doctrine\ORM\EntityManager'));
    					},
    			),
    	);
    }
}