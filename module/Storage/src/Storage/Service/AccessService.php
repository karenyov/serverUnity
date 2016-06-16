<?php
namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Configurator;
use Doctrine\ORM\Query\ResultSetMapping;
class AccessService extends AbstractService {
	
	private $fixedCoordinatorId;

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\Access";
        $this->fixedCoordinatorId = 4;// identificação da Role "Coordenador geral(requisições)" 
    }
    
    public function addAll($accessList){
    	$conn=$this->em->getConnection();
    	
    	foreach ($accessList as $access) {
	    	$sql="INSERT INTO access (prj_id,use_id,coordinator_responsible) values(".$access->prj->prjId.",". $access->use->useId .",".$access->coordinatorResponsible.")";
	    	try {
	    		$resultExec = $conn->exec($sql);
	    	}catch (\Doctrine\DBAL\DBALException $dbalExc){
	    		return false;
	    	}catch (\Exception $e){
	    		return false;
	    	}
    	}
    	try {
    		return true;
    	}catch (\Doctrine\DBAL\ConnectionException $e){
    		return false;
    	}
    }
    
    /**
     * Get Users from database using the criteria with filter.
     * @param Storage\Entity\Project $prj, a Project instance
     * @param boolean $coord, filter to coordinator responsible (values are 1(true) or 0(false)) no effect if this filter is null.
     * @return Array(Storage\Entity\User), the Access instances whith the associations between User and Project.
     */
    
    public function getUseByProject($prj,$coord = NULL) {
        try {
        	$sql="select us.* from access ac, user us ".
        			"WHERE ac.use_id=us.use_id AND ac.prj_id != 8 AND ac.prj_id = ".$prj->prjId." ";
        	if($coord){
        		$sql.="AND ac.coordinator_responsible = ".$coord." ";
        	}
        	
        	$rsm=new ResultSetMapping();
        	$rsm->addEntityResult('Storage\Entity\User', 'us');
        	$rsm->addFieldResult('us','use_id','useId');
        	$rsm->addMetaResult("us", "rol_id", "rol_id");
        	$rsm->addFieldResult('us','name','name');
        	$rsm->addFieldResult('us','email','email');
        	$rsm->addFieldResult('us','phone_number','phoneNumber');
        	$rsm->addFieldResult('us','institution','institution');
        	$rsm->addFieldResult('us','function_name','functionName');
        	$rsm->addFieldResult('us','last_access','lastAccess');
        	$rsm->addFieldResult('us','active','active');
        	$rsm->addFieldResult('us','requisition_tool','requisitionTool');
        	 
        	$query = $this->em->createNativeQuery($sql, $rsm);
        	$access=$query->getResult();
        	$this->em->clear();
        	return $access;
    	}catch (\Exception $e){
    	    return null;
    	}
    }
    /**
     * Get Project from database using the criteria with filter.
     * @param Storage\Entity\User $user, a User instance
     * @param boolean $coord, filter to coordinator responsible (values are 1(true) or 0(false)) no effect if this filter is null.
     * @return Array(Storage\Entity\Project), the Access instances whith the associations between User and Project.
     */
    public function getPrjByUser($user,$coord=NULL) {
        try{
        	$sql="select pr.* from access ac, project pr ".
        			"WHERE ac.prj_id=pr.prj_id AND ac.use_id = ".$user->useId." ";
        	if($coord){
        			$sql.="AND ac.coordinator_responsible = ".$coord." ";
        	}
    
        	$rsm=new ResultSetMapping();
        	$rsm->addEntityResult('Storage\Entity\Project', 'pr');
        	$rsm->addFieldResult('pr','prj_id','prjId');
        	$rsm->addFieldResult('pr','project_name','projectName');
        	$rsm->addFieldResult('pr','slug','slug');
        	$rsm->addFieldResult('pr','desc','desc');
        	$rsm->addFieldResult('pr','desc_full','descFull');
        	$rsm->addFieldResult('pr','logo','logo');
        	$rsm->addFieldResult('pr','report_count','reportCount');
        	
        	$query = $this->em->createNativeQuery($sql, $rsm);
        	$access=$query->getResult();
        	$this->em->clear();
        	return $access;
    	}catch (\Exception $e){
    	    return null;
    	}
    }
    
    public function hasEnoughCoordinators($prj){
    	try {
    		$repository=$this->em->getRepository($this->entity);
    		$criteria=array("prj"=>$prj,"coordinatorResponsible"=>1);
    		$prjs=$repository->findBy($criteria);
    		if($prjs){
    			if(count($prjs) == 2)
    				return true;
    			return false;
    		}
    		return null;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function removeAllByUser($user){
    	$sql="DELETE FROM access WHERE use_id = ".$user->useId;
    
    	$conn=$this->em->getConnection();
    
    	try {
    		$conn->exec($sql);
    		return true;
    	}catch (\Doctrine\DBAL\DBALException $dbalExc){
    		return false;
    	}catch (\Exception $e){
    		return false;
    	}
    }
    
    public function isCoordinatorResponsible($user)
    {
    	try {
    		$sql="SELECT * FROM access where use_id =". $user->useId ." AND coordinator_responsible = 1 AND prj_id != 8";
    		 
    		$rsm=new ResultSetMapping();
    		$rsm->addEntityResult('Storage\Entity\Access', 'ac');
    		$rsm->addFieldResult('ac','acc_id','accId');
    		$rsm->addMetaResult("ac", "prj_id", "prj_id");
    		$rsm->addMetaResult("ac", "use_id", "use_id");
    		$rsm->addFieldResult('ac','coordinator_responsible','coordinatorResponsible');
    		$query = $this->em->createNativeQuery($sql, $rsm);
    		$access=$query->getResult();
    		$this->em->clear();
    		if (empty($access))
    			return false;
    		return true;
    	}catch (\Exception $e){
    		return null;
    	}
        try {
            $accessRepository = $this->em->getRepository($this->entity);
            $access = $accessRepository->findOneBy(array(
                'use' => $user
            ));
          if ($access && $access->coordinatorResponsible)
          		return true;
            
          return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function getFixedCoordinators(){
    	try {
    		$sql="SELECT distinct u.use_id, u.rol_id, u.name,u.email, u.phone_number, u.institution, u.function_name, u.last_access, u.active, u.requisition_tool ".
    				 "FROM user u where u.rol_id = ".$this->fixedCoordinatorId.";";
    		 
    		$rsm=new ResultSetMapping();
    		$rsm->addEntityResult('Storage\Entity\User', 'u');
    		$rsm->addFieldResult('u','use_id','useId');
    		$rsm->addMetaResult("u", "rol_id", "rol_id");
    		$rsm->addFieldResult('u','name','name');
    		$rsm->addFieldResult('u','email','email');
    		$rsm->addFieldResult('u','phone_number','phoneNumber');
    		$rsm->addFieldResult('u','institution','institution');
    		$rsm->addFieldResult('u','function_name','functionName');
    		$rsm->addFieldResult('u','last_access','lastAccess');
    		$rsm->addFieldResult('u','active','active');
    		$rsm->addFieldResult('u','requisition_tool','requisitionTool');
    		 
    		$query = $this->em->createNativeQuery($sql, $rsm);
    		$users=$query->getResult();
    		$this->em->clear();
    		return $users;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function getDynamicCoordinator($serviceLocator, $project){
    	$acl = $serviceLocator->get ( 'Admin\Permissions\Acl' );
    	$dynamicCoordUser=null;
    	try{
    		$coordinators = $this->getUseByProject($project, 1);
    		if($coordinators){
    			foreach($coordinators as $coordinator){
    				if($acl->isAllowed ( $coordinator->rol->name, "Área de trabalho", "Aceitar/recusar requisições" )) {
    					$dynamicCoordUser = $coordinator;
    					break;
    				}
    			}
    		}
    		return $dynamicCoordUser;
    	} catch (\Exception $e) {
    		return null;
    	}
    }
    
    public function getCoordinators($serviceLocator, $prj){
    	$acl = $serviceLocator->get ( 'Admin\Permissions\Acl' );
    	try{
    		$coords = null;
	    	$coordinators = $this->getUseByProject($prj, 1);
	    	if($coordinators){
		    	foreach($coordinators as $coordinator){
		    		if($acl->isAllowed ( $coordinator->rol->name, "Área de trabalho", "Aceitar/recusar requisições" ))
		    			$dynamicCoordUser = $coordinator;
		    	}
		    	$coords = $this->getFixedCoordinators();
		    	if(count($coords)>0)
		    		array_push($coords, $dynamicCoordUser);
	    	}
    		return $coords;
    	} catch (\Exception $e) {
    		return null;
    	}
    }
    
    public function isFixedCoordinator($user){

    	if(!isset($user) || !($user instanceof \Storage\Entity\User) ) return false;
    	
    	if($user->rol->rolId == $this->fixedCoordinatorId)
			return true;
    		
   		return false;
    }
    
    public function canVote($serviceLocator, $user, $prj){
    	try{
	    	$coordinators = $this->getCoordinators($serviceLocator, $prj);
	    	if($coordinators){
	    		foreach($coordinators as $coord){
		    		if($coord->useId == $user->useId)
	    				return true;
	    		}
	    	}
	    	return false;
    	} catch (\Exception $e) {
    		return false;
    	}
    }
    public function getByProject($prj,$coord=NULL) {
    	try {
    		$sql="select ac.* from access ac, user us ".
    				"WHERE ac.use_id=us.use_id AND ac.prj_id != 8 AND ac.prj_id = ".$prj->prjId." ";
    		if($coord){
    			$sql.="AND ac.coordinator_responsible = ".$coord." ";
    		}
    		$rsm=new ResultSetMapping();
    		$rsm->addEntityResult('Storage\Entity\Access', 'ac');
    		$rsm->addFieldResult('ac','acc_id','accId');
    		$rsm->addMetaResult("ac", "prj_id", "prj_id");
    		$rsm->addMetaResult("ac", "use_id", "use_id");
    		$rsm->addFieldResult('ac','coordinator_responsible','coordinatorResponsible');
    		$query = $this->em->createNativeQuery($sql, $rsm);
    		$access=$query->getResult();
    		$this->em->clear();
    		return $access;
    	}catch (\Exception $e){
    		return null;
    	}
    }
    
    public function updateCoordinatorResponsible($acc_id, $responsible) {
    	$sql="UPDATE access set coordinator_responsible=".$responsible." WHERE acc_id=".$acc_id;
    
    	$conn=$this->em->getConnection();
    
    	try {
    		$resultExec = $conn->exec($sql);
   			return true;
    	}catch (\Doctrine\DBAL\DBALException $dbalExc){
    		return false;
    	}catch (\Exception $e){
    		return false;
    	}
    }
    
    public function getInpeCoordinator($serviceLocator, $project) {
    	$acl = $serviceLocator->get ( 'Admin\Permissions\Acl' );
    	$coordinators = $this->getByProject ( $project, 1 );
    	foreach ( $coordinators as $coordinator ) {
    		if ($acl->isAllowed ( $coordinator->use->rol->name, "Área de trabalho", "Aceitar/recusar requisições" ))
    			return $coordinator;
    	}
    }
    public function getFuncateCoordinator($serviceLocator, $project) {
    	$acl = $serviceLocator->get ( 'Admin\Permissions\Acl' );
    	$coordinators = $this->getByProject ( $project, 1 );
    	foreach ( $coordinators as $coordinator ) {
    		if ($acl->isAllowed ( $coordinator->use->rol->name, "Área de trabalho", "Alocar requisições" ))
    			return $coordinator;
    	}
    }
}