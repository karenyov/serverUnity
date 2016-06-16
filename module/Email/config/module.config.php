<?php

namespace Email;

return array (
		'controllers' => array (
				'invokables' => array (
						'Email\Controller\Email' => 'Email\Controller\EmailController' 
				),
		),
		'router' => array (
				'routes' => array (
						'email' => array (
								'type' => 'Segment',
								'options' => array (
										// Change this to something specific to your module
										'route' => '/email[/:action]',
										'defaults' => array (
												// Change this value to reflect the namespace in which
												// the controllers for your module are found
												'__NAMESPACE__' => 'Email\Controller',
												'controller' => 'Email',
												'action' => 'index' 
										) ,
								) ,
								
						)
						 
				) 
		),
				
	
		
		'view_manager' => array(
	        'template_map' => array(
	            'layout/mail' => __DIR__ . '/../view/layout/layout.phtml',
	        	'layout/attachment' => __DIR__ . '/../view/layout/layoutAttachment.phtml',
	        ),
	        'template_path_stack' => array(
	            'Email'          => __DIR__ . '/../view'
	        ),
    	),

);