<?php
namespace Email\Helper;

use \Email\Exception\MissingConfigException;

class ConfigHelper {
	
	public function getConfig()
	{
		$filename = __DIR__ . '/../../../config/email.config.php';
		$dir = __DIR__ . '/../../../config/';
		if(!file_exists($filename)) {
			if (is_dir($dir)) {
				if (!is_writable($dir) && !@chmod($dir, 0666)) {
					throw new MissingConfigException("Error on create email.config.php. Please, verify write permissions on directory (".$dir.").");
				}
			}else{
				throw new MissingConfigException("Error on create email.config.php. Your directory (".$dir."), not found.");
			}
			$this->createConfigFile($filename);
		}
	    return include $filename;
	}
	
	private function createConfigFile($filename) {
		
		$configTemplate =
"<?php

namespace Email;

return array (
		'send_account' => array(
				'host' => 'smtp.funcate.com.br',
				'mail_user' => '',
				'mail_password' => '',
				'port' => 587
		),
		'webmaster_account' => array(
			'mail_user' => ''
		),
);";
		file_put_contents($filename, $configTemplate);
		@chmod($filename, 0666);
	}
}