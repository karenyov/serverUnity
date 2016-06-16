<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation\Enum;
use Zend\Filter\Int;
use Zend\Db\Sql\Ddl\Column\Decimal;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Storage\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $useId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;
    
	/**
	 *
	 * @var \Storage\Entity\Institution 
	 * 
	 * 		@ORM\OneToOne(targetEntity="Storage\Entity\Institution", cascade={"persist"})
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="institution", referencedColumnName="institution_id")
	 *      })
	 */
    private $institution;
    
    /**
     *
     * @var \Storage\Entity\Role
     *
     * 		@ORM\OneToOne(targetEntity="Storage\Entity\Role", fetch="EAGER")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="rol_id", referencedColumnName="rol_id")
     *      })
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;


    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var bigint
     *
     * @ORM\Column(name="ra", type="bigint", nullable=false)
     */
    private $ra;

    /**
     * @var boolean
     *
     *0 = Feminimo
     *1 = Masculino
     *
     * @ORM\Column(name="gender", type="boolean", nullable=false)
     */
    private $gender;

    /**
     * @var decimal
     *
     * @ORM\Column(name="progress", type="decimal", nullable=true)
     */
    private $progress;
    
    /**
     * @var time
     *
     * @ORM\Column(name="time", type="time")
     */
    private $time;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="scene", type="integer")
     */
    private $scene;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="money", type="decimal")
     */
    private $money;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reset_token", type="string", length=255, nullable=true)
     */
    private $resetToken;

     /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function definePassword($password)
    {
       $this->password = $this->encryptPassword($password);
       return $this;
    }

    public function encryptPassword($password) {
       return sha1($password);
    }
    
    public function __set($name, $value) {
    	$this->$name = $value;
    }
    public function __get($name) {
    	return $this->$name;
    }
}
