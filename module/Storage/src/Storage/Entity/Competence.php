<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="competence")
 * @ORM\Entity(repositoryClass="Storage\Entity\CompetenceRepository")
 */
class Competence {
	/**
	 *
	 * @var integer @ORM\Column(name="competence_id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $competenceId;
	
	/**
	 *
	 * @var string @ORM\Column(name="competence_desc", type="string", length=255, nullable=false)
	 */
	private $description;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	public function __get($name) {
		return $this->$name;
	}
}
