<?php

namespace Admin\Controller;

use Main\Controller\MainController;
use Storage\Entity\Competence;
use Storage\Service\CompetenceService;

class CompetenceController extends MainController {
	public function indexAction() {
		return array();
	}
	
	public function CreateCompetenceAction() {
		try {
		$request = $this->getRequest();
		$serviceLocator = $this->getServiceLocator ();
		$competenceService = $serviceLocator->get ( 'Storage\Service\CompetenceService' );
		
		$formData = $this->getFormData ();
		if($formData ['competenceId']){
						$id = $formData ['competenceId'];
						$competence = $competenceService->getById ( $id);
					}
					if ($request->isPost ()) {
						if (!isset($competence)){ // O id indica que o usuário está sendo modificado
							$competence = new Competence ();
						}
						
						$description = trim ( $formData ['description'] );
						if (isset($description))
							$competence->description = $description;
						else {
							$url = '/competence/createCompetence';
							if ($id)
								$url .= '?id='.$competence->competenceId;
							return $this->showMessage('O campo descri��o � obrigat�rio.', 'home-error', $url);
						}
						
						if (isset($id)) {
							$response = $competenceService->updateCompetence ($competence);
							if ($response) {
								return $this->showMessage('Compet�ncia alterada com sucesso!', 'home-success', '/competence/listCompetence');
							} else
								return $this->showMessage('N�o foi possivel alterar a Compet�ncia', 'home-error', '/competence/createCompetence?id='.$competence->competenceId);
						} else {
							$response = $competenceService->add ( $competence );
							if ($response)
								return $this->showMessage('Compet�ncia criada com sucesso!', 'home-success', '/competence/listCompetence');
							else
								return $this->showMessage('N�o foi poss�vel criar Compet�ncia.', 'home-error', '/competence/createCompetence');
						}
					} else {
						$errorMessage = '';
	
					}
	
		} catch ( \Exception $e ) {
			return $this->showMessage('N�o foi poss�vel realizar opera��o', 'home-error', '/competence/listCompetence');
		}
}

		public function ListCompetenceAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$competenceService = $serviceLocator->get ( 'Storage\Service\CompetenceService' );
							
						$competencies = $competenceService->listAll();
						return array ('competencies' => $competencies);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('N�o foi poss�vel recuperar quest�es cadastradas', 'home-error', '/competence');
			}
		}
	
}
