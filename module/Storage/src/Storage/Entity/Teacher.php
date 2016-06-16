<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teacher
 *
 * @ORM\Table(name="teacher")
 * @ORM\Entity(repositoryClass="Storage\Entity\TeacherRepository")
 */
class Teacher {
	/**
	 *
	 * @var integer @ORM\Column(name="teacher_id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $teacherId;
	
	/**
	 *
	 * @var \Storage\Entity\Institution 
	 * 
	 * 		@ORM\ManyToOne(targetEntity="Storage\Entity\Institution")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="institution_id", referencedColumnName="institution_id")
	 *      })
	 */
	private $institutionId;
		
	/**
	 *
	 * @var string @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	private $name;
	public function __set($name, $value) {
		$this->$name = $value;
	}
	public function __get($name) {
		return $this->$name;
	}
}
