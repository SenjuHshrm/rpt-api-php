<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;
use App\Http\Controllers\ChekRequestAuth;

class GetTaxDec extends Controller
{
    public function getFile(Request $req) {
			$header = $req->header('Authorization');
			$token = new CheckRequestAuth();
			if($token->testToken($header)) {
				return json_encode([ 'res' => $this->makeFile($req) ]);
			} else {
				return json_encode([ 'res' => false ]);
			}
    }

		private function makeFile(Request $req) {
			$pdf = new TCPDI(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetTitle('TD_' . $req['pin'] . '_' . $req['diag_date_printed'] . '.pdf');
			$pdf->SetDisplayMode(100);
			$count = $pdf->setSourceFile(base_path().'\resources\assets\pdf\tax_declaration_template.pdf');
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$tpl = $pdf->importPage(1);
			$size = $pdf->getTemplateSize($tpl);
			$orn = $size['h'] > $size['w'] ? 'P' : 'L';
			$pdf->addPage($orn);
			$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
			$pdf->SetFont('helvetica', '', 8);
			$pdf->Text(27, 13, $req['reference_number']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(30, 27, $req['td_no']);
			$pdf->Text(142, 27, $req['pin']);
			if(strlen($req['owner_names']) > 100 && strlen($req['owner_names']) < 199) {
				$pdf->SetFont('helvetica', '', 5);
			} else if(strlen($req['owner_names']) > 199) {
				$pdf->SetFont('helvetica', '', 4);
			}
			$pdf->writeHTMLCell(105, 9, 28, 32, '<span>'.$req['owner_names'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(55, 9, 146, 32, '<span>'.$req['owner_tins'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(72, 6, 136, 46, '<span>'.$req['owner_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->SetFont('helvetica', '', 10);
			if(strlen($req['admin_names']) > 100 && strlen($req['admin_names']) < 199) {
				$pdf->SetFont('helvetica', '', 5);
			} else if(strlen($req['admin_names']) > 199) {
				$pdf->SetFont('helvetica', '', 4);
			}
			$pdf->writeHTMLCell(118, 6, 14, 56, '<span>'.$req['admin_names'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(55, 6, 146, 56, '<span>'.$req['admin_tins'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(72, 6, 136, 66, '<span>'.$req['admin_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(30, 42, $req['owner_addresses']);
			$pdf->Text(30, 63, $req['admin_addresses']);
			$pdf->Text(50, 73, $req['street_no']);
			$pdf->Text(112, 73, $req['brgy_district']);
			$pdf->Text(48, 84, $req['oct_tct_no']);
			$pdf->Text(128, 84, $req['survey_no']);
			$pdf->Text(27, 89, $req['condo_cert']);
			$pdf->Text(128, 89, $req['lot_no']);
			$pdf->Text(27, 94, $req['dated']);
			$pdf->Text(128, 94, $req['block_no']);
			$pdf->Text(40, 103, $req['north']);
			$pdf->Text(122, 103, $req['south']);
			$pdf->Text(40, 108, $req['east']);
			$pdf->Text(121, 108, $req['west']);
			$pdf->SetFont('helvetica', 'B', 12);
			$pdf->Text(16, 120, $req['s1']);
			$pdf->Text(59, 120, $req['s2']);
			$pdf->Text(132, 120, $req['s3']);
			$pdf->Text(16, 136, $req['s4']);
			$pdf->Text(32, 191, $req['tax']);
			$pdf->Text(60, 191, $req['exp']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(82, 127, $req['no_of_storey']);
			$pdf->Text(82, 132, $req['desc_bldg']);
			$pdf->Text(155, 127, $req['desc_mchn']);
			$pdf->Text(26, 143, $req['others_specify']);
			$pdf->SetFont('helvetica', '', 9);
			$pdf->Text(17, 156, $req['class']);
			$pdf->Text(45, 156, $req['area'] . ' sqm');
			$pdf->Text(75, 156, $req['market_val']);
			$pdf->Text(105, 156, $req['actual_use']);
			$pdf->Text(142, 156, $req['assess_level']);
			$pdf->Text(175, 156, $req['assessed_val']);
			$pdf->Text(75, 172, $req['total_market_val']);
			$pdf->Text(175, 172, $req['total_assessed_val']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(45, 181, $req['total_assessed_value_in_words'] . ' Only');
			$pdf->Text(135, 193, $req['pa_effectivity_assess_quarter']);
			$pdf->Text(170, 193, $req['pa_effectivity_assess_year']);
			$pdf->Text(45, 205, $req['approved_by1']);
			$pdf->Text(109, 205, $req['approved_by2']);
			$pdf->Text(170, 205, $req['approved_by_date']);
			$pdf->SetFont('times', 'I', 9);
			$pdf->Text(63, 209, $req['approver_title1']);
			$pdf->Text(125, 209, $req['approver_title2']);
			$pdf->SetFont('helvetica', '', 9);
			$pdf->Text(55, 219, $req['previous_td_no']);
			$pdf->Text(95, 219, $req['previous_owner']);
			$pdf->Text(178, 219, $req['previous_assessed_value']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(37, 227, $req['memoranda']);
			$pdf->SetFont('helvetica', '', 6);
			$pdf->StartTransform();
			$pdf->Rotate(90);
			$pdf->Text(200, 31, 'Date Printed: ' . $req['diag_date_printed']);
			$pdf->Text(200, 34, 'Printed By: ' . $req['diag_printed_by']);
			$pdf->StopTransform();
			return $pdf->Output('TD_' . $req['pin'] . '_' . $req['diag_date_printed'] . '.pdf', 'E');
		}
}
