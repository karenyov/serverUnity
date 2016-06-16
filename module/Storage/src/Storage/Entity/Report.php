<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity
 */
class Report
{
    /**
     * @var integer
     *
     * @ORM\Column(name="report_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reportId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=255, nullable=false)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="disk_location", type="string", length=255, nullable=true)
     */
    private $diskLocation;
    

    public function __set($name, $value) {
    	$this->$name = $value;
    }
    public function __get($name) {
    	return $this->$name;
    }
}