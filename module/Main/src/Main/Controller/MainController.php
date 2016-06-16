<?php
namespace Main\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Storage\Entity\User as AuthUser;
use Zend\Session\Container;
use Zend\Validator\EmailAddress;
use Main\Form\ResetPasswordForm;
use Main\Form\ResetPasswordFilter;
use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Validator\StringLength;
use Zend\I18n\Validator\Alnum;

class MainController extends AbstractActionController
{
	private $session;
	public function __construct() {
		$this->session = new Container ( 'App_Auth' );
	}
    public function indexAction()
    {
        return array();
    }


    public function resetPasswordAction()
    {
    	$request = $this->getRequest ();

    	if ($request->isPost ()) {
    		$url = $request->getHeader ( 'Referer' )->getUri ();
	    	$data = $request->getPost ();
	    	$email = $data ['email'];
	    	$validatorEmail = new EmailAddress();
	    	$validatorEmail->setOptions(array('domain' => FALSE));
	    	if (!($validatorEmail->isValid($email))) {
	    		// Email nÃ£o Ã© vÃ¡lido
	    		foreach ($validatorEmail->getMessages() as $messageId => $message) {
	    			$this->showMessage($message, 'error-email');
	    		}
	    		// Redirecionando usuÃ¡rio para mesma rota que estava
	    		return $this->redirect ()->toUrl ( '/reset/resetPassword' );
	    	}else{
	    		$serviceLocator = $this->getServiceLocator();
	    		$userService = $serviceLocator->get ('Storage\Service\UserService');
	    		$emailService = $serviceLocator->get ('Storage\Service\EmailService');
	    		
	    		$user = $userService->identifyUserByEmail($email);
	    		if($user){
	    			$timeStamp = time();
	    			$user->resetToken = $timeStamp;
	    			try {
	    				if($userService->updateUser ( $user )){
		    				$url_token_validation = str_replace("resetPassword", "newResetedPassword" ,$url) . "?email=" . $user->email ."&token=" . $timeStamp;
		    				try {
		    					$this->renderer = $this->getServiceLocator ()->get ( 'ViewRenderer' );
		    					$content = $this->renderer->render ('main/tpl/template',  array( "token" => $url_token_validation));
		    					$emailService->send('Portal projeto MSA - Definir nova senha', $user->email, $content);
		    				} catch (\Exception $e) {
		    					return $this->showMessage('Falhou ao enviar e-mail', 'error-email', '/reset/resetPassword');
		    				}
		    				return $this->showMessage("Um link de confirmaÃ§Ã£o para reset de senha foi enviado para o e-mail: " . $user->email, 'success-email', '/reset/resetPassword');
	    				}
	    				return $this->showMessage('Ocorreu um problema, contate o administrador do sistema.', 'error-email', '/reset/resetPassword');
	    			}catch(\Exception $e){
	    				return $this->showMessage('Ocorreu um problema, contate o administrador do sistema.', 'error-email', '/reset/resetPassword');
	    			}

	    		}else{
	    			return $this->showMessage('E-mail nÃ£o cadastrado no sistema.', 'error-email', '/reset/resetPassword');
	    		}
	    	}
    	}
    }

