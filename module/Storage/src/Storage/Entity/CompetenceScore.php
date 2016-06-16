<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Db\Sql\Ddl\Column\Integer;

/**
 * User
 *
 * @ORM\Table(name="competence_score")
 * @ORM\Entity(repositoryClass="Storage\Entity\CompetenceScoreRepository")
 */
class CompetenceScore {
	/**
	 *
	 * @var integer 
	 * 		@ORM\Column(name="competence_score_id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $competenceScoreId;
	
	/**
	 *
	 * @var \Storage\Entity\User 
	 * 		@ORM\ManyToOne(targetEntity="Storage\Entity\User")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
	 *      })
	 */
	private $userId;
	
	/**
	 *
	 * @var \Storage\Entity\Competence 
	 * 		@ORM\ManyToOne(targetEntity="Storage\Entity\Competence")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="competence_id", referencedColumnName="competence_id")
	 *      })
	 */
	private $competenceId;
	
	/**
	 *
	 * @var float @ORM\Column(name="score", type="float", nullable=false)
	 */
	private $score;                            
	public function __set($name, $value) {
		$this->$name = $value;
	}
	public function __get($name) {
		return $this->$name;
	}
}
