<?php

namespace Admin\Controller;

define ( 'MPDF_PATH', 'vendor/mpdf/mpdf/' );
include (MPDF_PATH . 'mpdf.php');

use Main\Controller\MainController;
use Storage\Entity\Report;
use Storage\Entity\User;
use Zend\Session\Container;

class ReportController extends MainController {
	private $pdf;
	public function generateReportAction() {
		try {
					$response = $this->getResponse();
					$request = $this->getRequest ();
					$formData = $this->getFormData ();
					$serviceLocator = $this->getServiceLocator ();
					$reportService = $serviceLocator->get ( 'Storage\Service\ReportService' );
					$institutionService = $serviceLocator->get ( 'Storage\Service\InstitutionService' );
					$userService = $serviceLocator->get ( 'Storage\Service\UserService' );
					$compScoreService = $serviceLocator->get ( 'Storage\Service\CompetenceScoreService' );
					$emailService = $serviceLocator->get ( 'Storage\Service\EmailService' );
					
					$mail = $formData['email'];
						
					$user = $userService->getByEmail($mail);
					
						$fileDir = dirname(dirname ( dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) )) . '/public/reports';
						$date = new \DateTime ( "now", new \DateTimeZone ( "America/Sao_Paulo" ) );
						$report = new Report ();
						$report->creationDate = $date;
						$report->userName = $user->name;
						$report->fileName = "";
						$report->diskLocation = $fileDir;
						$complete = '/public/reports';
						$reportsDir = dirname(dirname ( dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) )) .$complete;
						if(!is_dir($reportsDir)){
							if (!mkdir($reportsDir)){
								$response->setContent ( \Zend\Json\Json::encode ( array (
										'status' => false,
										'msg' => 'Pasta reports nÃ£o existe',
										'isLogged' => true,
										'permitted' => true
								) ) );
								return $response;
							}
						}
						if (! is_dir ( $fileDir )){
							if(!mkdir ( $fileDir )){
								$response->setContent ( \Zend\Json\Json::encode ( array (
										'status' => false,
										'msg' => 'NÃ£o foi possÃ­vel gerar o relatÃ³rio',
										'isLogged' => true,
										'permitted' => true
								) ) );
								return $response;
							}		
						}
						
						$reportService->begin();
						
						$r = $reportService->add ( $report );
						
						$file = $r->diskLocation . '/' . $r->reportId . ".pdf";
						
						$information = array();
						
						$information['name'] = $user->name;
						if ($this->session->user->gender){
							$information['gender'] = "Masculino";
						}else{
							$information['gender'] = "Feminino";
						}
						//$institution = $institutionService->getById($this->session->user->institution->institutionId);
						$information['institution'] = $user->institution->institutionDesc;
						
						$scoresForCompetencies = $compScoreService->getByUser($user->useId);
						
						$cont = 0;
						$percentage = array();
						foreach ($scoresForCompetencies as $score){
							$information[$cont] = $score->score;
							if ($score->score != 0 && $score->competenceId->competenceId == '3'){
								$percentage[$cont] = round(($score->score/20)*100, 1);
							}
							else if ($score->score != 0 && $score->competenceId->competenceId == '4'){
								$percentage[$cont] = round(($score->score/12)*100, 1);
							}
							else if ($score->score != 0 ){
								$percentage[$cont] = round(($score->score/16)*100, 1);
							}else{
								$percentage[$cont] = 0;
							}
							
							$cont++;
						}
						
						$this->pdf = new \mPDF ( '', 'A4-L', 0, '', 15, 15, 16, 16, 5, 5, '' );
						$this->pdf->SetHeader ( $this->getHeader ( $r->reportId ) );
						$this->pdf->SetFooter ( $this->getFooter ( $r->creationDate ) . '<p align="right"> {PAGENO} de {nb} </p>' );
						$this->pdf->WriteHTML ( $this->getContent ($information, $percentage) );
						$this->pdf->Output ( $file, 'F' );
						
