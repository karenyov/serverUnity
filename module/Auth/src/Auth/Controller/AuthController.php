<?php

namespace Auth\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Storage\Entity\User;
use Zend\Session\Container;
use Storage\Service\UserService;
use Email\Controller\EmailController;
use Auth\Form\ChangePasswordForm;
use Auth\Form\ChangePasswordFilter;
use Zend\Validator\EmailAddress;
use Zend\View\HelperPluginManager;
use Zend\Http\Client;
use Zend\Http\Request;
use Main\Controller\MainController;

class AuthController extends MainController {
	public function indexAction() {
		return array();
	}
	public function loginAction() {
	    try {
    		$request = $this->getRequest();
    		$response = $this->getResponse();
    		$basePath=$request->getBasePath();
    		if ($request->isPost ()) {
    			$data = $request->getPost ();
    			$validatorEmail = new EmailAddress();
    			if (!($validatorEmail->isValid($data ['email']))) {
    				foreach ($validatorEmail->getMessages() as $messageId => $message) {
    					//Retorno JSON
    					$response->setContent ( \Zend\Json\Json::encode ( array ('login' => false, 'msg' => 'Ocorreu um erro ao realizar login.') ) );
    					return $response; 
    				}
    			}
    			$sm = $this->getServiceLocator();
    			$authAdapter = $sm->get ( 'Auth\Auth\Adapter' );
    			$userService = $this->getServiceLocator ()->get ( 'Storage\Service\UserService' );
    			$authAdapter = $this->getServiceLocator ()->get ( 'Auth\Auth\Adapter' );
    			
    			$authenticationService = new AuthenticationService ();
    			$authenticationService->setStorage (new SessionStorage ());
    			
    			$authAdapter->setUsername ( $data ['email'] )->setPassword ( $data ['password'] );
    			
    			$result = $authenticationService->authenticate ( $authAdapter );
    			$user = $result->getIdentity ()['user'];
    			
    			if ($result->isValid ()) {
    				$this->session = new Container ( 'App_Auth' );
    				$this->session->user = $result->getIdentity ()['user'];
    				//$userService->updateLastAccess($this->session->user->useId);
    				$userService->clearResetToken($this->session->user->useId);
    				//retorno JSON
    				$scene = $user->scene;
    				settype($scene, 'string');
    				$mysqlDate = $user->time;
    				$time = $mysqlDate->format('i').$mysqlDate->format('s');
    				
    				$response->setContent ( \Zend\Json\Json::encode ( array ('login' => true, 'msg' => 'Login realizado com sucesso!', 'scene' => $scene, 'time' => $time) ) );
    				return $response;
    				
    			} else{
					$response->setContent ( \Zend\Json\Json::encode ( array ('login' => false, 'msg' => 'Login ou senha inválidos!') ) );
    				return $response;    				
    				
    			}
    		}
    		
		}catch (\Exception $e){
			$response->setContent ( \Zend\Json\Json::encode ( array ('login' => false, 'msg' => $e->getMessage()) ) );
    		return $response; 
		}
	}
	
	public function verifyUserSessionAction(){
		$request = $this->getRequest();
		$response = $this->getResponse();
		$this->session = new Container('App_Auth');					
		$user = $this->session->user;
		if ($user && get_class($user) == 'Storage\Entity\User'){
			$response->setContent ( \Zend\Json\Json::encode ( array ('session' => true, 'userName' => $user->name) ) );
    		return $response;			
		}else{
			$response->setContent ( \Zend\Json\Json::encode ( array ('session' => false) ) );
    		return $response;
		
		}
	}
	
	public function loginServerAction() {
		try {
			$request = $this->getRequest();
			$response = $this->getResponse();
			$basePath=$request->getBasePath();
			if ($request->isPost ()) {
				$data = $request->getPost ();
				$validatorEmail = new EmailAddress();
				if (!($validatorEmail->isValid($data ['email']))) {
					foreach ($validatorEmail->getMessages() as $messageId => $message) {
						//Retorno JSON
						$response->setContent ( \Zend\Json\Json::encode ( array ('login' => false, 'msg' => 'Ocorreu um erro ao realizar login.') ) );
						return $response;
					}
				}
				$sm = $this->getServiceLocator();
				$authAdapter = $sm->get ( 'Auth\Auth\Adapter' );
				$userService = $this->getServiceLocator ()->get ( 'Storage\Service\UserService' );
				$roleService = $this->getServiceLocator ()->get ( 'Storage\Service\RoleService' );
				$rol = $roleService->getById(1);
				$rol2 = $roleService->getById(2);
				$authAdapter = $this->getServiceLocator ()->get ( 'Auth\Auth\Adapter' );
				$authenticationService = new AuthenticationService ();
				$authenticationService->setStorage (new SessionStorage ());
				 
				$authAdapter->setUsername ( $data ['email'] )->setPassword ( $data ['password'] );
				 
				$result = $authenticationService->authenticate ( $authAdapter );
				$user = $result->getIdentity ()['user'];
				 
				if ($result->isValid ()) {
					$userSession = $userService->getByEMail($user->email);
						
					$this->session = new Container ( 'App_Auth' );
					$this->session->user = $userSession;
					//$userService->updateLastAccess($this->session->user->useId);
					$userService->clearResetToken($this->session->user->useId);
					//retorno JSON
					$scene = $user->scene;
					settype($scene, 'string');
					$mysqlDate = $user->time;
					$time = $mysqlDate->format('i').$mysqlDate->format('s');
	
					return $this->showMessage('Login realizado com sucesso!', 'home-success', '/');
	
				} else{
					return $this->showMessage('Login ou senha inválidos!', 'home-error', '/');
	
				}
			}
	
		}catch (\Exception $e){
			return $this->showMessage('Ocorreu um erro ao realizar o login', 'home-error', '/');
		}
		
	}
	
	public function logoutAction() {
		try {
			$response = $this->getResponse();
			$this->session->getManager ()->getStorage ()->clear ();
			$response->setContent ( \Zend\Json\Json::encode ( array ('logout' => true, 'msg' => 'Logout realizado com sucesso!') ) );
			return $response;
		}catch (\Exception $e){
			$response->setContent ( \Zend\Json\Json::encode ( array ('logout' => false, 'msg' => $e) ) );
			return $response;
		}
	}
	
	public function logoutServerAction() {
		try {
			$response = $this->getResponse();
			$this->session->getManager ()->getStorage ()->clear ();
			return $this->showMessage('Logout realizado com sucesso!', 'home-sucess', '/');
			return $response;
		}catch (\Exception $e){
			return $this->showMessage('Ocorreu um erro ao realizar o logout', 'home-error', '/');
				
		}
	}
}
