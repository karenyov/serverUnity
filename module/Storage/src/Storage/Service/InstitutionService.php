<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Configurator;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

class InstitutionService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\Institution";
    }
    
    public function getById($id) {
        try {
        	$repository=$this->em->getRepository('Storage\Entity\Institution');
        	$criteria=array("institutionId"=>$id);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
        	return $aUse;
    	}catch (\Exception $e){
    	    return $e->getMessage();
    	}
    }
    
    public function getFatec() {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\Institution');
    		$criteria=array("number"=>1);
    		$aUse=$repository->findOneBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return $e->getMessage();
    	}
    }

    public function updateInstitution($data) {
        $sql = 'UPDATE institution SET '. 
        		',number="'.$data->number.'"'.
        		',institution_desc="'.$data->institutionDesc.'"';
        
		$sql .= ' WHERE institution_id='.$data->institutionId;
        
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