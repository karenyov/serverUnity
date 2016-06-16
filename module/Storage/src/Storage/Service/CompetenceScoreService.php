<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

class CompetenceScoreService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\CompetenceScore";
    }
    
    public function getById($id) {
        try {
        	$repository=$this->em->getRepository('Storage\Entity\CompetenceScore');
        	$criteria=array("competenceId"=>$id);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
        	return $aUse;
    	}catch (\Exception $e){
    	    return $e->getMessage();
    	}
    }
    
    public function getByUser($id) {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\CompetenceScore');
    		$criteria=array("userId"=>$id);
    		$orderBy=null;
    		$aUse=$repository->findBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return $e->getMessage();
    	}
    }
    
    public function getByCompetenceAndUser($competence, $user) {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\CompetenceScore');
    		$criteria=array("userId"=>$user, "competenceId" => $competence );
    		$aUse=$repository->findOneBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return $e->getMessage();
    	}
    }
    
    /*
     * public function getByUser($id){
    	try {
    		$sql="select cs.* from competence_score cs WHERE cs.user_id = '".$id."'";
    		 
    		$rsm=new ResultSetMapping();
    		$rsm->addEntityResult('Storage\Entity\CompetenceScore', 'cs');
    		$rsm->addFieldResult('cs','competence_score_id','competenceScoreId');
    		$rsm->addFieldResult('cs','user_id','userId');
    		$rsm->addFieldResult('cs','competence_id','competenceId');
    		$rsm->addFieldResult('cs','score','score');
    		 
    		$query = $this->em->createNativeQuery($sql, $rsm);
    		$user=$query->getOneOrNullResult();
    		$this->em->clear();
    		return $user;
    	}catch (\Exception $e){
    		return null;
    	}
    }
     */
    

    public function updateCompetence($score, $competence, $user) {
        $sql = 'UPDATE competence_score SET '. 
        		'score="'.$score.'"';
        
		$sql .= ' WHERE competence_id='.$competence. ' AND user_id='.$user;
        
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