						$dirMail = dirname(dirname ( dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) )). '\public\reports';
						if (is_file ( $file )) {
							$reportService->commit();
							$emailService->send("karenyasmin37@gmail.com", $r, $dirMail);
							$response->setContent ( \Zend\Json\Json::encode ( array (
									'status' => true,
									'msg' => 'Relatório gerado com sucesso.',
									'reportId' => $r->reportId,
									'isLogged' => true,
									'permitted' => true
							) ) );
						} else {
							$reportService->rollback();
							$response->setContent ( \Zend\Json\Json::encode ( array (
									'status' => false,
									'msg' => 'Não foi possível gerar relatório.',
									'isLogged' => true,
									'permitted' => true
							) ) );
						}
						return $response;
							
		} catch ( \Exception $e ) {
			$response->setContent ( \Zend\Json\Json::encode ( array (
					'status' => false,
					'msg' => 'Não foi possível gerar relatório.',
					'isLogged' => true,
					'permitted' => true
			) ) );
			return $response;
		}
	}
	
	


	/*
	 * MÃ©todo para montar o CabeÃ§alho do relatÃ³rio em PDF
	 */
	protected function getHeader($reportCode) {
		$request = $this->getRequest ();
		$retorno = '<table width="1000">
			<thead>
				<tr>
					<td align="left"><img width="130" height="50" src="' . $request->getBasePath () . '/img/fatecsjc.png" /></td>
					<td align="center" style="font-size:20px;font-weight:bold">Relatório de competências</td>
					<td align="right"><img width="130" height="50" src="' . $request->getBasePath () . '/img/cps.png" /></td>
				</tr>
			</thead>
		</table>';
		return $retorno;
	}
	
	/*
	 * MÃ©todo para montar o RodapÃ© do relatÃ³rio em PDF
	 */
	protected function getFooter($date) {
		$request = $this->getRequest ();
		$retorno = '<table style="page-break-before: always">
					<tr>
						<td style="font-size:11px"><p>Relatório gerado em ' . $date->format ( 'd/m/Y' ) . ' </p></td>
					</tr>
	             	</table>';
		return $retorno;
	}
	/*
	 * MÃ©todo para construir a tabela em HTML com todos os dados
	 * Esse mÃ©todo tambÃ©m gera o conteÃºdo para o arquivo PDF
	 */
	private function getContent($information, $percentage) {
		$retorno = "<br /><br /></br>
				<div class='container-fluid'>
						<div class='row'>
							<div class='col-md-12'>
								</br>
								<h2 align='center'>Informações do aluno</h2>	
								<h4>
									<b>Nome:</b> ".$information['name']."
								</h4>
								<h4>
									<b>Instituição:</b> ".$information['institution']."
								</h4>
								<h4>
									<b>Genêro:</b> ".$information['gender']."
								</h4>
								</br>		
								<h2 align='center'>Resultado</h2>			
								<style type='text/css'>
							.tg  {border-collapse:collapse;border-spacing:0;border-color:#ccc;}
							.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;}
							.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#f0f0f0;}
							.tg .tg-ljhg{font-family:serif !important;;vertical-align:top}
							.tg .tg-yw4l{vertical-align:top}
							</style>
							<table class='tg' style='width:1000px;height:1000px'>
							  <tr>
							    <th class='tg-ljhg' align='center'><b>Competências</b></th>
							    <th class='tg-ljhg' align='center'><b>Pontuação</b></th>
							    <th class='tg-ljhg' align='center'><b>Porcentagem*</b></th>
							  </tr>
							  <tr>
							    <td class='tg-yw4l'align='center'>Comunicação</td>
							    <td class='tg-yw4l'align='center'>".$information['0'] ."</td>
							    <td class='tg-yw4l'align='center'>".$percentage['0'] ."%</td>
							  </tr>
							  <tr>
							    <td class='tg-yw4l'align='center'>Equilibrio Emocional</td>
							    <td class='tg-yw4l'align='center'>".$information['1']."</td>
							    <td class='tg-yw4l'align='center'>".$percentage['1'] ."%</td>
							  </tr>
							  <tr>
							    <td class='tg-yw4l'align='center'>Gestão de tempo</td>
							    <td class='tg-yw4l'align='center'>".$information['2']."</td>
							    <td class='tg-yw4l'align='center'>".$percentage['2'] ."%</td>
							  </tr>
							  <tr>
							    <td class='tg-yw4l'align='center'>Resiliencia</td>
							    <td class='tg-yw4l'align='center'>".$information['3']."</td>
							    <td class='tg-yw4l'align='center'>".$percentage['3'] ."%</td>
							  </tr>
							  <tr>
							    <td class='tg-yw4l'align='center'>Trabalho em equipe</td>
							    <td class='tg-yw4l'align='center'>".$information['4']."</td>
							    <td class='tg-yw4l'align='center'>".$percentage['4'] ."%</td>
							  </tr>
							  <tr>
							    <td class='tg-yw4l'align='center'>Visão de futuro</td>
							    <td class='tg-yw4l'align='center'>".$information['5']."</td>
							    <td class='tg-yw4l'align='center'>".$percentage['5'] ."%</td>
							  </tr>
							</table>			
								
								</div>
								<small>* Valores calculados com base na somatória dos pontos das competências.</small>
							</div>
						</div>
					</div>";
		return $retorno;
	}
	public function getStyle($file) {
		if (file_exists ( $file ))
			return file_get_contents ( $file );
	}
	
	public function viewReportAction() {
		$response = $this->getResponse();
		$request = $this->getRequest ();
		$formData = $this->getFormData ();
		$serviceLocator = $this->getServiceLocator ();
	
		$reportService = $serviceLocator->get ( 'Storage\Service\ReportService' );
		$report = $reportService->getByUser("Dai");
	
		return array('report' => $report);
	}
}