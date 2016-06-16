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
						if (!isset($teacher)){ // O id indica que o usuÃ¡rio estÃ¡ sendo modificado
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
							return $this->showMessage('O campo nome Ã© obrigatÃ³rio e deve conter no minimo 4 e no mÃ¡ximo 50 caracteres', 'home-error', $url);
						}
						
						$institution = $formData ['intitutions'] ;
							if (isset($institution)){
								$q = $institutionService->getById($institution[0]);
								$teacher->institutionId = $q;
							}else {
								$url = '/teacher/createTeacher';
								if (isset($id))
									$url .= '?id='.$teacher->teacherId;
									return $this->showMessage('O campo instituição Ã© obrigatÃ³rio.', 'home-error', $url);
							}								
						
						if (isset($id)) {
							$response = $teacherService->updateTeacher ($teacher);
							if ($response) {
								return $this->showMessage('Instituição alterada com sucesso!', 'home-success', '/teacher/associateProjects?id=' . $teacher->teacherId);
							} else
								return $this->showMessage('NÃ£o foi possÃ­vel alterar as informaÃ§Ãµes da instituição', 'home-error', '/teacher/form?id='.$teacher->teacherId);
						} else {
							$response = $teacherService->add ( $teacher );
							if ($response)
								return $this->showMessage('Instituição criado com sucesso!', 'home-success', '/teacher/listTeacher');
							else
								return $this->showMessage('NÃ£o foi possÃ­vel inserir a Instituição', 'home-error', '/teacher/createTeacher');
						}
					} else {
						return array ('institutions' => $institutions
						);
	
					}
	
		} catch ( \Exception $e ) {
			return $this->showMessage('NÃ£o foi possÃ­vel realizar essa operaÃ§Ã£o', 'home-error', '/teacher');
		}
}

		public function ListTeacherAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$teacherService = $serviceLocator->get ( 'Storage\Service\TeacherService' );
							
						$teachers = $teacherService->listAll ();
						return array ('teachers' => $teachers);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('NÃ£o foi possÃ­vel recuperar as instituições cadastrados', 'admin-error', '/user');
			}
		}
	
}