    public function newResetedPasswordAction()
    {
    	$request = $this->getRequest ();
    	$form = new ResetPasswordForm();
    	$filter = new ResetPasswordFilter();
    	if($request->isGet ()){
    		$requestGET = $request->getQuery();
    		$token = $requestGET['token'];
    		$form->get ( 'token' )->setAttribute ( 'value', $token );
    		$email = $requestGET['email'];;
    		$form->get ( 'email' )->setAttribute ( 'value', $email );
    		return array ("form" => $form);
    	}else if($request->isPost ()){
    		$url = $request->getHeader ( 'Referer' )->getUri ();
    		$serviceLocator = $this->getServiceLocator();
    		$userService = $serviceLocator->get ('Storage\Service\UserService');
    		$requestPOST = $request->getPost();
    		$data = $request->getPost ();
    		$form->setData ( $requestPOST );
    		
    		if($data['token'] && $data['email'])
    			$user = $userService->getByEmailAndToken($data['email'], $data['token']);
    		
    		if($user){
    			$diferenca =  $data['token'] - time();
    			$dias = (int)floor($diferenca / (60 * 60 * 24));
    			if($dias <= 3){
    				$form->setInputFilter ( $filter->getInputFilter() );
    				if ($form->isValid ()) {
    					$filter->exchangeArray ( $form->getData () ); // Pega valores do input do changePasswordForm filtra e popula objeto
    					$passNew1 = sha1($filter->passwordNew1);
    					$passNew2 = sha1($filter->passwordNew2);
    					if($passNew1 == $passNew2){
    						$user->password = $passNew1;
    						try {
    							if($userService->updateUser ( $user ))
    								return $this->showMessage('Nova senha criada com sucesso.', 'home-success', '/');
    							else
    								return $this->showMessage('NÃ£o foi possÃ­vel criar a nova senha.', 'error-email', '/reset/resetPassword');
    						}catch(\Exception $e){
    							return $this->showMessage('Ocorreu um problema, contate o administrador do sistema.', 'error-email', '/reset/resetPassword');
    						}
    					}else
    						return $this->showMessage('As senhas digitadas sÃ£o diferentes, favor, tente novamente.', 'error-email', '/reset/resetPassword');
    				}
    			}else
    				return $this->showMessage('Prazo para alteraÃ§Ã£o expirou, favor, solicitar um novo reset de senha.', 'error-email', '/reset/resetPassword');
    		}else
    			return $this->showMessage('NÃ£o foi possÃ­vel alterar sua senha, contate o administrador do sistema.', 'error-email', '/reset/resetPassword');
    	}
    }
	public function verifyUserSession()
    {
        $this->session = new Container('App_Auth');
        $user = $this->session->user;
        if ($user && get_class($user) == 'Storage\Entity\User')
            return true;
        return false;
    }
    public function showMessage($message, $namespace, $redirectTo = null) {
    	$request = $this->getRequest ();
    	$this->flashMessenger ()->setNamespace ( $namespace )->addMessage ( $message );
    	if($redirectTo){
    		$basePath = $request->getBasePath ();
    		if($redirectTo[0] != '/')
    			$redirectTo = '/'.$redirectTo;
    		$url = $basePath . $redirectTo;
    		return $this->redirect ()->toUrl ( $url );
    	}
    }
    public function getFormData()
    {
    	$request = $this->getRequest();
    	$formData = null;
    
    	// get data from browser form whith GET method
    	if ($request->isGet()) {
    		$formData = $request->getQuery();
    	}
    
    	// get data from browser form whith POST method
    	if ($request->isPost()) {
    		$formData = $request->getPost();
    	}
    	return $formData;
    }
    
    public function regexValidate($name, $regex){
    	$matches = array();
    	$validatedName = preg_match($regex, $name, $matches);
    	if($validatedName && $validatedName != 0){
    		if($matches[0] == $name)
    			return true;
    	}
    	return false;
    }
    
    public function userConfigurationsAction(){
    	$request = $this->getRequest();
    	$email = null;
    	$phone = null;
    	$name = null;
    	if ($this->verifyUserSession ()) {
    		$acl = $this->getServiceLocator ()->get ( 'Admin\Permissions\Acl' );
    		$userService = $this->getServiceLocator ()->get ( 'Storage\Service\UserService' );
    		$user = $userService->getById ( $this->session->user );
    		$requisitionTool = $user->requisitionTool;
    		$id = $user->useId;
    		$email = $user->email;
    		$phone = $user->phoneNumber;
    		$name = $user->name;
    		
    		$isAdmin = $this->session->user->rol->isAdmin;
    		if($isAdmin)
    			$this->session->active_menu = 2;
    		
    		/**
    		 * Permite informar o usuÃ¡rio que esta ferramenta Ã© de uso exclusivo dos coordenadores que avaliam as requisiÃ§Ãµes
    		 * 
    		 * requisitionTool==1 indica que Ã© um coordenador geral responsÃ¡vel por requisiÃ§Ãµes
    		 * rolId==4 indica que Ã© um coordenador de subprojeto responsÃ¡vel por avaliar uma requisiÃ§Ã£o do subprojeto
    		 */
    		$acceptRejectRequisitions = $acl->isAllowed($this->session->user->rol->name, "Ã�rea de trabalho", "Aceitar/recusar requisiÃ§Ãµes");
    		$allowRequisitionTool=(($requisitionTool==1 || $acceptRejectRequisitions)?(true):(false));
    		
    		if ($request->isGet()){
    			$data = $request->getQuery();
	    		if(isset($data["id"])){
	    			$this->session->active_menu = $data["id"]; //Ativa qual aba fica salva na sessÃ£o
	    		}
    		}
    		return array(
    				'active_menu' => $this->session->active_menu,
    				'id' => $id,
    				'email' => $email,
    				'phone' => $phone,
    				'name' => $name,
    				'requisitionTool' => $requisitionTool,
    				'allowRequisitionTool' => $allowRequisitionTool,
    				'isAdmin' => $isAdmin
    		);
    	}
    	return $this->showMessage('Sua sessÃ£o expirou, favor relogar', 'home-error', '/');
    }
    
