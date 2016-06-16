<?php

namespace Admin\Controller;

use Main\Controller\MainController;
use Storage\Entity\Institution;
use Storage\Service\InstitutionService;
use Zend\Session\Container;
use Storage\Entity\Access;
use Zend\I18n\Validator\Alnum;
use Zend\I18n\Validator\Alpha;
use Zend\I18n\Validator\Int;
use Zend\Validator\StringLength;
use Zend\I18n\Validator\PhoneNumber;
use Zend\I18n\Validator\Zend\I18n\Validator;

class InstitutionController extends MainController {
	public function indexAction() {
		return array();
	}
	
	public function CreateInstitutionAction() {
		try {
		$request = $this->getRequest();
		$serviceLocator = $this->getServiceLocator ();
		$institutionService = $serviceLocator->get ( 'Storage\Service\InstitutionService' );
		
		$formData = $this->getFormData ();
		if($formData ['id']){
						$id = $formData ['id'];
						$institution = $institutionService->getById ( $id);
					}
					if ($request->isPost ()) {
						if (!isset($institution)){ // O id indica que o usuÃ¡rio estÃ¡ sendo modificado
							$institution = new Institution ();
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
						$name = trim ( $formData ['institutionDesc'] );
						if ($nameLengthValidator->isValid ( $name ))
							$institution->institutionDesc = $name;
						else {
							$url = '/institution/createInstitution';
							if ($id)
								$url .= '?id='.$institution->institutionId;
							return $this->showMessage('O campo nome Ã© obrigatÃ³rio e deve conter no minimo 4 e no mÃ¡ximo 50 caracteres', 'home-error', $url);
						}
						
						$number = trim ( $formData ['number'] );
						if ($raLengthValidator->isValid ( $number ))
							$institution->number = $number;
							else {
								$url = '/institution/createInstitution';
								if (isset($id))
									$url .= '?id='.$institution->useId;
									return $this->showMessage('O campo ra Ã© obrigatÃ³rio e deve conter 13 caracteres', 'home-error', $url);
							}								
						
						if (isset($id)) {
							$response = $institutionService->updateInstitution ($institution);
							if ($response) {
								return $this->showMessage('Instituição alterada com sucesso!', 'home-success', '/institution/associateProjects?id=' . $institution->institutionId);
							} else
								return $this->showMessage('NÃ£o foi possÃ­vel alterar as informaÃ§Ãµes da instituição', 'home-error', '/institution/form?id='.$institution->institutionId);
						} else {
							$response = $institutionService->add ( $institution );
							if ($response)
								return $this->showMessage('Instituição criado com sucesso!', 'home-success', '/institution/listInstitution');
							else
								return $this->showMessage('NÃ£o foi possÃ­vel inserir a Instituição', 'home-error', '/institution/createInstitution');
						}
					} else {
						$errorMessage = '';
	
					}
	
		} catch ( \Exception $e ) {
			return $this->showMessage('NÃ£o foi possÃ­vel realizar essa operaÃ§Ã£o', 'home-error', '/institution');
		}
}

		public function ListInstitutionAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$institutionService = $serviceLocator->get ( 'Storage\Service\InstitutionService' );
							
						$institutions = $institutionService->listAll ();
						return array ('institutions' => $institutions);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('NÃ£o foi possÃ­vel recuperar as instituições cadastrados', 'admin-error', '/user');
			}
		}
	
}
