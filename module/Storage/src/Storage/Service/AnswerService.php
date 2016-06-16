<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Configurator;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

class AnswerService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\Answer";
    }
    
    public function getById($id) {
        try {
        	$repository=$this->em->getRepository('Storage\Entity\Answer');
        	$criteria=array("answerId"=>$id);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
        	return $aUse;
    	}catch (\Exception $e){
    	    return $e->getMessage();
    	}
    }

    public function getBySequence($sequence) {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\Answer');
    		$criteria=array("sequence"=>$sequence);
    		$orderBy=null;
    		$aUse=$repository->findBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return $e->getMessage();
    	}
    }
    public function updateAnswer($data) {
        $sql = 'UPDATE answer SET '. 
        		',question_id="'.$data->questionId.'"'.
        		',competence_id="'.$data->competenceId.'"'.
        		',answer_score="'.$data->score.'"'.
        		',answer_desc="'.$data->description.'"';
        
		$sql .= ' WHERE answer_id='.$data->questionId;
        
        $conn = $this->em->getConnection ();
         
        try {
        	$conn->beginTransaction ();
        	 
        	$resultExec = $conn->exec ( $sql );
        	 
        	$conn->commit ();
        	return true;
        } catch ( \Doctrine\DBAL\DBALException $dbalExc ) {
        	$conn->rollBack ();
        	return false;
        } catch ( \Exception $e ) {
        	$conn->rollBack ();
        	return false;
        }
    }
    
    
}