    public function editUserAction(){
    	try {
    		$request = $this->getRequest();
    		$response = $this->getResponse();
    		$userService = $this->getServiceLocator ()->get ( 'Storage\Service\UserService' );
    		$user = $userService->getById ( $this->session->user );
    		if ($this->verifyUserSession ()) {
    			if ($request->isPost()){
    				$data = $request->getPost();
    				$acl = $this->getServiceLocator()->get('Admin\Permissions\Acl');
    				if ($acl->isAllowed($this->session->user->rol->name, "Ã�rea de trabalho", "Trocar senha")) { //Alterar nome do ACL, porÃ©m esse tambÃ©m funciona.
    					$name = trim ( $data['name'] );
    					$newEmail = trim ( $data['email'] );
    					$newEmailConfirm = trim ( $data['email2'] );
    					$phone = str_replace(" ", "", $data['phoneNumber']); 
    					$lengthValidator = new StringLength(array('min'=>1, 'max'=>45));
    					$phoneLengthValidator = new StringLength ( array (
    							'min' => 13,
    							'max' => 14
    					) );
    					$validator = new Alnum(true);
    					$validatorEmail = new EmailAddress();
    					$validatorEmail->setOptions(array('domain' => FALSE));
    					 
    					if(!$lengthValidator->isValid($name)){
    						return $this->showMessage('Campo de nome nÃ£o pode ser vazio', 'error-user-config', '/configurations');
    					}
    					if($lengthValidator->isValid($newEmail) && $lengthValidator->isValid($newEmailConfirm)){
    						if(!($newEmail === $newEmailConfirm)){
    							return $this->showMessage('Os campos de Email devem ser iguais', 'error-user-config', '/configurations');
    						}
    						if (!($validatorEmail->isValid($newEmail))) {
    							foreach ($validatorEmail->getMessages() as $messageId => $message) {
    								$this->showMessage($message, 'error-user-config');
    							}
    							// Redirecionando usuÃ¡rio para mesma rota que estava
    							$url = $request->getHeader ( 'Referer' )->getUri ();
    							return $this->redirect ()->toUrl ( $url );
    						}
    					}else{
    						return $this->showMessage('Campo de Email nÃ£o pode ser vazio', 'error-user-config', '/configurations');
    					}
    					if($phoneLengthValidator->isValid($phone)){
    						$chars = array("(", ")", "-");
    						$resultPhone = str_replace($chars, "", $phone);
    						if(!is_numeric($resultPhone)){
    							return $this->showMessage('Campo de Telefone deve conter apenas nÃºmeros e os caracteres "(DD)" e "-", exemplo: (00)1234-5678', 'error-user-config', '/configurations');
    						}
    					}else{
    						return $this->showMessage('Campo de Telefone deve possuir 13 caracteres no mÃ­nimo, exemplo: (00)1234-5678', 'error-user-config', '/configurations');
    					}
    				}
    				else {
    					return $this->showMessage('VocÃª nÃ£o possui permissÃµes para realizar essa operaÃ§Ã£o', 'home-error', '/');
    				}
    			}
    		} else {
    			return $this->showMessage('Sua sessÃ£o expirou, favor relogar', 'home-error', '/');
    		}
    		//Se chegou atÃ© aqui Ã© porque tudo ocorreu bem, logo, deve-se alterar os dados do usuÃ¡rio na sessÃ£o e persistir no banco.
    		
    		$oldName = $this->session->user->name;
    		$oldEmail = $this->session->user->email;
    		$oldPhone = $this->session->user->phoneNumber;
    		
    		$this->session->user->name = $name;
    		$this->session->user->email = $newEmail;
    		$this->session->user->phoneNumber = $phone;
    		
    		$userOk = $userService->updateUser($this->session->user);
    		if($userOk){
    			return $this->showMessage('UsuÃ¡rio alterado com sucesso!', 'success-user-config', '/configurations');
    		}else{
    			//retornando dados anteriores nos campos do usuÃ¡rio
    			$this->session->user->name = $oldName;
    			$this->session->user->email = $oldEmail;
    			$this->session->user->phoneNumber = $oldPhone;
    			return $this->showMessage('Ocorreu um erro ao alterar o usuÃ¡rio', 'error-user-config', '/configurations');
    		}
    	}catch (\Exception $e){
    		return $this->showMessage('NÃ£o foi possÃ­vel alterar a senha, contate o administrador do sistema', 'home-error', '/configurations');
    	}
    }
    
