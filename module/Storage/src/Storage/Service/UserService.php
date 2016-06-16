<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

class UserService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\User";
    }
    
    public function addUser($data){
    	$sql="INSERT INTO user (institution, rol_id, name, ".
    			"ra, email, password, gender, progress, time,scene, money, reset_token) ".
    			"VALUES ('".$data->institution->institutionId."','".$data->role->rolId."', '".$data->name."', '".$data->ra."', '".$data->email."', '".$data->password."', '".$data->gender."', '".$data->progress."', '".$data->time."', '".$data->scene."', '".$data->money."', '".$data->resetToken."')";
    
    	$conn=$this->em->getConnection();
    
    	try {
    		$conn->beginTransaction();
    
    		$resultExec = $conn->exec($sql);
    
    		if($resultExec==0) {
    			$conn->rollBack();
    			return false;
    		}else{
    			$id = $conn->lastInsertId();
    			$entity = $this->getById($id);
    
    			$conn->commit();
    		}
    		return $entity;
    	}catch (\Doctrine\DBAL\DBALException $dbalExc){
    		$conn->rollBack();
    		return false;
    	}catch (\Exception $e){
    		$conn->rollBack();
    		return false;
    	}
    }
    
    
    public function getById($id) {
        try {
        	$repository=$this->em->getRepository('Storage\Entity\User');
        	$criteria=array("useId"=>$id);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
        	return $aUse;
    	}catch (\Exception $e){
    	    return null;
    	}
    }
    
    public function getByEmail($email) {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\User');
    		$criteria=array("email"=>$email);
    		$orderBy=null;
    		$aUse=$repository->findOneBy($criteria);
    		if ($aUse != null){
    			return $aUse;
    		}else{
    			return false;
    		}   		
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function getByName($name) {
    	try {
    		$repository=$this->em->getRepository('Storage\Entity\User');
    		$criteria=array("name"=>$name);
    		$orderBy=null;
    		$aUse=$repository->findOneBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return null;
    	}
    }

    public function updateUser($data) {
        $sql = 'UPDATE user SET '. 
        		'time="'.$data->time.'"'.
        		',scene="'.$data->scene.'"'.
        		',money="'.$data->money.'"';
        
		$sql .= ' WHERE user_id='.$data->useId;
        
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
    
    public function clearResetToken($userId) {
    	try{
    		$entity = $this->em->getReference($this->entity, $userId);
    		$entity->resetToken = null;
    		$this->em->persist($entity);
    		$this->em->flush();
    
    		return $entity;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function listAll(){
        try {
        	$repository=$this->em->getRepository($this->entity);
        	$orderBy = array("name"=>"ASC");
        	$users = $repository->findBy(array(),$orderBy);
        	return $users;
    	}catch (\Exception $e){
    	    return null;
    	}
    }
    
    public function findByEmail($email){
    	try {
    		$repository=$this->em->getRepository($this->entity);
    		$criteria=array("email"=>$email);
        	$orderBy=null;
        	$aUse=$repository->findOneBy($criteria);
    		return $aUse;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function checkIfEmailExists($email, $id=null){
        try {
        	
        	$user = $this->getByEmail($email);
        	
            if($user){
            	if($id){
            		if($user->useId != $id){
            			return true;
            		}
            	}else{ 
            		return true;
            	}
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
        
    }

    public function identifyUserByEmail($email){
    	try {
    		$sql="select us.* from user us WHERE us.email = '".$email."'";
    		 
    		$rsm=new ResultSetMapping();
    		$rsm->addEntityResult('Storage\Entity\User', 'us');
    		$rsm->addFieldResult('us','user_id','useId');
    		$rsm->addFieldResult('us','institution','institution'); 		
    		$rsm->addFieldResult('us','name','name');
    		$rsm->addFieldResult('us','email','email');
    		$rsm->addFieldResult('us','ra','ra');
    		$rsm->addFieldResult('us','gender','gender');
    		$rsm->addFieldResult('us','progress','progress');
    		$rsm->addFieldResult('us','time','time');
    		$rsm->addFieldResult('us','scene','scene');
    		$rsm->addFieldResult('us','money','money');
    		$rsm->addFieldResult('us','reset_token','resetToken');
    	
    		$query = $this->em->createNativeQuery($sql, $rsm);
    		$user=$query->getOneOrNullResult();
    		$this->em->clear();
    		return $user;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function getByEmailAndToken($email, $token){
    	try {
    		$sql="select us.* from user us WHERE us.email = '".$email. "' AND us.reset_token='".$token."'";
    		 
    		$rsm=new ResultSetMapping();
    		$rsm->addEntityResult('Storage\Entity\User', 'us');
    		$rsm->addFieldResult('us','user_id','userId');
    		$rsm->addFieldResult('us','institution','institution'); 		
    		$rsm->addFieldResult('us','name','name');
    		$rsm->addFieldResult('us','email','email');
    		$rsm->addFieldResult('us','ra','ra');
    		$rsm->addFieldResult('us','gender','gender');
    		$rsm->addFieldResult('us','progress','progress');
    		$rsm->addFieldResult('us','time','time');
    		$rsm->addFieldResult('us','scene','scene');
    		$rsm->addFieldResult('us','money','money');
    		$rsm->addFieldResult('us','reset_token','resetToken');
    	
    		$query = $this->em->createNativeQuery($sql, $rsm);
    		$user=$query->getOneOrNullResult();
    		$this->em->clear();
    		return $user;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    

}