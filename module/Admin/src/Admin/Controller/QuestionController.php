<?php

namespace Admin\Controller;

use Main\Controller\MainController;
use Storage\Entity\Question;
use Storage\Service\QuestionService;
use Zend\Session\Container;
use Storage\Entity\Access;
use Zend\I18n\Validator\Alnum;
use Zend\I18n\Validator\Alpha;
use Zend\I18n\Validator\Int;
use Zend\Validator\StringLength;
use Zend\I18n\Validator\PhoneNumber;
use Zend\I18n\Validator\Zend\I18n\Validator;

class QuestionController extends MainController {
	public function indexAction() {
		return array ();
	}
	public function CreateQuestionAction() {
		try {
			$request = $this->getRequest ();
			$serviceLocator = $this->getServiceLocator ();
			$questionService = $serviceLocator->get ( 'Storage\Service\QuestionService' );
			
			$formData = $this->getFormData ();
			if ($formData ['questionId']) {
				$id = $formData ['questionId'];
				$question = $questionService->getById ( $id );
			}
			if ($request->isPost ()) {
				if (! isset ( $question )) { // O id indica que o usuário está sendo modificado
					$question = new Question ();
				}
				
				$description = trim ( $formData ['description'] );
				if (isset ( $description ))
					$question->description = $description;
				else {
					$url = '/user/createQuestion';
					if ($id)
						$url .= '?id=' . $question->questionId;
					return $this->showMessage ( 'O campo descri��o � obrigat�rio.', 'home-error', $url );
				}
				
				if (isset ( $id )) {
					$response = $questionService->updateQuestion ( $question );
					if ($response) {
						return $this->showMessage ( 'Quest�o alterada com sucesso!', 'home-success', '/question/listQuestion' );
					} else
						return $this->showMessage ( 'N�o foi possivel alterar a quest�o', 'home-error', '/question/createQuestion?id=' . $question->questionId );
				} else {
					$response = $questionService->add ( $question );
					if ($response)
						return $this->showMessage ( 'Quest�o criada com sucesso!', 'home-success', '/question/listQuestion' );
					else
						return $this->showMessage ( 'N�o foi poss�vel criar quest�o.', 'home-error', '/question/createQuestion' );
				}
			} else {
				$errorMessage = '';
			}
		} catch ( \Exception $e ) {
			return $this->showMessage ( 'N�o foi poss�vel realizar opera��o', 'home-error', '/question/listQuestion' );
		}
	}
	public function ListQuestionAction() {
		try {
			$serviceLocator = $this->getServiceLocator ();
			$questionService = $serviceLocator->get ( 'Storage\Service\QuestionService' );
			
			$questions = $questionService->listAll ();
			return array (
					'questions' => $questions 
			);
		} catch ( \Exception $e ) {
			return $this->showMessage ( 'N�o foi poss�vel recuperar quest�es cadastradas', 'home-error', '/question' );
		}
	}
	
	public function getBySequenceAction() {
		try {
			$response = $this->getResponse();
			
			$serviceLocator = $this->getServiceLocator ();
			$questionService = $serviceLocator->get ( 'Storage\Service\QuestionService' );
			
			$formData = $this->getFormData ();
			$sequence = $formData['sequence'];
			
			$question = $questionService->getBySequence($sequence);
	
			$response->setContent ( \Zend\Json\Json::encode ( array ('question' => $question->description) ) );
    		return $response;
    		
		} catch ( \Exception $e ) {
			return $this->showMessage ( 'N�o foi poss�vel recuperar quest�es cadastradas', 'home-error', '/question' );
		}
	}
}
