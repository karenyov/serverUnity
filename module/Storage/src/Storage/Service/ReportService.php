<?php

namespace Storage\Service;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Configurator;
use Doctrine\ORM\Query\ResultSetMapping;

class ReportService extends AbstractService {
	public function __construct(EntityManager $em) {
		parent::__construct ( $em );
		$this->entity = "Storage\Entity\Report";
	}
	public function add($report) {
		try {
			$this->em->persist($report);
			$this->em->flush();
			return $report;
		} catch ( \Exception $e ) {
			return null;
		}
	}
	public function getById($id) {
		try {
			$reportRepository = $this->em->getRepository ( $this->entity );
			$report = $reportRepository->findOneBy ( array (
					'reportId' => $id 
			) );
			return $report;
		} catch ( \Exception $e ) {
			return null;
		}
	}
	
	public function getByUser($name) {
		try {
			$reportRepository = $this->em->getRepository ( $this->entity );
			$report = $reportRepository->findOneBy ( array (
					'userName' => $name
			) );
			return $report;
		} catch ( \Exception $e ) {
			return null;
		}
	}
	

	
	public function rename($newName, $reportId){
		try{
			$report = $this->em->getReference ( $this->entity, $reportId );
			
			$report->fileName = $newName;
			$this->em->persist ( $report );
			$this->em->flush ();
			
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
