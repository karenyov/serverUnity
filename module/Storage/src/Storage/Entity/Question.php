<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="Storage\Entity\QuestionRepository")
 */
class Question {
	/**
	 *
	 * @var integer @ORM\Column(name="question_id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $questionId;
	
	/**
	 *
	 * @var string @ORM\Column(name="question_desc", type="string", length=255, nullable=false)
	 */
	private $description;
	
	/**
	 *
	 * @var integer @ORM\Column(name="sequence", type="integer")
	 */
	private $sequence;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	public function __get($name) {
		return $this->$name;
	}
}