    public function changeRequisitionToolAction() {
    	try {
    		$response = $this->getResponse();
    		$request = $this->getRequest ();
    		$userService = $this->getServiceLocator ()->get ( 'Storage\Service\UserService' );
    		$acl = $this->getServiceLocator ()->get ( 'Admin\Permissions\Acl' );
    		$aclRequisitionTool = $acl->isAllowed ( $this->session->user->rol->name, "Ã�rea de trabalho", "Aceitar/recusar requisiÃ§Ãµes" );
    		$auth_user = $this->session->user;
    		$reqTool = $userService->getById($auth_user->useId);
    
    		if ($this->verifyUserSession ()){
    			if ($aclRequisitionTool){
    				if ($request->isGet()){
    					$data = $request->getQuery();
    					if (isset($data['value'])){
    						if($reqTool->requisitionTool != 0){
    							if(($data['value'] == 1) || ($data['value'] == 2)){
    								$userService->updateRequisitionTool($auth_user->useId, $data['value']);
    								$response->setContent(\Zend\Json\Json::encode(array('status' => true, 'msg' => 'Alterado com sucesso' )));
    								return $response;
    							}else{
    								$userService->updateRequisitionTool($auth_user->useId, 0);
    							}
    						}else{
    							$userService->updateRequisitionTool($auth_user->useId, 0);
    						}
    					}
    				}
    				$response->setContent(\Zend\Json\Json::encode(array('status' => false, 'msg' => 'Ocorreu um erro ao filtrar a pesquisa' )));
    				return $response;
    			}else{
    				$response->setContent(\Zend\Json\Json::encode(array('status' => false, 'msg' => 'VocÃª nÃ£o possui permissÃ£o para realizar essa operaÃ§Ã£o'  )));
    				return $response;
    			}
    		}else{
    			$response->setContent(\Zend\Json\Json::encode(array('status' => false, 'msg' => 'Sua sessÃ£o expirou, favor relogar'  )));
    			return $response;
    		}
    	}catch ( \Exception $e ){
    		$response->setContent(\Zend\Json\Json::encode(array('status' => false, 'msg' => 'Ocorreu um erro ao filtrar a pesquisa'  )));
    		return $response;
    	}
    }
    
    public function checkIfEmailExistsAction() {
    	try {
    		$response = $this->getResponse();
    		if ($this->verifyUserSession ()) {
    			$acl = $this->getServiceLocator()->get('Admin\Permissions\Acl');
    			if ($acl->isAllowed($this->session->user->rol->name, "Ã�rea de trabalho", "Trocar senha")) {
    				$formData = $this->getFormData ();
    				$email = $formData ['email'];
    				$userId = null;
    				if ($formData ['id'])
    					$userId = $formData ['id'];
    					
    				$serviceLocator = $this->getServiceLocator ();
    				$userService = $serviceLocator->get ( 'Storage\Service\UserService' );
    				if ($userService->checkIfEmailExists ( $email, $userId )) {
    					$response->setContent ( \Zend\Json\Json::encode ( array (
    							'status' => false,
    							'isLogged' => true
    					) ) );
    					return $response;
    				} else {
    					$response->setContent ( \Zend\Json\Json::encode ( array (
    							'status' => true,
    							'isLogged' => true
    					) ) );
    					return $response;
    				}
    			}
    			$this->showMessage('VocÃª nÃ£o possui permissÃµes para realizar essa operaÃ§Ã£o.', 'home-error');
    			$response->setContent ( \Zend\Json\Json::encode ( array ('status' => false, 'isLogged' => true) ) );
    			return $response;
    		}
    		$this->showMessage('VocÃª precisa fazer o login para realizar essa operaÃ§Ã£o', 'home-error');
    		$response->setContent ( \Zend\Json\Json::encode ( array ('status' => false, 'isLogged' => false, 'permitted' => true,) ) );
    		return $response;
    	} catch ( \Exception $e ) {
    		$response->setContent ( \Zend\Json\Json::encode ( array (
    				'status' => false,
    				'isLogged' => true
    		) ) );
    		return $response;
    	}
    }
    
