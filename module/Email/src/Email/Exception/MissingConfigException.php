<?php

namespace Email\Exception;

class MissingConfigException extends \Exception {
	
	/**
	 * @param message[optional]
	 */
	public function __construct ($message, $code) {
		$this->message = $message;
		$this->code = $code;
	}
	
}