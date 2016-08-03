<?php
$env = getenv('APPLICATION_ENV');
$genProxies = false;
$cache = 'apc';
if($env == 'development'){
	$genProxies = true;
	$cache = 'array';
}
return array(
		'doctrine' => array(
				'connection' => array(
						// default connection name
						'orm_default' => array(
								'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
								'params' => array(
										'host'     => 'localhost',
										'port'     => '3306',
										'user'     => '',
										'password' => '',
										'dbname'   => '',
										'driverOptions' => array(
												PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
										)
								)
						),
						'doctrine_type_mappings' => array(
                    		'enum' => 'string',
                ),
				),
				'configuration' => array(
						'orm_default' => array(
								'metadata_cache'    => $cache,
								'query_cache'       => $cache,
								'result_cache'      => $cache,
								'hydration_cache'   => $cache,
								'driver'            => 'orm_default',
								'generate_proxies'  => $genProxies,
								'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
								'proxy_namespace'   => 'DoctrineORMModule\Proxy',
								'filters'           => array()
						)
				),
		)
);
