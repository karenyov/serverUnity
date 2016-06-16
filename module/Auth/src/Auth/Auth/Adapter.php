<?php
namespace Auth\Auth;

use Zend\Authentication\Adapter\AdapterInterface, Zend\Authentication\Result;
use Storage\Entity\User;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Role;
use Zend\Mail\Storage;

class Adapter implements AdapterInterface {
    /**
     *
     * @var EntityManager
     */
    protected $em;
    protected $username;
    protected $password;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
   
   public function authenticate() {
        $repository = $this->em->getRepository("Storage\Entity\User");
        $user = $repository->findByEmailAndPassword($this->getUsername(),$this->getPassword());
        if($user) {
        	$auth_user=new User();
        	$auth_user->email=$user->email;
        	$auth_user->name=$user->name;
        	$auth_user->institution=$user->institution;
        	$auth_user->ra=$user->ra;
        	$auth_user->gender=$user->gender;
        	$auth_user->progress=$user->progress;
        	$auth_user->money=$user->money;
        	$auth_user->scene=$user->scene;
        	$auth_user->time=$user->time;
        	$auth_user->resetToken=$user->resetToken;
        	$auth_user->password=$user->password;
        
        	
            return new Result(Result::SUCCESS, array('user'=>$auth_user), array('OK'));
        }
        else
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());
    }
}