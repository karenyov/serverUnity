<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="Storage\Entity\AnswerRepository")
 */
class Answer {
	/**
	 *
	 * @var integer @ORM\Column(name="answer_id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $answerId;
	
	/**
	 *
	 * @var \Storage\Entity\Question 
	 * 
	 * 		@ORM\ManyToOne(targetEntity="Storage\Entity\Question")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="question_id", referencedColumnName="question_id")
	 *      })
	 */
	private $questionId;
	
	/**
	 *
	 * @var \Storage\Entity\Competence
	 * 
	 * 		@ORM\ManyToOne(targetEntity="Storage\Entity\Competence")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="competence_id", referencedColumnName="competence_id")
	 *      })
	 */
	private $competenceId;
	
	/**
	 *
	 * @var float 
	 * 
	 * @ORM\Column(name="answer_score", type="float", nullable=false)
	 */
	private $score;
	
	/**
	 *
	 * @var string @ORM\Column(name="answer_desc", type="string", length=255, nullable=false)
	 */
	private $answerDesc;
	
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