    public function changePasswordAction() {
    	try {
    		$request = $this->getRequest();
    		$response = $this->getResponse();
    		$userService = $this->getServiceLocator ()->get ( 'Storage\Service\UserService' );
    		if ($this->verifyUserSession ()) {
    			$user_pass = $userService->getById ( $this->session->user );
    			if ($request->isPost()){
    				$data = $request->getPost();
	    			$acl = $this->getServiceLocator()->get('Admin\Permissions\Acl');
	    			if ($acl->isAllowed($this->session->user->rol->name, "Ã�rea de trabalho", "Trocar senha")) {
    					$passOld = $data['password'];
    					$passNew1 = sha1 ( $data['passwordNew1'] );
    					$passNew2 = sha1 ( $data['passwordNew2'] );
    					$lengthValidator = new StringLength(array('min' => 6, 'max' => 45));
    					if (sha1 ( $passOld ) == $user_pass->password) {
    						if($lengthValidator->isValid($data['passwordNew1']) && $lengthValidator->isValid($data['passwordNew2'])){
    							if ($passNew1 == $passNew2) {
    								$user_pass->password = $passNew1;
    								$user_change = $userService->updateUser ( $user_pass );
    								$this->msgSucesso = "<div class='alert alert-success' role='alert'> Senha alterada com sucesso! </div>";;
    							} else {
    								return $this->showMessage('Os campos de senha devem ser iguais!', 'error-user-config', '/configurations');
    							}
    						} else {
    							return $this->showMessage('Nova senha deve conter no mÃ­nimo 6 caracteres!', 'error-user-config', '/configurations');
    						}
    					}else{
    						return $this->showMessage('Senha atual incorreta!', 'error-user-config', '/configurations');
    					}
	    			}
	    			else {
	    				return $this->showMessage('VocÃª nÃ£o possui permissÃµes para realizar essa operaÃ§Ã£o', 'home-error', '/');
	    			}
    			}
    			
    		} else {
    			return $this->showMessage('Sua sessÃ£o expirou, favor relogar', 'home-error', '/');
    		}
    		return $this->showMessage('Senha alterada com sucesso', 'success-user-config', '/configurations');
    		
    	}catch (\Exception $e){
    		return $this->showMessage('NÃ£o foi possÃ­vel alterar a senha, contate o administrador do sistema', 'home-error', '/configurations');
    	}
    }
    
    public function __set($name, $value) {
    	$this->$name = $value;
    }
    public function __get($name) {
    	return $this->$name;
    }
    
    public function checkCurrentPasswordAction() {
    	try {
    		$response = $this->getResponse();
    		if ($this->verifyUserSession ()) {
    			$acl = $this->getServiceLocator()->get('Admin\Permissions\Acl');
    			if ($acl->isAllowed($this->session->user->rol->name, "Ã�rea de trabalho", "Trocar senha")) {
    				$auth_user = $this->session->user;
    				$formData = $this->getFormData ();
    				$password = sha1($formData ['password']);
    				
    				if ($password == $auth_user->password) {
    					$response->setContent ( \Zend\Json\Json::encode ( array (
    							'status' => true,
    							'isLogged' => true
    					) ) );
    					return $response;
    				} else {
    					$response->setContent ( \Zend\Json\Json::encode ( array (
    							'status' => false,
    							'isLogged' => true
    					) ) );
    					return $response;
    				}
    			}
    			$this->showMessage('VocÃª nÃ£o possui permissÃµes para realizar essa operaÃ§Ã£o.', 'home-error');
    			$response->setContent ( \Zend\Json\Json::encode ( array ('status' => false, 'isLogged' => true) ) );
    			return $response;
    		}
    		$this->showMessage('VocÃª precisa fazer o login para realizar essa operaÃ§Ã£o', 'home-error');
    		$response->setContent ( \Zend\Json\Json::encode ( array ('status' => false, 'isLogged' => false, 'permitted' => true,) ) );
    		return $response;
    	} catch ( \Exception $e ) {
    		$response->setContent ( \Zend\Json\Json::encode ( array (
    				'status' => false,
    				'isLogged' => true
    		) ) );
    		return $response;
    	}
    }
}
