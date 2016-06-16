<?php

namespace Admin\Controller;

use Main\Controller\MainController;
use Storage\Entity\User;
use Storage\Service\UserService;
use Zend\I18n\Validator\Alnum;
use Zend\I18n\Validator\Alpha;
use Zend\Validator\StringLength;
use Storage\Entity\CompetenceScore;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Json\Json;

class UserController extends MainController {
	public function indexAction() {
		return array();
	}
	
	public function CreateUserAction() {
		try {
		$response = $this->getResponse();	
		$request = $this->getRequest();
		$serviceLocator = $this->getServiceLocator ();
		$userService = $serviceLocator->get ( 'Storage\Service\UserService' );
		$institutionService = $serviceLocator->get ( 'Storage\Service\InstitutionService' );
		$roleService = $serviceLocator->get ( 'Storage\Service\RoleService' );
		$role = $roleService->getById(1);
		$formData = $this->getFormData ();
					if ($request->isPost ()) {
						if (!isset($user)){ // O id indica que o usuÃ¡rio estÃ¡ sendo modificado
							$user = new User ();
						}
						
						$alphaValidator = new Alpha ( true );
						$alphaNumValidator = new Alnum( true );
						$nameLengthValidator = new StringLength ( array (
							'min' => 3,
							'max' => 50
						) );
						$passwordLengthValidator = new StringLength ( array (
							'min' => 6,
							'max' => 20
						) );
						$raLengthValidator = new StringLength ( array (
								'min' => 13,
								'max' => 13
						) );
						$name = trim ( $formData ['name'] );
						if ($nameLengthValidator->isValid ( $name ))
							$user->name = $name;
						else {
							
							$response->setContent ( 'O campo nome é obrigatório e deve conter no minimo 3 e no máximo 50 caracteres.' );								
							return $response;
						}
						
						$ra = trim ( $formData ['ra'] );
						if ($raLengthValidator->isValid ( $ra )){
							$user->ra = $ra;
						}else {
								$response->setContent ('O campo RA deve conter 13 caracteres.');			
								return $response;
						}							
							
						$user->time = 0;
						
						$user->scene = 1;
						
						$user->money = 0;
						
						$user->progress = 0.00; 
						
						$user->institution = $institutionService->getFatec();
						
						$user->role = $role;
								
						$gender = trim ( $formData ['gender'] );
						if (isset($gender)){
							$user->gender = $gender;
						}else {
							$response->setContent('O campo genêro deve estar preenchido.');
						}
						
						$email = trim ( $formData ['email'] );
						if (isset($email)) {
							if (! $userService->checkIfEmailExists ( $formData ['email'], null )){
								$user->email = $email;
							}else {
								$response->setContent ('O e-mail digitado existe no sistema.');
								return $response;
							}
						} else {
								$response->setContent ('O e-mail digitado já está cadastrado no sistema.');
								return $response;
						}
						$password = trim ( $formData ['password'] );
						if ($formData ['password']) {
							if ($passwordLengthValidator->isValid ( $password )){
								$user->definePassword ( $password );
							}else {
								$response->setContent ('O campo senha precisa de no minimo 6 caracteres.');
								return $response;
							}
						}

							$responseUser = $userService->addUser ( $user );
							if (!$responseUser){
								$response->setContent ('Ocorreu um erro ao realizar cadastro.');
								return $response;
							}else{
								$this->createCompetenceScore($responseUser);
								$response->setContent ('Cadastro realizado com sucesso.');
								return $response;
							}
					}
				
		} catch ( \Exception $e ) {
			$response->setContent ('Ocorreu um erro ao realizar cadastro.');
			return $response;
		}
}

		public function ListUserAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$userService = $serviceLocator->get ( 'Storage\Service\UserService' );
							
						$users = $userService->listAll ();
						return array ('users' => $users);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('NÃ£o foi possÃ­vel recuperar os usuÃ¡rios cadastrados', 'admin-error', '/user');
			}
		}
	
	public function updateUserInformationAction(){
		try {
				$request = $this->getRequest();
				$response = $this->getResponse();
				$serviceLocator = $this->getServiceLocator ();
				$userService = $serviceLocator->get ( 'Storage\Service\UserService' );
				$answerService = $serviceLocator->get ( 'Storage\Service\AnswerService' );
				$competenceService = $serviceLocator->get ( 'Storage\Service\CompetenceService' );
				$competenceScoreService = $serviceLocator->get ( 'Storage\Service\CompetenceScoreService' );
						
					$formData = $this->getFormData ();
					
					$time = $formData['time'];
					$money = $formData['money'];
					$answerId = $formData['answerId'];
					$mail = $formData['email'];
					
					$use = $userService->getByEmail($mail);
					
					$answer = $answerService->getById($answerId);
					$sequence = $answer->sequence;
					
					$user = new User;
					$user->useId = $use->useId;
					$user->time = $time;
					$user->money = $money;
					
					$user->scene = $sequence+1;
					
					
					$userResponse= $userService->updateUser($user);
					
					$competenceId = $answer->competenceId->competenceId;			
					$competence = $competenceService->getById($competenceId);							
					$competenceScore = $competenceScoreService->getByCompetenceAndUser($competence, $user->useId);
					$newScore = $competenceScore->score + $answer->score;
					
					$responseCompetence = $competenceScoreService->updateCompetence($newScore, $competence->competenceId, $user->useId);
					
				$response->setContent ( Json::encode ( array ( 'msg' => $responseCompetence) ) );
				return $response;
			/**}
			$response->setContent ( Json::encode ( array ( 'teste' => 'usuário nao está na sessão') ) );
			return $response;
			**/
		}catch (\Exception $e){
			$response->setContent ( Json::encode ( array ( 'teste' => $e->getMessage()) ) );
			return $response;
		}
	}
	
	private function  createCompetenceScore($user){
		$serviceLocator = $this->getServiceLocator ();
		$competenceService = $serviceLocator->get ( 'Storage\Service\CompetenceService' );
		$competenceScoreService = $serviceLocator->get ( 'Storage\Service\CompetenceScoreService' );
		$userService = $serviceLocator->get ( 'Storage\Service\UserService' );
				
		$competences = $competenceService->listAll();
		
		foreach ($competences as $competence){
			$competenceScore = new CompetenceScore();
			$competenceScore->userId = $user;
			$competenceScore->score = 0;
			$competenceScore->competenceId = $competence;
			
			$competenceScoreService->add($competenceScore);
		}
		
	}
}
