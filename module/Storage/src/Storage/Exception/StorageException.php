<?php

namespace Storage\Exception;

class StorageException extends \Exception {

	/**
	 * @param message[optional]
	 */
	public function __construct ($message, $code) {
		$this->message = $message;
		$this->code = $code;
	}

}