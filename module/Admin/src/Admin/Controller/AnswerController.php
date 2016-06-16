<?php

namespace Admin\Controller;

use Main\Controller\MainController;
use Storage\Entity\Answer;
use Storage\Service\AnswerService;
use Storage\Service\CompetenceService;
use Storage\Service\QuestionService;

class AnswerController extends MainController {
	public function indexAction() {
	return array();
		
	}
	
	public function CreateAnswerAction() {
		try {
		$request = $this->getRequest();
		$serviceLocator = $this->getServiceLocator ();
		$answerService = $serviceLocator->get ( 'Storage\Service\AnswerService' );
		$competenceService = $serviceLocator->get ( 'Storage\Service\CompetenceService' );
		$questionService = $serviceLocator->get ( 'Storage\Service\QuestionService' );
		
		$competencies = $competenceService->listAll();
		$questions = $questionService->listAll();
		
		$formData = $this->getFormData ();
		if($formData ['answerId']){
						$id = $formData ['answerId'];
						$answer = $answerService->getById ( $id);
					}
					if ($request->isPost ()) {
						if (!isset($answer)){ // O id indica que o usuário está sendo modificado
							$answer = new Answer ();
						}
						$question = $formData ['questions'] ;
						if (isset($question)){
							$q = $questionService->getById($question[0]);
							$answer->questionId = $q;
						}else {
								$url = '/answer/createAnswer';
								if (isset($id))
									$url .= '?id='.$answer->answerId;
									return $this->showMessage('O campo pergunta é obrigatório.', 'home-error', $url);
							}
							
						$competence = $formData ['competencies'];
							if (isset($competence)){
								$c = $competenceService->getById($competence[0]);
								$answer->competenceId = $c;
							}else {
									$url = '/answer/createAnswer';
									if (isset($id))
										$url .= '?id='.$answer->answerId;
										return $this->showMessage('O campo competencia é obrigatório.', 'home-error', $url);
						}
								
						$score = trim ( $formData ['score'] );
						if (isset($score))
							$answer->score = $score;
							else {
								$url = '/answer/createAnswer';
								if (isset($id))
									$url .= '?id='.$answer->answerId;
									return $this->showMessage('O campo pontua��o � obrigat�rio.', 'home-error', $url);
							}
						
						$description = trim ( $formData ['description'] );
						if (isset($description))
							$answer->answerDesc = $description;
						else {
							$url = '/answer/createAnswer';
							if (isset($id))
								$url .= '?id='.$answer->answerId;
							return $this->showMessage('O campo descri��o � obrigat�rio.', 'home-error', $url);
						}
						
						if (isset($id)) {
							$response = $answerService->updateAnswer ($answer);
							if ($response) {
								return $this->showMessage('Questão alterada com sucesso!', 'home-success', '/answer/listAnswer');
							} else
								return $this->showMessage('Não foi possivel alterar a questão.', 'home-error', '/answer/createAnswer?id='.$answer->answerId);
						} else {
							$response = $answerService->add ( $answer );
							if ($response)
								return $this->showMessage('Questão criada com sucesso!', 'home-success', '/answer/listAnswer');
							else
								return $this->showMessage('Não foi possível criar questão.', 'home-error', '/answer/createAnswer');
						}
					} else {
						return array ('competencies' => $competencies,
									  'questions' => $questions
						);
						
	
					}
	
		} catch ( \Exception $e ) {
			return $this->showMessage('N�o foi poss�vel realizar opera��o', 'home-error', '/answer/listAnswer');
		}
}

		public function ListAnswerAction() {
			try {
						$serviceLocator = $this->getServiceLocator ();
						$answerService = $serviceLocator->get ( 'Storage\Service\AnswerService' );
							
						$answers = $answerService->listAll();
						return array ('answers' => $answers);
						
			} catch ( \Exception $e ) {
				return $this->showMessage('N�o foi poss�vel recuperar quest�es cadastradas', 'home-error', '/answer');
			}
		}
		
		public function getBySequenceAnswerAction() {
			try {
				$response = $this->getResponse();
					
				$serviceLocator = $this->getServiceLocator ();
				$answerService = $serviceLocator->get ( 'Storage\Service\AnswerService' );
					
				$formData = $this->getFormData ();
				$sequence = $formData['sequence'];
					
				$answer = $answerService->getBySequence($sequence);
					
				$id1 = strval($answer[0]->answerId);
				$id2 = strval($answer[1]->answerId);
				$id3 = strval($answer[2]->answerId);
				$id4 = strval($answer[3]->answerId);
		
				
				$response->setContent ( \Zend\Json\Json::encode ( array ( '1' => $answer[0]->answerDesc,'2' => $answer[1]->answerDesc,'3' => $answer[2]->answerDesc,'4' => $answer[3]->answerDesc,'5' => $id1,'6' => $id2,'7' => $id3,'8' => $id4) ) );
				return $response;
		
			} catch ( \Exception $e ) {
				return $this->showMessage ( 'N�o foi poss�vel recuperar quest�es cadastradas', 'home-error', '/question' );
			}
		}
	
}
