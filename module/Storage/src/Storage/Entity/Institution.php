<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="institution")
 * @ORM\Entity(repositoryClass="Storage\Entity\InstitutionRepository")
 */
class Institution {
	/**
	 *
	 * @var integer @ORM\Column(name="institution_id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $institutionId;
	
	/**
	 *
	 * @var integer
	 * 
	 * 		@ORM\Column(name="institution_number", type="integer", nullable=false)
	 */
	private $number;
	
	/**
	 *
	 * @var string
	 * 
	 * 		@ORM\Column(name="institution_desc", type="string", length=255, nullable=false)
	 */
	private $institutionDesc;
	
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	public function __get($name) {
		return $this->$name;
	}
}
