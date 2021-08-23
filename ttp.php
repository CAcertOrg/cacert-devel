<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2021  CAcert Inc.

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
		class PDF2 extends FPDF { }
	} else {
		require('/usr/share/fpdf/japanese.php');
		class PDF2 extends PDF_Japanese { }
	}

	class PDF extends PDF2
	{
		function MultiCellBlt($w,$h,$blt,$txt,$border=0,$align='J',$fill=0)
		{
			$blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;
			$bak_x = $this->x;
			$this->Cell($blt_width,$h,$blt,0,'',$fill);
			$this->MultiCell($w-$blt_width,$h,$txt,$border,$align,$fill);
			$this->x = $bak_x;
		}

		function Header()
		{
			$this->Image($_REQUEST['bw']?'images/CAcert-logo-mono-1000.png':'images/CAcert-logo-colour-1000.png',8,8,100);
			$this->SetFont('Arial','B',14);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',14);
			$this->Cell(100);
			$this->Cell(40,20,recode($_SESSION['_config']['recode'], _("Trusted Third Party")));
			$this->Ln(6);
			$this->Cell(100);
			$this->Cell(40,20,recode($_SESSION['_config']['recode'], _("Identity Verification Form")));
			$this->Ln(10);
		}

		function Footer()
		{
			$this->SetY(-10);
			$this->SetFont('Arial','I',8);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','I',8);
			$this->Cell(0,0,'CAcert Inc. - P.O. Box 4107 - Denistone East NSW 2112 - Australia - http://www.CAcert.org',0,0,'C');
			$this->SetY(-7);
			$this->SetFont('Arial','',6);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',6);
			$this->Cell(0,0, recode($_SESSION['_config']['recode'], _("CAcert's Root Certificate fingerprints")).": 07ED BD82 4A49 88CF EF42 15DA 20D4 8C2B 41D7 1529 D7C9 00F5 7092 6F27 7CC2 30C5 "._("and")." DDFC DA54 1E75 77AD DCA8 7E88 27A9 8A50 6032 52A5",0,0,'C');
		}

		function Body($name = "", $dob = "", $email = "", $date = "")
		{
			if($date == "now")
				$date = date("Y-m-d");

			// Show text blurb at top of page
			$this->SetY(40);
			$this->SetFont('Arial','',10);
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','',10);
			$this->Write(4, recode($_SESSION['_config']['recode'], _("The CAcert Trusted Third Party (TTP) Programme is designed to assure Internet user identities through personal verification of government issued identity documents.")));
			$this->Ln(7);
			$this->Write(4, recode($_SESSION['_config']['recode'], _("The Applicant asks you to certify to CAcert that you have met with the Applicant and verified the Full Name, Date of Birth, and ID Numbers of the Applicant against two separate original government issued photo-identity documents. Once the photocopies of the photo IDs have been verified by the TTP they must be signed by the TTP with the statement 'I certify that this copy is a true copy of the original document'.  The verified and signed photocopies of IDs are then to be included with the completed TTP forms and returned to CAcert Inc.")));
			$this->Ln(7);
			$this->Write(4, recode($_SESSION['_config']['recode'], _("Please complete and sign this form, and sign the photocopies of the IDs, to acknowledge that").":"));
			$this->Ln(7);
			$this->MultiCellBlt($this->w - 25, 5, "1", recode($_SESSION['_config']['recode'], _("You have viewed two of the Applicant's photo identity documents and you are convinced of their authenticity, and are convinced that the photos indeed depict the Applicant (allowed documents are government-issued documents with photos such as driver's license, passport, or others that are normally accepted as legal identification in your country; expired documents are allowed).")));
			$this->Ln(2);
			$this->MultiCellBlt($this->w - 25, 5, "2", recode($_SESSION['_config']['recode'], _("You have verified that the Full Name, Date of Birth, and ID Numbers on the identity documents matches those filled in the Applicant section below and in the photocopies provided.")));
			$this->Ln(4);
			$this->Write(4, sprintf(recode($_SESSION['_config']['recode'], _("If you have ANY doubts or concerns about the identity of the Applicant then please DO NOT COMPLETE AND SIGN this form. For more information about the Web of Trust, including detailed guides for Trusted Third Parties, please see: %s")), "http://www.CAcert.org"));
			$this->Ln(8);
			$this->Write(4, recode($_SESSION['_config']['recode'], _("PLEASE NOTE: You must get 2 fully completed TTP forms before sending anything to CAcert. Failure to do so will only cause your application to be delayed until all forms have been received by CAcert!")));

			// TTP Section
			$top = 160;
			$this->Rect(11, $top, $this->w - 25, 45, "D");
			$this->Line(11, $top + 6, $this->w - 14, $top + 6);
			$this->Line(11, $top + 12, 120, $top + 12);
			$this->Line(11, $top + 18, 120, $top + 18);
			$this->Line(11, $top + 24, 120, $top + 24);
			$this->Line(11, $top + 30, 120, $top + 30);
			$this->Line(11, $top + 36, $this->w - 14, $top + 36);
			$this->Line(120, $top + 6, 120, $top + 36);
			$this->SetXY(11, $top + 3);
			$this->SetFont("Arial", "BUI", "12");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','BUI',12);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Person Verifying Applicant's Identity")));
			$this->SetXY(11, $top + 9);
			$this->SetFont("Arial", "B", "8");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',8);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Names").":"));
			$this->SetXY(120, $top + 9);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Office Street Address").":"));
			$this->SetFont("Arial", "B", "6");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',6);
			$this->SetXY(11, $top + 14);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Profession (Please circle one)")).":");
			$this->SetXY(11, $top + 16);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Justice of the Peace, Public Notary, Lawyer, Accountant, or Bank Manager")));
			$this->SetXY(11, $top + 20);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Bar Association, CPA Number or Bank Name and Branch, JP/Notary Number")).":");
			$this->SetXY(11, $top + 22);
			$this->Write(0, recode($_SESSION['_config']['recode'], "("._("as applicable")."):"));
			$this->SetFont("Arial", "B", "8");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',8);
			$this->SetXY(11, $top + 27);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Office Phone")).":");
			$this->SetXY(11, $top + 33);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Email (if applicable)")).":");
			$this->SetXY(11, $top + 39);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Signature")).":");
			$this->SetXY(120, $top + 39);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Date")).": ");
			if($date)
				$this->Write(0, $date);

			// TTP Section
			$top += 50;
			$this->Rect(11, $top, $this->w - 25, 45, "D");
			$this->Line(11, $top + 6, $this->w - 14, $top + 6);
			$this->Line(11, $top + 12, $this->w - 14, $top + 12);
			$this->Line(11, $top + 18, $this->w - 14, $top + 18);
			$this->Line(11, $top + 24, $this->w - 14, $top + 24);
			$this->Line(11, $top + 30, $this->w - 14, $top + 30);
			$this->Line(11, $top + 36, $this->w - 14, $top + 36);
			$this->Line(120, $top + 6, 120, $top + 36);
			$this->SetXY(11, $top + 3);
			$this->SetFont("Arial", "BUI", "12");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','BUI',12);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Applicant Information")));
			$this->SetXY(11, $top + 9);
			$this->SetFont("Arial", "B", "8");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',8);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Full Name (as shown on ID)").":"));
			if($name)
			{
				$this->SetXY(120, $top + 9);
				$this->Write(0, $name);
			}
			$this->SetXY(11, $top + 15);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Main email (so we can find you)")).":");
			if($email)
			{
				$this->SetXY(120, $top + 15);
				$this->Write(0, $email);
			}
			$this->SetXY(11, $top + 21);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Date of Birth")).": ");
			$this->SetFont("Arial", "B", "6");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',6);
			$this->Write(0, "(".recode($_SESSION['_config']['recode'], _("YYYY-MM-DD")).")");
			$this->SetFont("Arial", "B", "8");
			if($_SESSION['_config']['language'] == "ja")
				$this->SetFont('SJIS','B',8);
			if($dob)
			{
				$this->SetXY(120, $top + 21);
				$this->Write(0, $dob);
			}
			$this->SetXY(11, $top + 27);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("First ID Number (driver's license, passport etc)")).":");
			$this->SetXY(11, $top + 33);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Second ID Number (driver's license, passport etc)")).":");
			$this->SetXY(11, $top + 39);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Signature")).":");
			$this->SetXY(120, $top + 39);
			$this->Write(0, recode($_SESSION['_config']['recode'],_("Date")).": ");
			if($date)
				$this->Write(0, $date);
		}	
	}

	$format = $_GET['format'];
	if($format != "letter")
		$format = "A4";

	$pdf = new PDF('P', 'mm', $format);
	if($_SESSION['_config']['language'] == "ja")
		$pdf->AddSJISFont();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->Body($_GET['name'], $_GET['dob'], $_GET['email'], $_GET['date']);
        header("Expires: ".gmdate("D, j M Y G:i:s \G\M\T", time()+10800));
	header("Content-Disposition: attachment; filename=ttp.pdf");
        header("Cache-Control: public, max-age=10800");
        header("Pragma: cache");
	$pdf->output();
	exit;
?>
