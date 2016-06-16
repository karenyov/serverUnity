<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;

class TeacherService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\Teacher";
    }
    
    public function getById($id) {
        try {
        	$repository=$this->em->getRepository('Storage\Entity\Teacher');
        	$criteria=array("teacherId"=>$id);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
        	return $aUse;
    	}catch (\Exception $e){
    	    return $e->getMessage();
    	}
    }

    public function updateTeacher($data) {
        $sql = 'UPDATE teacher SET '. 
        		',insitution_id="'.$data->institutionId.'"'.
        		',name="'.$data->name.'"';
        
		$sql .= ' WHERE teacher_id='.$data->teacherId;
        
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