<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/
	if($_SESSION['_config']['language'] != "ja")
	{
		define('FPDF_FONTPATH','/usr/share/fpdf/font/');
		require_once('/usr/share/ufpdf/fpdf.php');
		class PDF2 extends FPDF
		{
		}
	} else {
		require('/usr/share/fpdf/japanese.php');
		class PDF2 extends PDF_Japanese
		{
		}
	}

	class PDF extends PDF2
	{
		function Header()
		{
			$this->Image((array_key_exists('bw',$_REQUEST) && $_REQUEST['bw'])?'images/CAcert-logo-mono-1000.png':'images/CAcert-logo-colour-1000.png',8,8,100);
			$this->SetFont('Arial','B',14);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',14);
			$this->Cell(100);
			$this->Cell(40,20,recode($_SESSION['_config']['recode'], _("CAcert Assurance Programme")));
			$this->Ln(6);
			$this->Cell(100);
			$this->Cell(40,20,recode($_SESSION['_config']['recode'], _("Identity Verification Form")));
			$this->Ln(10);

			$this->SetY(36);
			$this->SetFont('Arial','I',8);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','I',8);
			$this->Cell(0,0,'CAcert Inc. - PO Box 66 - Oatley NSW 2223 -  Australia - http://www.CAcert.org',0,0,'C');
			$this->Ln(3);
			$this->SetFont('Arial','',6);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',6);
			$this->Cell(0,0, recode($_SESSION['_config']['recode'], _("CAcert's Root Certificate fingerprints")).": A6:1B:37:5E:39:0D:9C:36:54:EE:BD:20:31:46:1F:6B "._("and")." 135C EC36 F49C B8E9 3B1A B270 CD80 8846 76CE 8F33",0,0,'C');
			$this->SetLineWidth(0.05);
			$this->Line(1, 43, $this->w - 1, 43);
			$this->SetLineWidth(0.2);
		}

		function Footer()
		{
		}

		function Body($name = "", $dob = "", $email = "", $assurer = "", $date = "", $maxpoints = "", $document1 = "", $document2 = "", $location = "")
		{
			if($date == "now")
				$date = date("Y-m-d");

			// Show text blurb at top of page
			$this->SetY(45);
			$this->SetFont('Arial','',10);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',10);
			$this->Write(4,sprintf(recode($_SESSION['_config']['recode'], _("To the Assurer: The CAcert Assurance Programme (CAP) aims to verify the identities of Internet users through face-to-face witnessing of government issued identity documents. The Applicant asks you to verify to CAcert.org that you have met them and verified their identity against one or more original, trusted, government photo identity documents. If you have ANY doubts or concerns about the Applicant's identity, DO NOT COMPLETE OR SIGN this form. For more information about the CAcert Assurance Programme, including detailed guides for CAcert Assurers, please visit: %s")), "http://www.CAcert.org"));
			$this->Ln(10);
			$this->Write(4,recode($_SESSION['_config']['recode'], _("As the assurer, you are required to keep the signed document on file for 7 years. Should Cacert Inc. have any concerns about a meeting taking place, Cacert Inc. can request proof, in the form of this signed document, to ensure the process is being followed correctly. After 7 years if you wish to dispose of this form it's preferred that you shred and burn it. You do not need to retain copies of ID at all.")));
			$this->Ln(10);
			$this->Write(4,recode($_SESSION['_config']['recode'], _("It's encouraged that you tear the top of this form off and give it to the person you are assuring as a reminder to sign up, and as a side benefit the tear off section also contains a method of offline verification of our fingerprints.")));

			// Assuree Section
			$top = 120;
			$this->Rect(11, $top, $this->w - 25, 60, "D");  //50 -> 60
			$this->SetXY(11, $top + 5);
			$this->SetFont("Arial", "BUI", "20");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','BUI',20);
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Applicant's Statement")));
			$this->Rect(13, $top + 10, $this->w - 29, 6, "D");
			$this->Line(80, $top + 10, 80, $top + 16);
			$this->SetXY(15, $top + 13);
			$this->SetFont("Arial", "B", "12");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',12);
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Names")).":");
			if($name)
			{
				$this->SetXY(82, $top + 13);
				$this->SetFont("Arial", '', "11");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',11);
				$this->Write(0, $name);
			}
			$this->Rect(13, $top + 16, $this->w - 29, 6, "D");
			$this->Line(80, $top + 16, 80, $top + 22);
			$this->SetXY(15, $top + 19);
			$this->SetFont("Arial", "B", "12");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',12);
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Date of Birth")).": ");
			$this->SetFont("Arial", "", "8");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',8);
			$this->Write(0, "(".recode($_SESSION['_config']['recode'], _("YYYY-MM-DD")).")");
			if($dob)
			{
				$this->SetXY(82, $top + 19);
				$this->SetFont("Arial", "", "11");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','',11);
				$this->Write(0, $dob);
			}
			$this->Rect(13, $top + 22, $this->w - 29, 6, "D");
			$this->Line(80, $top + 22, 80, $top + 28);
			$this->SetXY(15, $top + 25);
			$this->SetFont("Arial", "B", "12");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',12);
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Email Address")).":");
			if($email)
			{
				$this->SetXY(82, $top + 25);
				$this->SetFont("Arial", "", "11");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','',11);
				$this->Write(0, $email);
			}
			$this->SetXY(13, $top + 32);
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->MultiCell($this->w - 29, 3, recode($_SESSION['_config']['recode'], _("I hereby confirm that the information stated above is both true and correct, and request the CAcert Assurer (identified below) to verify me according to CAcert Assurance Policy.")));
// new da start
			$this->SetXY(13, $top + 42);
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->MultiCell($this->w - 29, 3, recode($_SESSION['_config']['recode'], _("I agree to the CAcert Community Agreement.")." ( http://www.cacert.org/policy/CAcertCommunityAgreement.html )"));
// new da end
			$this->SetXY(13, $top + 55); //45->55
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Applicant's signature")).": __________________________________");
			$this->SetXY(135, $top + 55);//45->55
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Date (YYYY-MM-DD)")).": ");
			if($date == "")
			{
				$this->Write(0, "20___-___-___");
			} else {
				$this->SetFont("Arial", "U", "10");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','U',10);
				$this->Write(0, str_pad($date, 13, " "));
			}

			// Assurer Section
			$top += 65; // 55->65
			$this->Rect(11, $top, $this->w - 25, 83, "D"); //63->93
			$this->SetXY(11, $top + 5);
			$this->SetFont("Arial", "BUI", "20");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','BUI',20);
			$this->Write(0, recode($_SESSION['_config']['recode'], _("CAcert Assurer")));
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 15);
			if($assurer)
			{
				$this->Write(0, recode($_SESSION['_config']['recode'], _("Assurer's Name")).": ");
				$this->SetFont("Arial", "", "10");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','U',10);
//				$this->MultiCell($this->w - 70, 2, recode($_SESSION['_config']['recode'], $assurer));
				$this->Write(0, str_pad($assurer, 50, " "));
			} else {
				$this->SetFont("Arial", "U", "10");
				$this->Write(0, recode($_SESSION['_config']['recode'], _("Assurer's Name")).": ________________________________________________________________");
			}
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 22);
			$this->MultiCell($this->w - 34, 3, recode($_SESSION['_config']['recode'], _("Photo ID Shown: (ID types, not numbers. eg Drivers license, Passport)")));
			$this->SetXY(13, $top + 30);
			if($document1 == "")
			{
				$this->Write(0, "1. __________________________________________________________________");
			} else {
				$this->Write(0, "1. ");
				$this->SetFont("Arial", "U", "10");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','U',10);
				$this->Write(0, str_pad($document1, 90, " "));
			}
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 35);
			if($document2 == "")
			{
				$this->Write(0, "2. __________________________________________________________________");
			} else {
				$this->Write(0, "2. ");
				$this->SetFont("Arial", "U", "10");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','U',10);
				$this->Write(0, str_pad($document2, 90, " "));
			}
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 45);
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Location of Face-to-face Meeting")).": ");
			if($location == "")
			{
				$this->Write(0, "_____________________________________________");
			} else {
				$this->SetFont("Arial", "U", "10");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','U',10);
				$this->Write(0, str_pad($location, 70, " "));
			}
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 50);
			if($maxpoints > 0)
			{
				$this->Write(0, recode($_SESSION['_config']['recode'], _("Maximum Points")).": ".$maxpoints);
			} else {
				$this->Write(0, recode($_SESSION['_config']['recode'], _("Points Allocated")).": ______________");
			}
			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 54);
			$this->MultiCell($this->w - 33, 3, recode($_SESSION['_config']['recode'], _("I, the Assurer, hereby confirm that I have verified the Member according to CAcert Assurance Policy.")));
			$this->SetXY(13, $top + 59);
			$this->MultiCell($this->w - 33, 3, recode($_SESSION['_config']['recode'], _("I am a CAcert Community Member, have passed the Assurance Challenge, and have been assured with at least 100 Assurance Points.")));

			$this->SetFont("Arial", "", "9");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',9);
			$this->SetXY(13, $top + 74);  //22->67
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Assurer's signature")).": __________________________________");
			$this->SetXY(135, $top + 74); //22->67
			$this->Write(0, recode($_SESSION['_config']['recode'], _("Date (YYYY-MM-DD)")).": ");
			if($date == "")
			{
				$this->Write(0, "20___-___-___");
			} else {
				$this->SetFont("Arial", "U", "10");
				if($_SESSION['_config']['language'] == "ja")
					$this->SetFont('SJIS','U',10);
				$this->Write(0, str_pad($date, 13, " "));
			}

		}
	}

	$format = array_key_exists('format',$_REQUEST)?$_REQUEST['format']:"";
	if($format != "letter")
		$format = "A4";

	$maxpoints = array_key_exists('maxpoints',$_REQUEST)?intval($_GET['maxpoints']):0;
	if($maxpoints < 0)
		$maxpoints = 0;

	$pdf = new PDF('P', 'mm', $format);
	if($_SESSION['_config']['language'] == "ja")
		$pdf->AddSJISFont();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->Body(array_key_exists('name',$_REQUEST)?$_REQUEST['name']:"", array_key_exists('dob',$_REQUEST)?$_REQUEST['dob']:"", array_key_exists('email',$_REQUEST)?$_REQUEST['email']:"", array_key_exists('assurer',$_REQUEST)?$_REQUEST['assurer']:"", array_key_exists('date',$_REQUEST)?$_REQUEST['date']:"", $maxpoints, array_key_exists('document1',$_REQUEST)?$_REQUEST['document1']:"", array_key_exists('document2',$_REQUEST)?$_REQUEST['document2']:"", array_key_exists('location',$_REQUEST)?$_REQUEST['location']:"");
	header("Expires: ".gmdate("D, j M Y G:i:s \G\M\T", time()+10800));
	header("Content-Disposition: attachment; filename=cap.pdf");
	header("Cache-Control: public, max-age=10800");
	header("Pragma: cache");
	$pdf->output();
	exit;
?>
