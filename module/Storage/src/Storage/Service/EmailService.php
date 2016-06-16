<?php

namespace Storage\Service;

use \Email\Exception\MissingConfigException;
use \Zend\Mail\Transport\Exception\RuntimeException;
use Email\Helper\ConfigHelper;
use Storage\Entity\Email;
use Zend\Mail;
use Zend\Mail\Storage as Storage;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as Mime;

class EmailService {
	
	private $IMAP=null;
	
	public function send($email, $report, $dirMail) {
		
		// ConfiguraÃ§Ãµes de envio de email, ver o arquivo email.config.php
		$emailConfig = $this->getEmailConfigurations ();
		$USER_MAIL = $emailConfig ['send_account'] ['mail_user'];
		$PASS_MAIL = $emailConfig ['send_account'] ['mail_password'];
		$HOST = $emailConfig ['send_account'] ['host'];
		$PORT = $emailConfig ['send_account'] ['port'];
		
		if (! isset ( $email ) || empty ( $email )) {
			throw new \Email\Exception\MissingConfigException ( "Missing address to \"To recipients\"." );
		}
		
		if (
				! isset ( $USER_MAIL ) || empty ( $USER_MAIL ) ||
				! isset ( $PASS_MAIL ) || empty ( $PASS_MAIL ) ||
				! isset ( $HOST ) || empty ( $HOST ) ||
				! isset ( $PORT ) || empty ( $PORT )
			) {
			throw new \Email\Exception\MissingConfigException ( "Missing account configuration. Please, verify your email.config.php file." );
		}
		
		$options = new Mail\Transport\SmtpOptions ( array (
				'name' => 'localhost',
				'host' => $HOST,
				'port' => $PORT,
				'connection_class' => 'login',
				'connection_config' => array (
						'username' => $USER_MAIL,
						'password' => $PASS_MAIL,
						'ssl' => 'tls' 
				) 
		) );
		
		$mail = new Mail\Message ();
		
		$html = new MimePart ();
		$html->type = "text/html";
		$html->charset = "utf-8";
		
		$content = file_get_contents($dirMail.	'/'.$report->reportId.'.pdf'); // e.g. ("attachment/abc.pdf")
		$attachment = new MimePart($content);
		$attachment->type = 'application/pdf';
		$attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
		$attachment->encoding = Mime::ENCODING_BASE64;
		$attachment->filename = 'filename.pdf'; // name of file
		
		$body = new MimeMessage ();
		$body->setParts ( array (
				$html, $attachment
		) );
		
		//$env = getenv('APPLICATION_ENV');
		//$emailsTo = ( ($env == 'development') ? ($emailConfig['webmaster_account']['mail_user']) : ($emailsTo) );
		
		$mail->setBody ( $body ); // will generate our code html from template.phtml
		$mail->setFrom ( $USER_MAIL, 'Administrador do Sistema' );
		$mail->addTo ( $email );
		$mail->setSubject ( 'Relatório de competências' );
				
		$transport = new Mail\Transport\Smtp ( $options );
		$returnSend=false;
		try{
			$returnSend = $transport->send ( $mail );
		}catch (\Zend\Mail\Transport\Exception\RuntimeException $e) {
			$returnSend = $e->getMessage();
			return false;
		}
		
		return $returnSend;
	}
	
	public function getEmailConfigurations() {
		$config = new ConfigHelper ();
		$emailConfig = $config->getConfig ();
		return $emailConfig;
	}

}
