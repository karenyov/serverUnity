<?php

namespace Admin\Controller;

use Main\Controller\MainController;
use Storage\Entity\Teacher;
use Storage\Service\TeacherService;
use Zend\Session\Container;
use Storage\Entity\Access;
use Zend\I18n\Validator\Alnum;
use Zend\I18n\Validator\Alpha;
use Zend\I18n\Validator\Int;
use Zend\Validator\StringLength;
use Zend\I18n\Validator\PhoneNumber;
use Zend\I18n\Validator\Zend\I18n\Validator;

class TeacherController extends MainController {
	public function indexAction() {
		return array();
	}
	
	public function CreateTeacherAction() {
		try {
		$request = $this->getRequest();
		$serviceLocator = $this->getServiceLocator ();
		$teacherService = $serviceLocator->get ( 'Storage\Service\TeacherService' );
		$institutionService = $serviceLocator->get ( 'Storage\Service\InstitutionService' );
		
		$institutions = $institutionService->listAll();
		
		$formData = $this->getFormData ();
		if($formData ['id']){
						$id = $formData ['id'];
						$teacher = $teacherService->getById ( $id);
					}
					if ($request->isPost ()) {
						if (!isset($teacher)){ // O id indica que o usuário está sendo modificado
							$teacher = new Teacher ();
						}
						
						$alphaValidator = new Alpha ( true );
						$alphaNumValidator = new Alnum( true );
						$nameLengthValidator = new StringLength ( array (
							'min' => 4,
							'max' => 50
						) );
						$passwordLengthValidator = new StringLength ( array (
							'min' => 6,
							'max' => 20
						) );
						$raLengthValidator = new StringLength ( array (
								'min' => 1
								
						) );
						$name = trim ( $formData ['name'] );
						if ($nameLengthValidator->isValid ( $name ))
							$teacher->teacherDesc = $name;
						else {
							$url = '/teacher/createTeacher';
							if ($id)
								$url .= '?id='.$teacher->teacherId;
							return $this->showMessage('O campo nome é obrigatório e deve conter no minimo 4 e no máximo 50 caracteres', 'home-error', $url);
						}
						
						$institution = $formData ['intitutions'] ;
							if (isset($institution)){
								$q = $institutionService->getById($institution[0]);
								$teacher->institutionId = $q;
							}else {
								$url = '/teacher/createTeacher';
								if (isset($id))
									$url .= '?id='.$teacher->teacherId;
									return $this->showMessage('O campo institui��o é obrigatório.', 'home-error', $url);
							}								
						
						if (isset($id)) {
							$response = $teacherService->updateTeacher ($teacher);
							if ($response) {
								return $this->showMessage('Institui��o alterada com sucesso!', 'home-success', '/teacher/associateProjects?id=' . $teacher->teacherId);
							} else
								return $this->showMessage('Não foi possível alterar as informações da institui��o', 'home-error', '/teacher/form?id='.$teacher->teacherId);
						} else {
							$response = $teacherService->add ( $teacher );
							if ($response)
								return $this->showMessage('Institui��o criado com sucesso!', 'home-success', '/teacher/listTeacher');
							else
								return $this->showMessage('Não foi possível inserir a Institui��o', 'home-error', '/teacher/createTeacher');
						}
					} else {
						return array ('institutions' => $institutions
						);
	
					}
	
		} catch ( \Exception $e ) {
			return $this->showMessage('Não foi possível realizar essa operação', 'home-error', '/teacher');
		}
}

		public function ListTeacherAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$teacherService = $serviceLocator->get ( 'Storage\Service\TeacherService' );
							
						$teachers = $teacherService->listAll ();
						return array ('teachers' => $teachers);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('Não foi possível recuperar as institui��es cadastrados', 'admin-error', '/user');
			}
		}
	
}
