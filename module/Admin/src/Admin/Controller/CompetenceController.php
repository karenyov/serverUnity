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
						if (!isset($competence)){ // O id indica que o usuÃ¡rio estÃ¡ sendo modificado
							$competence = new Competence ();
						}
						
						$description = trim ( $formData ['description'] );
						if (isset($description))
							$competence->description = $description;
						else {
							$url = '/competence/createCompetence';
							if ($id)
								$url .= '?id='.$competence->competenceId;
							return $this->showMessage('O campo descrição é obrigatório.', 'home-error', $url);
						}
						
						if (isset($id)) {
							$response = $competenceService->updateCompetence ($competence);
							if ($response) {
								return $this->showMessage('Competência alterada com sucesso!', 'home-success', '/competence/listCompetence');
							} else
								return $this->showMessage('Não foi possivel alterar a Competência', 'home-error', '/competence/createCompetence?id='.$competence->competenceId);
						} else {
							$response = $competenceService->add ( $competence );
							if ($response)
								return $this->showMessage('Competência criada com sucesso!', 'home-success', '/competence/listCompetence');
							else
								return $this->showMessage('Não foi possível criar Competência.', 'home-error', '/competence/createCompetence');
						}
					} else {
						$errorMessage = '';
	
					}
	
		} catch ( \Exception $e ) {
			return $this->showMessage('Não foi possível realizar operação', 'home-error', '/competence/listCompetence');
		}
}

		public function ListCompetenceAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$competenceService = $serviceLocator->get ( 'Storage\Service\CompetenceService' );
							
						$competencies = $competenceService->listAll();
						return array ('competencies' => $competencies);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('Não foi possível recuperar questões cadastradas', 'home-error', '/competence');
			}
		}
	
}
