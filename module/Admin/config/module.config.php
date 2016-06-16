<?php

namespace Admin;

return array(
    'controllers' => array(
        'invokables' => array(
        	'Admin\Controller\User' => 'Admin\Controller\UserController',
        	'Admin\Controller\Question' => 'Admin\Controller\QuestionController',	
        	'Admin\Controller\Competence' => 'Admin\Controller\CompetenceController',
        	'Admin\Controller\Answer' => 'Admin\Controller\AnswerController',	
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
        	'Admin\Controller\Institution' => 'Admin\Controller\InstitutionController',
        	'Admin\Controller\Report' => 'Admin\Controller\ReportController',
        	'Admin\Controller\Teacher' => 'Admin\Controller\TeacherController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'user' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/user[/:action]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'User',
                        //'action'        => 'index',
                    ),
                ),
            ),
        						'admin' => array(
        								'type'    => 'Segment',
        								'options' => array(
        										'route'    => '/admin[/:action]',
        										'defaults' => array(
        												'__NAMESPACE__' => 'Admin\Controller',
        												'controller'    => 'Admin',
        												//'action'        => 'index',
        										),
        								),
        ),
        		'question' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/question[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Question',
        								//'action'        => 'index',
        						),
        				),
        		),
        		'competence' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/competence[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Competence',
        								//'action'        => 'index',
        						),
        				),
        		),
        		'answer' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/answer[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Answer',
        								//'action'        => 'index',
        						),
        				),
        		),
        		'answer' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/answer[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Answer',
        								//'action'        => 'index',
        						),
        				),
        		),
        		'institution' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/institution[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Institution',
        								//'action'        => 'index',
        						),
        				),
        		),
        		
        		'teacher' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/teacher[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Teacher',
        								//'action'        => 'index',
        						),
        				),
        		),
        		'report' => array(
        				'type'    => 'Segment',
        				'options' => array(
        						'route'    => '/report[/:action]',
        						'defaults' => array(
        								'__NAMESPACE__' => 'Admin\Controller',
        								'controller'    => 'Report',
        								//'action'        => 'index',
        						),
        				),
        		),
    ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view',
        ),
    )
);
