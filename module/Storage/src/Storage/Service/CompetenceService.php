<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Configurator;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

class CompetenceService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\Competence";
    }
    
    public function getById($id) {
        try {
        	$repository=$this->em->getRepository('Storage\Entity\Competence');
        	$criteria=array("competenceId"=>$id);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
        	return $aUse;
    	}catch (\Exception $e){
    	    return $e->getMessage();
    	}
    }
    
    public function getByName($name) {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\Competence');
    		$criteria=array("description"=>$name);
    		$orderBy=null;
    		$aUse=$repository->findOneBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return $e->getMessage();
    	}
    }

    public function updateCompetence($data) {
        $sql = 'UPDATE competence SET '. 
        		',competence_desc="'.$data->description.'"';
        
		$sql .= ' WHERE competence_id='.$data->competenceId;
        
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