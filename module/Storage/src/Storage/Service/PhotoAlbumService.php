<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Storage\Entity\PhotoAlbum;
use Storage\Entity\PhotoAlbumRepository;
use Storage\Entity\Project;
use Doctrine\Common;

class PhotoAlbumService extends AbstractService {

    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->entity = "Storage\Entity\PhotoAlbum";
    }
    
    public function listByPrj($aPrj){
        try {
            $repository=$this->em->getRepository($this->entity);
            $criteria=array('prj'=>$aPrj);
        	$orderBy=array('name'=>'ASC');
        	$albuns=$repository->findBy($criteria, $orderBy);
        	if(is_array($albuns) && count($albuns)>0)
    			return $albuns;
        	else
        		return false;
    	}catch (\Exception $e){
    	    return false;
    	}
    }
    
    public function rename($newName,$id) {
        try {
        	if(!isset($id)) return false;
        	
        	$data=$this->em->find($this->entity, $id);
        	if(!$data) return false;
        	$data->name=$newName;
    
        	return $this->update(array(0=>$data,'id'=>$data->phaId));
    	}catch (\Exception $e){
    	    return null;
    	}
    }
    
    public function remove($id) {
        try {
    	   if(!isset($id)) return false;
    	   return $this->delete($id);
    	}catch (\Exception $e){
    	    return null;
    	}
    }
    
    public function getById($id) {
        try{
            $repository=$this->em->getRepository($this->entity);
            $criteria=array("phaId"=>$id);
            $orderBy=null;
            $photoAlbum=$repository->findOneBy($criteria,$orderBy);
            return $photoAlbum;
        }catch (\Exception $e){
            return null;
        }
    }
    
    public function listAlbumByPrj($prj){
        try {
        	$photoAlbumRepository=$this->em->getRepository($this->entity);
        	$criteria=array("prj"=>$prj);
        	$orderBy=array("phaId"=>"DESC");
        	$photoAlbuns = $photoAlbumRepository->findBy($criteria, $orderBy);
        	return $photoAlbuns;
    	}catch (\Exception $e){
    	    return null;
    	}
    }
}