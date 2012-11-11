<?php
/*
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

//   $Id: capnew.php,v 1.4 2012-01-24 14:26:05 root Exp $
define('REV', '$Revision: 1.4 $');

/*
**  Created from old cap.php 2003, which used the now obsoleted ftpdf package
**  First created: 12 July 2008
**  Last change: see Revision date
**  Reviews:
**     printed text by Ian Grigg and Teus Hagen (July 2008)
**     layout/design by Teus Hagen and Johan Vromans (July 2008)
**     coding by Teus Hagen and ...
**
**  Installation:
**     std PHP lib:
**         recode_string(), zcompress() (PHP-ZLIB) only used if present
**         zcompress (pdf compression) gives performance loss, disable it?
**     PDF generation package (tcpdf/tcpdf.php):
**         TCPDF package + define the TCPDF_DIR install directory (GPL)
**         tcpdf package is patched for text subtypes see tcpdf diff file.
**         Add free embedding zapfdingbat font
**         ttf2pt1 -F zapfdinbats.ttf -> zapfdingbats.utf metrics file
**         php -q makefont.php zapfsdingbats.ttf zapfdingbats.utf -> .php,.ctg.z,.z
**         install files: zapfdingbats.{php,z,ctg.z} in tcpdf/fonts dir
**     UTF8 package for unicode (utf8/native/core.php):
**         utf8_substr() only when package is found and needs to be used
**     transliteration (and abbreviation):
**         if full name has non-ascii char(s) try to use: utf8_to_ascii()
**         First from transtab.php package which is Markus Kuhn compliant
**           transtab.php is CAcert php package.
**           Transtab depends on on its turn on UTF8 package.
**         Secondly if not found utf8ascii lib tried (artistic license)
**           http://sourceforge.net/projects/phputf8
**           .../utf8_to_ascii-0.3.tar.gz
**           see UTF8_ASCII definition for location requirements
**         Thirdly: if not found transliteration feature is disabled.
**
**  policy documents for pdf inclusion:
**    	define CCA file (default policy)
**  LOGO: CAcert logo logos directory is LOGO
**
**  Functionality:
**    Test: use environment variable settings as parameters for
**     Assuree: names, dob, sign date, id types, email
**       if name gets too long font size is increased to get it on one line
**     Assurer: name, email, dob (mutual ass), sign date
**     Assurance: location, date of assurance
**     Form fields: javascript form fields with fields for printout and change
**     Printout: printed, and completed for final signatures
**     On transliteration and abbreviation of a name:
**        if shoes a std way show accepted conversion as pdf comment
**     Orientation: on landscape (dflt) print 2-up
**     PDF URL links are used to web, wiki, and faq for more info search 
**     Only on non-ascii chars in a name the utf8 routines are loaded
**     PDF reader has wiki info url's and easy email feedback
**  ENABLED:
**     included is the CCA generates 2 extra pages (needs work to limit vert spacing)
**
**  For other re-use of some routines:
**     abbreviate() abbreviate a name on std way
**     transliterate() provide name in translated format in std way
**
**  For tests:
**      environment settings (FORM, FORMAT, CCA, ...) define used test data
**      In test modus variable path_url from tcpdf package unset warnings
**      Set for operation modus TEST on false (or comment code out)
**
**  Future:
**      digitally sign form and process it via network
**
**  unicode and UTF-8 support:
**  php4/5 recode() is alias of recode_string() of PHP library
**         If not provided: should check every string is transcoded?
**         recode(), recode_string(0 is said to have too many (japanese) defeats
**         recode_string() is only used on GET[] input (html->utf-8),
**  UTF-8  use routines from http://www.sourceforge.net/projects/phputf8
**         which replaces php recode() package. 
**         on many places own utf-8 handling code exists and is loaded (tcpdf problem)
**  _() translation routine. The returned HTML string is translated to utf-8 string.
**  the GET() routines expects utf-8 code (see test defs) but might be changed
**         to use html entity conversion routine of PHP (5.2 has a problem...).
**
**  PDF compression zlib: (now disabled)
**      if PHP lib zcompress() is present, generated PDF is compressed
**
**  FONTS future use ? http://www.slovo.info/unifonts.htm? (not used now)
**         or Bitstream Cyberbit http://www.orwell.ru/download/cyberbit.zip
**         Latter font is no longer for free download
**         For now: FreeVeraSans is used now and embedded (std in TCPDF package)
**     ZapfDingbat font: some Open Source readers have bad font handling or
**         no zapfdingbat font. So one is embedded
**         To be generated with tooling in util directory.
**
**  TO DO, to CHECK and KNOWN PROBLEMS:
**     Japanse package (maybe not needed with TCPDF?)
**     CCA informal should be on one page (no CCA printed yet)
**     form field checks, print button (Java script)
**     data structs in Java script and globalize property settings
**     XML
**     timestamping, signatures and certificate usage
**     list of recipients, encrypt the document and send it off
**     On Acrobat 7.0: first form field call error (have work around)
**     eps problem with logo (no eps logo yet)
**     multi selection of ID types in form fields (value editable now)
**     ugly capital char use in intro to bold or italic lowercase
**     tables over page boundaries do not fully work yet
**
**  DEPENDENCIES:
**  This PDF GENERATION package relies on the PHP PDF generation
**  package of TCPDF source force project:
**     http://sourceforge.net/projects/tcpdf/ V 4.0.007 18th July 2008
**     The tcpdf software supports encryption, signatures, and form fields
**     TCPDF is using URF-8 code (good!)
**  The TRANSLITERATE code tables db (utf8ascii) is not compliant (!?) with
**     Markus Kuhn <http://www.cl.cam.ac.uk/~mgk25/> -- 2001-09-02
**     First is tried to use Markus his tables
**     For a test file with all chars see there (it is also in tcpdf package).
**  Both transliteration packages rely on UTF-8 code, only loaded when available and
**     when really needed.
**  PDF generation:  The alternative is the one from the std PHP library.
**
**  SECURITY:
**  PHP libs: packages seems to download files on the fly into local filesystem!!!
**
**  All sizes (in mm) is related to A5 base, so other page formats are scaled.
**
**  Parameters (API):
**     $_GET['date'] date of assurance and signature assuree
**     $_GET['name'] first full name assuree default empty for upward compatibility
**     $_GET['name1'] first full name assuree default empty equal to 'name' (new)
**     $_GET['name<%d>'] etc. %d = 1-9 (new)
**     $_GET['name1ID'] ID of first name (new)
**     $_GET['name<%d>ID'] ID of second name (new)
**     $_GET['name1Pnts'] assurance points for first name (new)
**     $_GET['name<%d>Pnts'] assurance points for second name (new)
**     $_GET['dob'] date of birth assuree default empty
**     $_GET['document1'] ID type assuree default empty (deprecated)
**     $_GET['document2'] second ID type assuree default empty (deprecated)
**     $_GET['email'] email address assuree default empty
**     $_GET['assurer'] full name assurer default empty
**     $_GET['assurerdob'] date of birth assurer default empty (new)
**     $_GET['assureremail'] email address assurer default empty (new)
**     $_GET['assurerdate'] date of signature assurer (new)
**     $_GET['assurancedate'] date of assurance (new)
**     $_GET['location'] location of assurance
**     $_GET['maxpoints'] max points allocated depends on assurer default 35
**     $_GET['nocca'] do not print CCA on back side (dflt: false)
**     $_GET['policy<%d>'] to include policy document(s) in pdf file %d = 1-9 (new)
**     $_GET['noform'] do not print form (dflt: true) (new)
**     $_GET['format'] paper format required A0-A6,Letter, Folio, B0-B6 (dflt A4)
**     $_GET['watermark'] watermark on the page
**     $_GET['orientation'] paper orientation default 'portrait'
**     $_SESSION['_config']['language'] for 'ja' japanese default != ja
**     $_SESSION['_config']['recode'] = 'format' recode() uses it: needed ?
**     recode() is aliased to php lib function recode_string()
**     $_REQUEST['bw'] if exists use black/white, default use colour
**
**  Output, package generates:
**     PDF display screen is scaled to 150% A5 size
**     PDF property fields have CAcert info
**     on non empty _GET strings, the package generates prefilled form fields.
**     PDF form field variables (Java Script):
**     Assuree
**        Assuree.Names[].Name   1..9
**        Assuree.Names[].TypeID 1..9 language dependant (passport, ID card, etc.)
**        Assuree.Names[].Points 1..9 points:empty, 0..max points (dflt max 35)
**        Assuree.Email
**        Assuree.DoB
**        Assuree.Date     date of signature
**     Assurer
**        Assurer.Name string may have Assurer email address
**        Assurer.Email
**        Assurer.DoB
**        Assurer.Date     date of signature
**     Assurance
**        Assurance.Location string may have date of meeting
**        Assurance.Date   date of assurance
**     Form Revision string is generated from RCS revision string.
**     More info on PDF fields:
**        http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf
**     
*/

// use next define if you test this code
define( 'TEST', true );
// INSTALLATION DIRS OF PACKAGES ==============================
// make sure packages are installed here
define('RT','./');
define('TCPDF_DIR', '/usr/share/tcpdf_php4');
define('UTF8',RT.'/utf8/native/core.php');
if( file_exists(RT.'/transtab.php') ) // wherever it is
    define('UTF8_ASCII', RT.'/transtab.php');
else
    define('UTF8_ASCII', RT.'/utf8_to_ascii/utf8_to_ascii.php'); // optional
// end operational special code defs

if( defined( 'TEST' ) ) {
    // ONLY FOR TEST PURPOSES =====================================
    /* test data */

    $_SESSION['_config']['recode'] = 'html..utf-8'; // ????
    if( isset($_SERVER['LANG']) )
        $_SESSION['_config']['language'] = $_SERVER['LANG'];

    if( array_key_exists('FORMAT',$_SERVER) AND $_SERVER['FORMAT'] )
        $_GET['format'] = $_SERVER['FORMAT'];
    else {
        //$_GET['format'] = 'A5'; // ok, default
        //$_GET['format'] = 'Legal'; // ok
        //$_GET['format'] = 'Folio'; // ok
        //$_GET['format'] = 'Letter'; // letter little margin problem
        //$_GET['format'] = 'A4'; // ok
    }
    if( array_key_exists('ORIENTATION',$_SERVER) AND $_SERVER['ORIENTATION'] )
        $_GET['orientation'] = $_SERVER['ORIENTATION'];
    else {
        //$_GET['orientation'] = 'portrait'; // default 2 pages, or portrait
    }
    $_GET['nocca'] = isset($_SERVER['CCA']) ? $_SERVER['CCA'] : '';
    //$_GET['policy1'] = 'policy/PolicyOnPolicy.php';
    if( isset($_SERVER['FORM']) AND $_SERVER['FORM'] == 'noform' )
        $_GET['noform'] = 'true';

    if( array_key_exists('FORM',$_SERVER) AND $_SERVER['FORM'] != 'empty' ){
        // assuree info part
        $_GET['date'] = '2008-11-04';
        $_GET['name'] = 'drs. T. Fabrice Ghuege-Denis'; // upwards competable
        $_GET['name1'] = $_GET['name'];        // will void 'name'
        $_GET['name1ID'] = 'rijbewijs';
        //$_GET['name1Pnts'] = '';
        $_GET['name2'] = 'drs. Teus F. Ghuege-Denis';    // second name
        //$_GET['name2ID'] = '';
        $_GET['name2Pnts'] = '10'; // seems not to work properly yet
        $_GET['name3'] = 'Tänўs Iŉtèrnątiœñalîsàtïǿn Ĝügé'; // third, show of javascript error
        $_GET['name3ID'] = 'paspoort';
        $_GET['name3Pnts'] = '12';
        $_GET['name8'] = 'This Should Be invisible';
        $_GET['dob'] = '1945-10-06';
        $_GET['document1'] = 'führerschein';
        $_GET['document2'] = 'paspoort';
        $_GET['email'] = 'tesu.hagaen@thesu.xs4all.eu';
        // assurer info
        $_GET['assurer'] = 'mr A. B. C. Äßurer';// one name only
        $_GET['assurerdob'] = '2010-12-32';    // for mutual assurance
        $_GET['assureremail'] = 'assurer.email@cacert.org'; // for mutual assurance
        // meeting info
        $_GET['location'] = 'Grubbenvorst, the Carabiën';
        $_GET['assurerdate'] = '';
        $_GET['assurancedate'] = '2008-12-31';
        $_GET['maxpoints'] = '20';
        $_GET['watermark'] = 'just an EXAMPLE';
    } else {
        //$_GET['ALL'] = 'empty';
    }

    //$_REQUEST[bw] = true;

} // end of TEST code ===========================================================

/* Directory settings for installation */
// change next for directory settings for packages !!!!!!!!!!!!!!!!!!!!!!
// set to correct internal path to TCPDF pakage installation
// Make sure pdf generation package is not connecting internet for
// whatever reason and downloading files into this host!!!!
// UCPDF as well PHP PDF std package have unsecure code as well....
require_once(TCPDF_DIR . '/config/lang/eng.php');
require_once(TCPDF_DIR . '/tcpdf.php');

// CAcert logo path/file name is extended with eg color, mono and format type
define( 'LOGO','logos/CAcert-logo-');
// eps should give better quality, LOGO_TYPE ->  .eps
// eps does not work with CAcert logo, set to .eps when ok
define( 'LOGO_DPI', '1000');
define( 'LOGO_TYPE','-'.LOGO_DPI.'.png');
// logo colors RGB hex
define('BLUE', '#11568C');  // RGB 17 86 140
define('LBLUE', '#ADC5D7'); // RGB 112 154 186
define('LLBLUE','#D6E2EB'); // lighhter blue RGB 173 197 215
define('LIME', '#C7FF00');  // RGB 199 255 0
define('GREEN', '#00BE00'); // 0 190 0

define('POBOX','CAcert Inc. - P.O. Box 4107 - Denistone East NSW 2112 - Australia');
define('WEB', 'http://www.cacert.org');
define('WIKI','http://wiki.cacert.org/wiki');
define('ROOTKEYS','http://www.cacert.org/index.php?id=3');
define('ASSCAP', WIKI.'/FAQ/AssuranceByCAP');
define('ASSHBK', WIKI.'/AssuranceHandbook');
define('ASSINFO', WIKI.'/FAQ/AssuranceInformationForCAP');
define('ASSINTRO', WIKI.'/FAQ/AssuranceIntroduction');
define('ASSORG', WIKI.'/OrganisationAssurance');
define('ARBIT', WIKI.'/ArbitrationForum');
// CAcert Community Agreement
define('CCA', 'CAcertCommunityAgreement'); // default policy to print
define('POLICY','policy/'); // default polciy doc directory
define('EXT','.php'); // default polciy doc extention, should be html
/* finger print CAcert Root Key */ // should obtain this automatically
define('CLASS1_SHA1','135C EC36 F49C B8E9 3B1A B270 CD80 8846 76CE 8F33');
define('CLASS3_SHA1','AD7C 3F64 FC44 39FE F4E9 0BE8 F47C 6CFA 8AAD FDCE');
// next two are not used on the form
define('CLASS1_MD5','A6:1B:37:5E:39:0D:9C:36:54:EE:BD:20:31:46:1F:6B');
define('CLASS3_MD5','F7:25:12:82:4E:67:B5:D0:8D:92:B7:7C:0B:86:7A:42');
// if on draft provide std message
define('WATERMARK','');

// other definitions for the form
define('MAX_COLS', 2); // max coulumns per page Landscape is printed with 2-up
// put next to 200 and it will disable printout
define('MINH', 98.5); // in A5 mm is current estimated left over space on one page
define('H', 5); // height of a name entry field
//set margins
define('MARGIN',11.296); // 2-up will be scaled
// base of font size
define( 'F_SIZE', 7 );

define('DFL_FORMAT', 'html..utf-8');

// enviroment dependent constants
// Japanese is not supported?
if( array_key_exists('_config', $_SESSION) ) {
    if( isset($_SESSION['_config']['language']) ) {
        if($_SESSION['_config']['language'] == 'ja')
            define('FONT','SJIS');
        else define( 'FONT', 'freesans');
    }
    else define( 'FONT', 'freesans');
}
else
    //define( 'FONT', 'dejavusans');
    define( 'FONT', 'freesans');

// generate black/white?
if(array_key_exists('bw',$_REQUEST))
    define('BW', true);
else
    define('BW', false);

// function is left in tact, but to new tcpdf code UFT-8 is fully supported now.
function my_recode($strg = NULL )
{
    static $format = NULL;
    if( $strg == NULL OR !$strg ) return ( '' );
    if( $format == NULL ) {
         if( array_key_exists('_config', $_SESSION) ) {
             if( isset( $_SESSION['_config']['recode'])  )
                 $format = $_SESSION['_config']['recode'];
             else $format = DFL_FORMAT;
         }
         else $format = DFL_FORMAT;
    }
    // newer tcpdf package is full UTF-8 Voided by this package?
    if( function_exists('recode_string' ) )
        return ( recode_string($format, $strg) );
    else return( $strg );
}

// return TRUE if string is ascii and not device control chars specialized for
// personal names (no device controls)
function utf8_is_ascii_ctrl($str) {
    if ( strlen($str) > 0 ) {
        // Search for any bytes which are outside the ASCII range,
        // or are device control codes
        //return (preg_match('/[^\x09\x0A\x0D\x20-\x7E]/',$str) !== 1); deleted \r and \n
        return (preg_match('/[^\x09\x20-\x7E]/',$str) !== 1);
    }
    return FALSE;
}


// extend TCPF with custom functions
class CAPPDF extends TCPDF {

    // do cap form version numbering automatically '$Revision: 1.4 $'
    /*public*/ function Version() {
	strtok(REV, ' ');
        return(strtok(' '));
    }

    /*public*/ function myHeader( $msg = NULL, $url = NULL )
    {
	static $my_url = NULL;
	if( $msg != NULL ) {
	    $this->my_header_msg = $msg; $my_url = $url; return;
        }
	if( $this->my_header_msg == NULL ) return;
	if( $this->msg_page_nr > 0 ) {
	    $font_fam = $this->FontFamily;
	    $font_style = $this->FontStyle.($this->underline ? 'U' : '').($this->linethrough ? 'D' : '');
	    $font_size = $this->getFontSize();
            $this->SetFont(FONT,'', F_SIZE-1);
            $this->setXY($this->lMargin, MARGIN-3);
            $this->Cell($this->colwidth, 3,$this->my_header_msg, 0, 0, 'R');
	    if( !empty($font_fam ) )
		$this->SetFont($font_fam,$font_style,$font_size);
            if( $my_url != NULL AND $my_url != '' )
                $this->myLink($this->lMargin+$this->colwidth/2,$this->lMargin-4,$this->colwidth,(F_SIZE+5)/2.9,$my_url);
	}
        $this->setXY($this->lMargin, MARGIN+3);
	$this->y0 = $this->getY();
    }

    // undefine default header and footer handling
    // default routines do not handle columns
    function Footer() { }
    function Header() { }
    function Mark( $string = '' ) {
	return array( $string, 1+substr_count($string,'.') );
    }

    /*public*/ function myFooter( $msg = NULL, $url = NULL )
    {
	static $my_url = NULL;
	if( $msg != NULL ) {
	    $this->my_footer_msg = $msg; $this->msg_page_nr = 0;
	    $my_url = $url; return;
	}
        if( $this->my_footer_msg == NULL ) return;
        $this->InFooter = true;
	$this->msg_page_nr++;
	$font_fam = $this->FontFamily;
	$font_style = $this->FontStyle.($this->underline ? 'U' : '').($this->linethrough ? 'D' : '');
	$font_size = $this->getFontSize();
        $this->SetFont(FONT,'', F_SIZE-1);
        if( $this->msg_page_nr > 1 ) {
            $this->SetXY($this->lMargin, $this->GetPageHeight()/$this->scale*100.0-6);
            $this->Cell($this->colwidth, 3,
                    sprintf("%s %d", $this->unhtmlentities( _('page') ), $this->msg_page_nr),
                            0, 0, 'C');
        }
        if( $this->my_footer_msg != '' ) {
            $strg =  '© '. date('Y'). ' CAcert Inc.'.', '. $this->my_footer_msg;
            $this->SetXY($this->lMargin+MARGIN/2, $this->GetPageHeight()/$this->scale*100.0-6);
            $this->Cell($this->colwidth, 3, $strg, 0, 0, 'R');
            if( $my_url != NULL AND $my_url != '' )
                $this->myLink($this->lMargin+MARGIN/2,$this->GetPageHeight()/$this->scale*100.0-6,$this->colwidth,(F_SIZE+5)/2.9,$my_url);
	}
        if( $this->Watermark != '' ) {
            $this->StartTransform();
            $savex = $this->GetX(); $savey = $this->GetY();
            $this->SetFont(FONT,'', F_SIZE*7);
            $l = $this->GetStringWidth($this->Watermark);
            $h = $this->GetPageHeight()/$this->scale*100.0/2;
            $w = $this->colwidth/2+MARGIN;
            $this->SetXY(0,0);
            $this->TranslateY($h+(F_SIZE*7)/2.9);
            $this->TranslateX($w-MARGIN+$this->lMargin);
            $this->Rotate(rad2deg(atan($h/$w)));
            $this->Text(-$l/2,0,$this->Watermark, 0.8);
            $this->StopTransform();
            $this->SetXY($savex,$savey);
         }

	 if( !empty($font_fam ) )
	     $this->SetFont($font_fam,$font_style,$font_size);
        $this->InFooter = false;
    }

    // user and print preferences
    // NumCopies, PrintPageRange, DisplayDocTitle, HideMenuBar, HideToolBar, ...
    /*public*/ var $ViewerPrefs = array(
        'Duplex' => 'Simplex',
        'NumCopies'=> 2,
        'DisplayDocTitle' => 'CAcert Assurance Programme (CAP)',
        'HideToolBar' => true,
	'FitWindow' => true,
    );

    //number of colums
    /*protected*/ var $ncols=1;
    
    // columns width
    /*protected*/ var $colwidth=0;

    // space between columns
    /*protected*/ var $column_space = 0;
    
    //Current column
    /*protected*/ var $col=0;
    
    //Ordinate of column start
    /*protected*/ var $y0;

    // scaling factor
    /*protected*/ var $scale = 100.0;

    // print header and footer
    /*protected*/ var $my_footer_msg = NULL;
    /*protected*/ var $my_header_msg = NULL;
    /*protected*/ var $msg_page_nr = 0;

    // print short watermark on the page
    /*public*/ var $Watermark = WATERMARK;

    /*public*/ function SetFormat( $format = 'a5' ) {
        switch( strtolower($format) ) {
        // there is some scale problems with margins...
        case 'a1':
        case 'b1':
		$this->scale *= 1.4142;
        case 'a2':
        case 'b2':
		$this->scale *= 1.4142;
        case 'a3':
        case 'b3':
		$this->scale *= 1.4142; break;
        case 'a5':
        case 'b5':
		$this->scale /= 1.4142; break;
        case 'letter':
                $this->scale *= 0.97; break;
        default: $format = 'a4';
        case 'a4':
        case 'b4':
        case 'folio':
        case 'legal':
            break;
        }
        $this->SetDisplayMode(intval($this->scale), 'SinglePage', 'UseOC');
        return( $format );
    }
        
    //Set position at a given column
    /*private*/ function SetCol($col = -1) {
        static $pagecolwidth = 1.0;
        static $column_space = 1.0;

        $this->InFooter = true;
        if( $col == -1 ) $col = $this->col+1;
        if( $this->colwidth == 0 ) {
            // only once at start; set default values
            //set margins
            $this->addPage(); $col = 0; // reset to zero
            $this->SetMargins(MARGIN, MARGIN, MARGIN);
            if( $this->CurOrientation != 'L' ) {
                $this->scale *= 1.4142;
                $this->ScaleXY($this->scale,0,0);
            } else {
                $this->scale *= 1.0;
                $this->ScaleXY($this->scale,0,0);
            }
            $this->ncols = $this->CurOrientation == 'L'? MAX_COLS : 1;
            $this->colwidth = $this->w / $this->scale * 100 / $this->ncols - MARGIN*2;
            $pagecolwidth = $this->w/$this->ncols;
            // space between columns
            if ($this->ncols > 1) {
                $column_space = round((float)($this->w - ($this->ncols * $pagecolwidth)) / ($this->ncols - 1));
            } else {
                $column_space = 0;
            }
            $this->y0 = $this->GetY();
        }
        else {
	    if( $col == $this->col ) { // reset on close of this column
	        $x = MARGIN + $this->col*($pagecolwidth+$column_space);
                $this->SetLeftMargin($x);
                //$this->SetRightMargin($this->w - $x - $this->colwidth);
            }
            $this->PrintTable('', -1); // if pending table close up table
	    $this->myFooter(); // print footer msg if defined
        }
        if( $col >= $this->ncols ) {
                $this->addPage(); $col = 0; 
                $this->ScaleXY($this->scale,0,0);
                $this->y0 = 0;  //no header/footer done...
        } elseif ( $col > 0  AND $col < $this->ncols) {
                // print column separator
                $x = $this->w/$this->ncols*($this->col+1);
                $y = $this->tMargin;
                $this->SetLineWidth(0.1); $this->SetDrawColor(195);
                $this->SetLineStyle(array('dash'=>'1,8') ); // gray dotted
                $this->Line( $x, $y+27, $x, $y+185);
                $this->SetLineWidth(0.2); $this->SetDrawColor(0);
                $this->SetLineStyle(array('dash'=>'0') );
        }
        $this->col = $col;
        // X position of the current column
        $x = MARGIN + $col*($pagecolwidth+$column_space);
        $this->SetLeftMargin($x);
        $this->SetRightMargin($this->w - $x - $this->colwidth);
        $this->SetXY($x, $this->y0);
	$this->myHeader(); //print header msg if defined
        $this->PrintTable('', 0); // if in table reprint title table
        $this->InFooter = false;
    }
    
    //Method accepting or not automatic page break
    /*public*/ function AcceptPageBreak() {
       $this->SetCol();
       return false;
    }

    // redefine this routine from tcpdf.php due to scaling bug
    /*protected*/ function checkPageBreak($h) {
       if (((($this->y + $h)*$this->scale/100.0) > $this->PageBreakTrigger) ) {
           if ( !$this->InFooter ) {
               if ( ($this->AcceptPageBreak())) {
                   $rs = '';
                   //Automatic page break
                   $x = $this->x;
                   $ws = $this->ws;
                   if ($ws > 0) {
                       $this->ws = 0;
                       $rs .= '0 Tw';
                   }
                   $this->AddPage($this->CurOrientation);
                   if ($ws > 0) {
                       $this->ws = $ws;
                       $rs .= sprintf('%.3f Tw', $ws * $k);
                   }
                   $this->_out($rs);
                   $this->y = $this->tMargin;
                   $this->x = $x;
               }
            }
        }
    }

    /*private*/ function myLine( $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0 ) {
        if ( BW ) {
             $this->SetDrawColor(195);
        } else {
            $this->SetDrawColor(173,197,215);
        }
        $this->SetLineWidth(0.1); // small line in points collumn
        $this->Line($x1, $y1, $x2, $y2 );
        $this->SetLineWidth(0.2); $this->SetDrawColor(0);

    }

    /*private*/ function S( $value = 1.0 ) {
        return( $value * $this->scale / 100.0 );
    }

    // put Link in user space
    /*private*/ function myLink( $x, $y, $w, $h, $Lnk = NULL, $Type = array('SubType'=>'Link') ) {
        if( $Lnk == NULL ) return;
        if( $Lnk == '' ) $Lnk = WEB;
        $this->Annotation( $this->S($x), $this->S($y), $this->S($w), $this->S($h), $Lnk, $Type);
    }


    //require_once('../utf8/native/core.php');
    // only for to upper case //require_once('../utf8/utils/unicoe.php');

    //setlocale(LC_ALL, 'de_DE');
    // try to abbreviate a full name, returns name if abbreviation was/is done
    // has pointers to sur name, first name, avoids titles and extentions
    // is based that given names and family names starts with capital
    // all names between first given name and surname are secondary names
    // will use utf8 routines only when needed and available
    /*private*/ function Abbreviate( $name = '') {
        // need to change this for utf8 uppercase detection
        // substr and strtoupper arte dependent of setlocale...
        $substr = 'substr';
        $strtoupper = 'strtoupper';
        $tokens = array();
        $cnt = preg_match_all('/([^\s\.]+\.|[^\s\.]+)/', $name, $tokens, PREG_SET_ORDER);
        if( $cnt <= 0 ) return ( $name );
        $fam = -1; $married = 0;  $i = 0; $success = FALSE; $first_name = -1;
        for( $j = 0; $j < $cnt ; $j++ ) {
            $tk = $tokens[$j];
            $nm = $tk[0]; if( $nm == '' ) continue;
            // not utf8
            $ltr = $substr( $nm, 0, 1 );
            if(preg_match('/[^\x09\x20-\x7E]/',$ltr) !== 1  AND // it is utf8
              function_exists( 'utf8_substr') ) {
                $substr='utf8_substr';
                //$strtoupper = 'utf8_strtoupper'; // requires utf8/utils/unicode.php
            }
            if( $strtoupper($ltr) != $ltr ) continue; // lower case setlocale dependent
            elseif( preg_match('/\./', $nm ) ) {
                if( $first_name < 0 ) $first_name = $j;
                if( $first_name >= 0 ) $success = TRUE; // was abbreviated
                continue; // title 
            }
            if( $first_name < 0 ) $first_name = $j;
            if( $married == 0 ) $fam = $j;
            if( preg_match('/[-_]/', $nm ) ) {
                // find special markers
                if( $married == 0 ) $fam = $j;
                $married++;
            }
        }
        $name = '';
        for( $j = 0; $j < $cnt; $j++ ){
            $tk = $tokens[$j];
            if( !isset($tk[0]) ) continue;
            $nm = $tk[0]; if( $nm == '' ) continue;
            if( $name != '') $name .= ' ';
            $ltr = $substr( $nm, 0, 1 );
            if( $j == $fam ) $name .= $nm;
            elseif( $strtoupper($ltr) != $ltr )  $name .= $nm; // lower case
            elseif( preg_match('/\./', $nm ) ) $name .= $nm;
            elseif( $j < $fam ) { // need to abbreviate
                 // not utf8
                 // and abbreviate 
                if( $j == $first_name )
                    $abr = '('. $substr( $nm, 1 ) . ')';
                else $abr = '.';
                $name .= $ltr . $abr; $success = TRUE; // is abbreviated
            } else $name .= $nm;
        }
        $ext = -1; for( $j = $cnt-1; $j >= 0 AND $j >= $fam; $j-- ) {
            // try to find family names and see if there is abbreviation
            $tk = $tokens[$j];
            if( !isset($tk[0]) ) continue;
            $nm = $tk[0];
            if( $ext < 0 AND preg_match('/(^[^A-Z]|\.)/', $nm ) ) continue;
            if( $ext < 0 ) $ext = $j+1;
            if( preg_match('/\./', $nm ) ) {  $success = TRUE; break; } 
        }
        return( $success? $name : '' ); // and return abbriviated name
    }

    // set formfield coordinates
    // this routine is needed due to field ordinates are not scaled and in user space
    // to be called before form field call (or as width parameter)
    // and just after with true argument to restore X Y ordinates.
    /*private*/ function SetFieldXY( $x=NULL, $y=NULL, $w=0) {
        static $savex;
        static $savey;
        static $restored = true;
        $restoreXY =  $x == NULL ? true : false;

	if( $restored == $restoreXY )
            $this->Error("internal Form Field save/restore error\n");
        if( !$restoreXY ) {
	    /* save X Y ordinates */
            $savex = $this->GetX(); $savey = $this->GetY();
            // scale to user ordinates
            $this->SetY( $this->S($y));
            $this->SetX( $this->S($x));
        } else {
            /* restore X Y ordinates */
            $this->SetY( $savey); // different from SetXY()
            $this->SetX( $savex); // different from SetXY()
        }
        $restored = $restoreXY;
        return( $this->S($w) );
    }

    // print Date  on left or right side
    /*private*/ function PrintDate( $x=10, $y=10, $dstrg='teus', $dvalue='1945-10-6', $field = NULL , $RL = 'L')
    {
        static $TextProps = array('strokeColor' => LLBLUE, 'value' => '', 'fillColor' => LBLUE , 'textSize' => '11', 'charLimit'=> 10);
        // next statements will cause php to go into an infinite loop
        //if( $dstrg != NULL AND $dstrg == '')
        //     $TextProps['userName'] = $this->unhtmlentities( _("On mutual assurance provide Assurer date of birth (yyyy-mm-dd)") );
        //else
            $TextProps['userName'] = $this->unhtmlentities( _("yyyy-mm-dd") );

        $this->SetFont( FONT, '', F_SIZE);
        $this->SetXY($RL == 'L'? $x : $x-50, $y);
        $this->Cell(50, 3, $dstrg, 0, 0, $RL);
        if($dvalue) {
            $this->SetXY($RL == 'L'? $x :$x-50, $y+3.5);
            $this->SetFont(FONT, 'B', F_SIZE);
            $this->Cell(50, 3, $dvalue, 0 , 0, $RL);
        }
        if( $field == NULL ) return;
        $TextProps['value'] = $dvalue;
        $this->TextField($field, $this->SetFieldXY(($RL == 'L'? $x+1 : $x-17), ($y+3.5),17), 5, $TextProps );
        $this->SetFieldXY();
    }

    // Add import HTML text  eg from CCA
    /*private*/ function PrintHTML( $url = NULL ) {
            if( $url == NULL OR $url == '' ) return;
            $error = ''; $title = ''; $url = POLICY.$url.EXT;
	    if( ! file_exists($url) ) $url = WEB.'/'.$url;
            $data = file_get_contents($url);
            if( !$data ) $error = "\nInternal Error: no ".$url.' found.';
            else {
		$regs = array();
		preg_match('/<[Tt][Ii][Tt][Ll][Ee][^>]*>/', $data, $regs);
                if( count($regs) < 1 ) $error .= "\nInternal Error: no open tag title found on $url.";
		else {
		    $start = strpos($data, $regs[0]) + strlen($regs[0]);
		    $data = substr($data, $start);
		}
		$regs = array();
		preg_match('/<\/[Tt][Ii][Tt][Ll][Ee][^>]*>/', $data, $regs);
                if( count($regs) < 1 ) $error .= "\nInternal Error: no close title tag found on $url.";
		else {
		    $end = strpos($data, $regs[0]);
		    $title = trim(substr($data,0,$end));
		    $data = substr($data, $end+strlen($regs[0]));
		}
                $regs = array();
                preg_match('/<[Bb][oO][Dd][yY][^>]*>/', $data, $regs);
                if( count($regs) < 1 ) $error .= "\nInternal Error: no open html body tag found on $url.";
                else {
                    $start = strpos($data, $regs[0]) + strlen($regs[0]);
                    $data = substr($data, $start);
                }
                $regs = array();
                preg_match('/<\/[Bb][oO][Dd][yY][^>]*>/', $data, $regs);
                if( count($regs) < 1 ) $error .= "\nInternal Error: no closing html body tag found on $url.";
                else {
                    $end = strpos($data, $regs[0])-1;
                    $data = substr($data, 1, $end);
                }
            }
            if( !$title ) $title = $url;
            $this->SetCol();
            $this->setFont(FONT, F_SIZE);
            if( !$error ) {
                $this->PrintHeader($this->unhtmlentities( _($title) ), $this->unhtmlentities( _('policy document') ), strncmp($url,WEB,strlen(WEB))==0? $url : (WEB.'/'.$url));
                if( $title ) $this->Bookmark($title,0);
                $this->writeHTMLCell($this->colwidth,2.5,$this->lMargin+1,$this->GetY()+2.5,
                   $data, 0,2,0,'L');
            }
            else
                $this->MultiCell($this->colwidth, 3, $error);
    }

    /*private*/ function PrintCAP($assuree = NULL, $assurer = NULL, $assurance = NULL, $page = NULL ) {
        $this->SetCol();
        $this->PrintHeader($this->unhtmlentities( _('CAcert Assurance Programme') ), $this->unhtmlentities( _('CAcert Assurance Program (CAP) form') ), defined('ASSCAP')?ASSCAP:'',defined('WEB')? WEB.substr(__FILE__, strrpos(__FILE__,'/')) : '');
	// define slightly different footer message
        $this->myFooter('V'. substr($this->Version(), 0, strpos($this->Version(), '.')).', '. $this->unhtmlentities( _('generated') ).' '.date('Y-n-j'), NULL);
        $this->AssuranceInfo($assurance);
        $this->InfoAssuree($assuree, $assurer, $assurance);
        $this->StatementAssuree( $assuree['date']);
        $this->StatementAssurer( $assurer, $assurance );
    }
    
    //Add form and/or CCA (on duplex only when more as one page is printed)
    /*public*/ function PrintForm( $assuree = NULL, $assurer = NULL, $assurance = NULL, $page = NULL ) {

        for($cnt=0  ; $cnt < $this->ncols; $cnt++ ) {
            if( !isset( $page['form']) OR $page['form'] ) {
	        // the form is one page, use new room?
                if ( $assuree == NULL OR $assurer == NULL OR $assurance == NULL )
                    $this->Error('Assuree or Assurer data records failure');
                $this->PrintCap( $assuree, $assurer, $assurance);
            }

	    // print off policy documents to be included in pdf file
	    foreach( $page['policies'] as $i => $file ) {
		$this->Watermark = WATERMARK; // no watermark on these pages
		if( $file ) $this->PrintHTML( $file );
            }
	    if( $this->col > 0 OR $this->getPage() > 1 ) break;
         }
         if(  $this->getPage() > 1 ) {
             // and on duplex print back side with Community Agreement
             if( $this->CurOrientation == 'P' )
                 $this->ViewerPrefs['Duplex'] = 'DuplexFlipLongEdge';
             else
                 $this->ViewerPrefs['Duplex'] = 'DuplexFlipShortEdge';
         }
	 // close up this column, make sure footer is printed.
	 $this->my_header_msg = NULL; $this->SetCol($this->col);
    }

// Set form title (right align)
/*public*/ function PrintHeader($title1 = ' ', $title2 = ' ' , $url1 = NULL, $url2 = NULL) {
            // store current top margin value
            $tSide = $this->tMargin;

            // CAcert logo
            // eps should be better, but it does not seem to work with CAcert logo
            $this->rMargin -= 1;
	    $this->myFooter($title1,$url1);
	    $this->myHeader($title2,$url2);
	    if( LOGO_TYPE == '.eps' )
                $this->ImageEPS(BW?LOGO.'mono'.LOGO_TYPE:LOGO.'colour'.LOGO_TYPE,
                                ($this->lMargin+$this->colwidth)-51,$tSide-3,51);
            else
                // png image 1000 X 229 * 8 bits
                $this->Image(BW?LOGO.'mono'.LOGO_TYPE:LOGO.'colour'.LOGO_TYPE,
                                ($this->lMargin+$this->colwidth)-51,$tSide-3,51,0,0,
                                NULL,0,true,intval(LOGO_DPI));
            $this->myLink($this->lMargin+$this->colwidth-51, $tSide-3,51,51/1000*229,WEB);
            // form type
            $this->SetFont(FONT,'B',F_SIZE+5);
            $this->SetY($tSide+5); $this->SetX($this->lMargin);
            $l = $this->GetStringWidth($title1);
            $this->Cell($this->colwidth+1,14,$title1,0,0,'R',0,NULL);
	    if( $url1 != NULL AND $url1 != '' )
                $this->myLink($this->lMargin+$this->colwidth-$l,$this->GetY()+5,$l,(F_SIZE+5)/2.9,$url1);
            $this->Ln(5); $this->SetX($this->lMargin);
            $l = $this->GetStringWidth($title2);
            $this->Cell($this->colwidth+1,14,$title2,0,0,'R',0,NULL);
	    if( $url2 != NULL AND $url2 != '' )
                $this->myLink($this->lMargin+$this->colwidth-$l,$this->GetY()+5,$l,(F_SIZE+5)/2.9,$url2);

            // CAcert Inc. postbox address
            $this->Ln(6); $this->SetX($this->lMargin);
            $this->SetFont(FONT,'',F_SIZE);
            $savex = $this->GetX(); $savey = $this->GetY();
            $strg = POBOX .' - '. WEB;
            $this->SetXY($this->lMargin+$this->colwidth-$this->GetStringWidth($strg)-1.1,$this->GetY()+3.5); // right align
            if( !BW ) $this->SetTextColor(17,86,140);
            $ret = $this->Write(0,  $strg, NULL);
            $l = $this->GetStringWidth($strg);
            $this->myLink($this->lMargin+$this->colwidth-$l,$this->GetY()+0.5,$l,F_SIZE/2.9,WEB);
            $this->Ln();
            if( !BW ) $this->SetTextColor(0);
            $this->SetXY($savex,$savey);

            // sha1 fingerprint CAcert rootkeys class 1 and class 3
            $strg = $this->unhtmlentities( _("CAcert's Root Certificate sha1 fingerprints") ) . ', class 1: '. CLASS1_SHA1 . ', class 3: ' . CLASS3_SHA1;
            $this->Ln(3); $this->SetX($this->lMargin);
            $this->SetFont(FONT,'',F_SIZE * $this->colwidth / ($this->GetStringWidth($strg) +1));
            $this->Cell($this->colwidth,10, $strg,0,0,'C',0,NULL);
            $this->myLink($this->lMargin, $this->GetY()+4,$this->colwidth,F_SIZE/2.9,ROOTKEYS);
            $this->SetLineWidth(0.1);
            if ( BW ) { $this->SetDrawColor(195);
            } else { $this->SetDrawColor(17,86,140);
            }

            $this->Line($this->lMargin, $tSide+25, $this->lMargin+$this->colwidth, $tSide+25);
            $this->SetLineWidth(0.2); $this->SetDrawColor(0);
            $this->rMargin += 1;
            $this->SetXY($this->lMargin, $tSide+26); // top
    }

// Set general form information
    /*private*/ function PrintInfo( $strg = '', $url = '') {
            // store current margin values

            // Print text blurb paragraph at top of page
            $this->SetFont(FONT,'',F_SIZE+0.5);
            $this->SetXY($this->lMargin, $this->GetY()-1.5);
            $y = $this->GetY(); $x = $this->GetX();
            $cnt=$this->MultiCell($this->colwidth+1, 0, $strg,0,'L',0,2);
            if ( $url != '' ) // link should be in user space
                $this->myLink($x, $y, $this->colwidth, $this->GetY()-$y, $url);
            return($cnt);
    }

// print general CAP info
/*public*/ function AssuranceInfo( $assurance = NULL ) {
            // store current margin values
            $cellcnt = 0;
            $this->SetY($this->GetY()+0.5);
            $this->Bookmark($this->unhtmlentities( _('CAcert CAP form') ),0,$this->S($this->GetY()));

            // Show text blurb at top of page
            $strg = $this->unhtmlentities( _('The CAcert Assurance Programme (CAP) aims to verify the identities of Internet users through face to face witnessing of government-issued photo identity documents.') );
            $strg .= ' '. $this->unhtmlentities( _("The Applicant asks the Assurer to verify to the CAcert Community that the Assurer has met and verified the Applicant's identity against original documents.") );
            $strg .= ' '. $this->unhtmlentities( _('Assurer may leave a copy of the details with the Applicant, and may complete and sign her final form after the meeting.') );
            $strg .= ' '. $this->unhtmlentities( _("If there are any doubts or concerns about the Applicant's identity, do not allocate points. You are encouraged to perform a mutual Assurance.") );
            $cellcnt += $this->PrintInfo( $strg, defined('ASSINTRO')? ASSINTRO:'');
            $cellcnt += $this->PrintInfo( $this->unhtmlentities( _('For more information about the CAcert Assurance Programme, including detailed guides for CAcert Assurers, please visit:') ).' '.WEB, defined('ASSCAP')?ASSCAP:'');
            $cellcnt += $this->PrintInfo( $this->unhtmlentities( _('A CAcert Arbitrator can require the Assurer to deliver the completed form in the event of a dispute. After 7 years this form should be securely disposed of to prevent identity misuse. E.g. shred or burn the form. The Assurer does not retain copies of ID at all.') ),defined('ARBIT')?ARBIT:'');
            $cellcnt += $this->PrintInfo( $this->unhtmlentities( _('For the CAcert Organisation Assurance Programme there is a separate special COAP form.') ),defined('ASSORG')?ASSORG:'');
            $this->SetY($this->GetY()-0.1);
            $this->PrintLocation( $assurance );
            $this->SetY($this->GetY()+1);

        }

    // print empty table with title for statements (called twice per table)
    /*private*/ function PrintTable( $strg = NULL, $height = -1, $ext = 0 ) {
        // store current margin values
        static $tSide = -1;
        static $title = '';
        if( $height < 0 ) { // mark table position, leave room for title
            if( $strg != '' ) $title = $strg;
            if( $title == '' ) return ($this->GetY()); // nothing to do
            $tSide = $this->GetY()+1;
	    // background
            if ( BW ) {
                $this->SetFillColor(195);
                $this->SetDrawColor(195);
            } else {
                $this->SetFillColor(173,197,215);
                $this->SetDrawColor(173,197,215);
            }
            $this->Rect($this->lMargin-1,$tSide-1,1,9, 'F');
            $this->Rect($this->lMargin-1,$tSide-1,$this->colwidth,1, 'F');
            $this->SetFillColor(255);
            if ( BW ) { $this->SetFillColor(125);
            } else { $this->SetFillColor(17,86,140);
            }
            $this->Rect($this->lMargin,$tSide,$this->colwidth,7, 'DF');
            $this->SetFillColor(255); $this->SetDrawColor(0);

            $this->SetXY($this->lMargin+1, $tSide+0.6);
            $this->Bookmark($title,1,$this->S($tSide));
            $this->SetFont(FONT, '', F_SIZE+7);
            $this->SetTextColor(255);
            $this->Write(0, $title);
            $this->SetTextColor(0);
            $this->SetXY($this->lMargin+1, $tSide + 7);
            $tSide += 8; // save old top
            if ( $height != 0 ) return($this->GetY());
        }
        elseif( $tSide < 0 ) return( $this->GetY());
        if( $height == 0 )  { // interrupted bottum of column reached
            $height = $this-GetY() - $tSide; $save = $title;
            $this->PrintTable('', $height); // finish till bottumn page
            $tSide = $this->originalMargin;
            $title = $save;
            return( $this->GetY());
        }
        if( $strg != '' ) $title = $strg; // just to be defensive

	// background
        if ( BW ) {
            $this->SetFillColor(195);
            $this->SetDrawColor(195);
        } else {
            $this->SetFillColor(173,197,215);
            $this->SetDrawColor(173,197,215);
        }
        $this->Rect($this->lMargin-1,$tSide,1,$height-1+$ext, 'F');
        if( $ext )
            $this->Rect($this->lMargin-1,$tSide+$height,$this->colwidth,$ext, 'F');
        $this->SetFillColor(255);
        // borders of the table left, bottumn, right
        $this->Line($this->lMargin,$tSide+$height-1, $this->lMargin, $tSide+$height);
        $this->Line($this->lMargin,$tSide+$height,$this->lMargin+$this->colwidth,$tSide+$height);
        $this->Line($this->lMargin+$this->colwidth,$tSide-1, $this->lMargin+$this->colwidth, $tSide+$height);
        $this->SetDrawColor(0);
	$this->SetY($tSide + $height + 1); // set Y ordinate to plus 7 
        $tSide = -1; $title = '';
	return($this->GetY());
    }

// assuree information
    /*private*/ function PrintName( $names = NULL, $assurancemax = -1 ) {
        //$H = 5; // height of the name cell
        $name = $names == NULL ? '': $names['name'];
        $id_type = $names == NULL ? '' : $names['idtype'];
        // store current margin values
	static $nr = 0;
        static $idtypes = NULL; 
        static $listpoints = NULL;
        static $ComboProps = array( 'fillColor'=> LBLUE, 'strokeColor'=> LLBLUE, 'editable'=> 'true', 'textSize' => 9, 'rotate'=> '0');
        static $TextProps = array('strokeColor'=> LLBLUE, 'value' => ' ', 'fillColor'=> LBLUE, 'doNotScrole'=> 'false', 'textSize' => 12, 'rotate'=> '0');
        static $TextBlankProps = array('strokeColor'=> 'white', 'fillColor'=> 'white', 'readonly' => 'true', 'doNotScrole'=> 'true', /* 'lineWidth'=> '0'*/);
        $TextProps['textSize'] = 12/6*H;

        $nr++;
        $savey = $this->GetY();

        $namepoints = intval($names == NULL? -1 : $names['points']);
        if( $namepoints <= 0 ) $namepoints = $assurancemax;
        if( $namepoints <= 0 OR $namepoints >= 35 ) $namepoints = 35;
        if( $listpoints == NULL ) {
            $listpoints = array( 0=> ' ');
            for ($i = 0; $i <= $namepoints; $i++)
	        $listpoints += array( ($i+1) => sprintf("%d",$namepoints-$i) );
        }
        $points = sprintf("AssureeNames_%d_Points", $nr);

        // just once to recover from Acrobat 7.0 problem !!!!!!!!!!!!!!!!!!!!!!!!!!
        // make sure before the first time form field JS is called the fake is done
        static $AcrobatName = array('strokeColor'=> LLBLUE, 'fillColor'=> LLBLUE, 'readonly' => 'true');
        if( $AcrobatName != NULL ) {
            $this->TextField( 'NameNone', $this->SetFieldXY(300, $this->GetY()+2, 0), 0, $AcrobatName);
            $this->SetFieldXY(); $AcrobatName = NULL;
        }
        static $AcrobatCombo = array( 'fillColor'=> LLBLUE, 'strokeColor'=> LLBLUE, 'editable'=> 'false');
        if( $AcrobatCombo != NULL ) {
	    $this->ComboBox( 'PointsNone', $this->SetFieldXY(300, $this->GetY()+20, 0), 0, array(' '), $AcrobatCombo);
            $this->SetFieldXY(); $AcrobatCombo = NULL;
        }
        // end of Acrobat defeat !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $this->ComboBox( $points, $this->SetFieldXY($this->lMargin+$this->colwidth-15, $this->GetY()+2, 10),(5/6*H), $listpoints, $ComboProps);
        $this->SetFieldXY();
        // small line in points collumn
        $this->myLine($this->lMargin+$this->colwidth-8.5,$savey+H+0.3,$this->lMargin+$this->colwidth-8,$savey+H+0.3);
        if( $namepoints >= 0 ) {
           // this seems not to be visible: no points printed...
	   $this->SetXY( $this->lMargin+$this->colwidth-17.5, $savey+1 );
           $this->SetFont(FONT, 'B', (F_SIZE+1)/6*H);
           $this->Cell( 15, sprintf("(%d)", $namepoints), 0, 0, 'C');
        }

        $TextProps['value'] =  $name ? $name : ' '; $TextProps['textSize'] = 14;
        $TextProps['userName'] = $this->unhtmlentities( _('Exact full name as on shown ID for this name.') );
        $this->TextField(sprintf('AssureeNames_%d_Name',$nr), $this->SetFieldXY($this->lMargin+2,$savey+0.9,$this->colwidth-40), 8/6*H, $TextProps);
	$this->SetFieldXY();
        $this->SetXY($this->lMargin+2, $savey);
        if($name) {
            $token = $name;
            $fnt = (F_SIZE+4)/6*H;
            $this->SetFont(FONT, 'B', $fnt);
            $lngth = $this->GetStringWidth($token);
            if( $lngth > $this->colwidth-35 ) $fnt *= ($this->colwidth-35) / $lngth;
            $this->SetFont(FONT, 'B', $fnt);
            $this->MultiCell($this->colwidth-25, 0, $token, 0, 'L');
            if( function_exists('utf8_to_ascii') AND
              !utf8_is_ascii_ctrl( $name) )
                $ascii = utf8_to_ascii($name);
            else
                $ascii = '';
            $abbr = $this->Abbreviate( $name );
            if( strcmp($ascii,$name) != 0 OR strcmp($abbr,$name) != 0) {
                $msg = $this->unhtmlentities( _('The exact name of the assuree may have transliterated characters and/or some given names may be abbreviated. If present the first given name will be shown abbreviated and parentheses around the last part of the given name.\n') );
                if( $ascii != '' AND strcmp($ascii,$name) != 0 ) $msg .= $this->unhtmlentities( _('transliterated: ') ). $ascii.'\n';
                if( $abbr  != '' AND strcmp($abbr, $name) != 0 ) $msg .= $this->unhtmlentities( _('abbreviated: ') ). $abbr.'\n';
                $this->myLink($this->lMargin-7, $savey+1, 3, 3, $msg,
                array('subtype'=> 'Text', 'Open' => 'false',
                      'c'=> array(173,197,215), 'name'=> 'Comment',
                      'f'=> array( 'nozoom', 'readonly', 'norotate'),
                      't'=> $this->unhtmlentities( _('On the assuree full name') )));
            }
	} else {
            $id_type = '';
	    // no name, give a line to write on
            $this->myLine($this->lMargin+2,$savey+H+0.3,$this->lMargin+$this->colwidth-37,$savey+H+0.3); // line under name
        }
        $this->SetXY($this->lMargin+2, $savey);
        if ( !$name ) $idtypes = NULL ;
        if( $idtypes == NULL ) {
            $idtypes = array( ); $i = 0;
            if (!$name) $idtypes = array( $i++ => ' ');
            if( $id_type ) {
                $token = $id_type;
                $token = strtok($token,'/');
                for( ; $token != ''; $i++) {
		   $idtypes += array( $i => $token ); $token = strtok('/');
                }
            }
            $idtypes += array( $i => $this->unhtmlentities( _('passport') ), $this->unhtmlentities( _('driver license') ), $this->unhtmlentities( _('ID card') ), $this->unhtmlentities( _('mil. ID') ), $this->unhtmlentities( _('certificate') ), $this->unhtmlentities( _('TTP') ) );
        }
        if( !$id_type ) {
            $this->myLine($this->lMargin+$this->colwidth-37,$savey+H+0.3,$this->lMargin+$this->colwidth -17,$savey+H+0.3);// line under ID
            $this->myLine($this->lMargin+$this->colwidth-37,$savey+H-0.7,$this->lMargin+$this->colwidth-37,$savey+H+0.1); // l small ID vert
        } else {
            $this->SetXY($this->lMargin+20, $savey+1);
            $this->SetFont(FONT, 'B', (F_SIZE+1)/6*H);
            $this->Cell($this->colwidth-37, 2, '('.$id_type .')', 0, 0, 'R');
            // hide id type print on screen with the formfields, just nicety
            // one could extend the name field, but this has more drawbacks 
            $this->TextField(sprintf('AssureeNames_%d_None',$nr), $this->SetFieldXY($this->lMargin+$this->colwidth-38,$savey+0.5,20), 7/6*H, $TextBlankProps);
            $this->SetFieldXY();
        }
        $this->ComboBox( sprintf("AssureeNames_%d_TypeID",$nr), $this->SetFieldXY($this->lMargin+$this->colwidth-37, $savey+2, 19), 5/6*H, $idtypes, $ComboProps);
        $this->SetFieldXY();
        $this->SetXY($this->lMargin+2, $savey+H);
        return( $name );
    }

// All information of Assuree goes in one table
/*public*/ function InfoAssuree( $assuree = NULL, $assurer = NULL, $assurance = NULL ) {
        $dob = $assuree['dob'];
        $email = $assuree['email'];
        $assurancemax = $assurance['maxpoints'];
        if( $assurancemax <= 0 ) $assurancemax = 35;

        // Assuree Identity information part
        $tSide = $this->PrintTable($this->unhtmlentities( _("Applicant's Identity Information") ))+1;

        $msg  = $this->unhtmlentities( _('Some examples of possible standard transliterations in a full individual name. If more than one transliteration of a character is possible, it is denoted within parentheses.') );
        $msg .=  $this->unhtmlentities( _('a and y umlaut, and o/slash examples of accepted transliterations for the full name(s)') ).':';
        $msg .= "\n* ". 'J(ae|a)hny G(oe|o)le McName';
        $msg .= "\n\n".$this->unhtmlentities( _('As an example standard abbreviation on given names is provided here. If a given name is counted as first given name part of the name is denoted with parentheses around the name.') );
        $msg .= $this->unhtmlentities( _('Examples of accepted abbreviation(s) for full name(s)') ).':';
        $msg .= "\n* ". 'sir J(ähnÿ) G. McName';
        $msg .= "\n";
        // put hint on comparing names on title table
        $this->myLink($this->lMargin-7, $tSide-7, 5, 5, $msg,
        array('subtype'=> 'Text', 'Open' => 'true',
                      'c'=> array(173,197,215), 'name'=> 'Comment',
                      'f'=> array( 'nozoom', 'readonly', 'norotate'),
                      't'=> $this->unhtmlentities( _('On comparing names') )));

        // names of applicant
        $this->SetXY($this->lMargin+1, $tSide);
        $this->SetFont(FONT, '', F_SIZE+1);
        $this->Write(0, $this->unhtmlentities( _('Exact full name on the ID') ).':  ');
        $strg = '('. $this->unhtmlentities( _('type of ID shown') ).')';
        $this->SetFont(FONT, '', F_SIZE);
        $this->SetXY($this->lMargin+$this->colwidth-$this->GetStringwidth($strg)-19, $tSide);
	$this->Write(0, $strg);
        if( $assurancemax > 0 AND $assurancemax <= 50 ) {
	    $strg = sprintf($this->unhtmlentities( _('max') ) . " %d", $assurancemax);
            $this->SetFont(FONT, F_SIZE);
            $strg = $strg;
	    $this->SetXY($this->lMargin+$this->colwidth-19, $tSide);
            $this->Cell(20, 3, $strg, 0, 0, 'C');
        }
        $this->SetXY($this->lMargin+2, $tSide+3);

        // all (max) three names with ID type right aligned.
        $cnt = $assuree['namecnt'];
        $space = $this->getPageHeight()/$this->scale*100.0 -MINH ; // margin
        for( $i = 0; $i < $cnt; $i++ )  { // names to be printed 
            $this->PrintName( $assuree['names'][$i], $assurer['maxpoints'] < 0? 35: $assurer['maxpoints'] );
            if( $space < $this->getY() ) break;
        }
        for( $j=0 ; $j < $i+3; $j++ ) {
            // empty fields up to max 3 empty fields and allowed space
            if( $space < $this->getY() ) break;
            $this->PrintName( NULL, $assurer['maxpoints'] < 0? 35: $assurer['maxpoints']);
        }

	// email address assuree
        $savey = $this->GetY()+1;
        $this->myLine($this->lMargin+$this->colwidth-17,$savey-0.7,$this->lMargin+$this->colwidth,$savey-0.7); // line above DoB
        $this->myLine($this->lMargin+$this->colwidth-17,$tSide+3,$this->lMargin+$this->colwidth-17,$savey-0.7); // left points column
        $this->SetXY($this->lMargin+1, $savey);
        $this->SetFont(FONT, '', F_SIZE);
        $strg = $this->unhtmlentities( _('Email address') ).':  ';
        $l = $this->GetStringWidth($strg);
        $this->Write(0, $strg);
        if($email) {
            $this->SetFont(FONT, 'B', F_SIZE+1);
            if( !BW ) $this->SetTextColor(17,86,140); $this->SetXY($this->GetX(),$this->GetY()-0.35);
            $ret = $this->Write(0,  $email);
            $this->myLink($this->lMargin+2,$this->GetY(),$l, F_SIZE/2, 'mailto:'.$email.'?subject=Your CAcert Assurance');
            $this->Ln();
            if( !BW ) $this->SetTextColor(0);
        }
        // somehow fields are not scaled....
        $TextProps = array('strokeColor'=> LLBLUE, 'value' => '', 'fillColor'=> LBLUE, 'doNotScrole'=> 'true', /* 'lineWidth'=> '0', */ 'textSize' => '11', 'rotate'=>0, 'userName'=> $this->unhtmlentities( _('email address as e.g. john.family@gmail.com') ) );
        $TextProps['value'] = $email;
        $this->TextField('AssureeEmail', $this->SetFieldXY($this->lMargin+2+$l, $savey,$this->colwidth-40-$l), 5, $TextProps);
	$this->SetFieldXY();

        $this->SetXY($this->lMargin+$this->colwidth-25, $savey);
        // date of birth assuree
        $strg =  $this->unhtmlentities( _('Date of Birth') );
        if( $dob == '' )
            $strg .= ' ('.$this->unhtmlentities( _("yyyy-mm-dd") ). ')';
        $this->PrintDate( $this->lMargin+$this->colwidth-3, $savey, $strg, $dob, 'AssureeDoB', 'R');
        $savey += 4;

        $this->myLine($this->lMargin+$this->colwidth-16.9,$tSide-7.0,$this->lMargin+$this->colwidth-16.9,$tSide-2); // column left
        $this->SetXY($this->lMargin+$this->colwidth-14.95, $tSide-8.7);
        $this->SetFont(FONT, '', F_SIZE);
        $this->SetTextColor(255);
        $this->MultiCell(14.9,2, $this->unhtmlentities( _("points allocated") ), 0, 0, 'C');
        $this->SetTextColor(0);
        $this->SetXY($this->lMargin+1, $savey);
        $savey += 3; // save some room for DoB
	$next = $this->PrintTable( '', $savey-$tSide, 5);
	$this->SetY($next);
    }

    // print marked paragraph in the table
    /*private*/ function PrintTicked( $strg = '', $tick = true ) {
        // store current margin values
        $savey = $this->GetY();

	$celcnt = 0; $this->SetX($this->lMargin + 1);
	if( $tick ) {
            // use ✔ and ❑ of zapfdingbats font for OK tick
            $savex = $this->GetX();
	    $this->SetXY($this->GetX(), $savey+0.9);
            $this->SetFont('zapfdingbats', F_SIZE+3);
            $this->Write(0,'q'); // ❑
            $this->SetXY($savex+0.1, $savey+0.1);
            if ( BW )
                $this->SetTextColor(80);
            else
                $this->SetTextColor(17, 86, 140);
                //$this->SetTextColor(0,92,0); // #00BE00 lime
            $this->Write(0,'4 ');// ✓
            $this->SetTextColor(0);
        }
        $this->SetXY($this->GetX(), $savey);
        $this->SetFont(FONT,'',F_SIZE+0.5);
        $celcnt = $this->MultiCell($this->lMargin+$this->colwidth-$this->GetX(), 3, $strg,0,'L');
	$this->SetXY($this->lMargin+1, $this->GetY()-1.5);
	return($celcnt);
    }

// assuree statement
/*public*/ function StatementAssuree( $date = '' ) {
        // store current margin values
	$cellcnt = 0;

        // assuree statement section
        $tSide = $this->PrintTable($this->unhtmlentities( _("Applicant's Statement") )); // mark table header

        $cellcnt += $this->PrintTicked( $this->unhtmlentities( _("Make sure you have read and agreed with the CAcert Community Agreement") ), false /* no tick */);
        if( !BW ) $this->SetTextColor(17, 86, 140);
        $ret = $this->Write($this->lasth, WEB.'/'.POLICY.CCA.EXT, NULL);
        $this->myLink($this->lMargin+1, $this->GetY()-F_SIZE/2.9, $this->colwidth-2, F_SIZE*2.9/2.9, WEB.'/'.POLICY.CCA.EXT);
        $this->Ln(4);
        if( !BW ) $this->SetTextColor(0);

        $cellcnt += $this->PrintTicked( $this->unhtmlentities( _('I hereby confirm that the information stating my Identity Information above is both true and correct, and request the CAcert Assurer (see below) to witness my identity in the CAcert Assurance Programme.') ), true /* tick */);
        $cellcnt += $this->PrintTicked( $this->unhtmlentities( _('I agree to the CAcert Community Agreement.') ), true /* tick */);

        $this->Ln(0.5);
        $this->SetFont(FONT, F_SIZE+1); $savey = $this->GetY();
        $strg = $this->unhtmlentities( _('Date') );
        if($date == '')
            $strg .=  ' ('. $this->unhtmlentities( _("yyyy-mm-dd") ) . ')';
        $strg = $strg;
        $this->PrintDate( $this->lMargin+1, $savey+1, $strg, $date, 'AssureeDate', 'L');

	$strg = $this->unhtmlentities( _("Applicant's signature") );
        $this->SetFont(FONT, '', F_SIZE);
	$l = $this->GetStringWidth( $strg );
	$this->SetXY($this->lMargin+$this->colwidth-$l-3, $savey+1);
        $this->Write(0, $strg); $this->Ln(7) ; // and leave some room

        // draw the table borders and header at marked ordinate
        $next = $this->PrintTable('', $this->GetY()-$tSide);

        $this->SetY($next);
    }

// assurer statement
 /*public*/ function StatementAssurer( $assurer = NULL, $assurance = NULL ) {
        if( $assurer == NULL OR $assurance == NULL ) return;

        // store current margin values
        $TextProps = array('strokeColor'=> LLBLUE, 'value' => '', 'fillColor'=> LBLUE, 'doNotScrole'=> 'true', 'textSize' => '14', 'rotate'=>0);
	$cellcnt = 0;

        $tSide = $this->PrintTable($this->unhtmlentities( _("Assurer's Statement") )); // mark table ordinate
        // put hint on mutaul assurance in pdf file for mouse over
        $msg = $this->unhtmlentities( _('One is advised for a mutual assurance. If done so the exact full name, email address and date of birth of the Assurer is also required on a form. In this case the Assuree assures the Assurer as well. In this case two copies are needed of the CAP form.') );
        $this->myLink($this->lMargin-7, $tSide-6, 5, 5, $msg,
        array('subtype'=> 'Text', 'Open' => 'true',
                      'c'=> array(173,197,215), 'name'=> 'Comment',
                      'f'=> array( 'nozoom', 'readonly', 'norotate'),
                      't'=> $this->unhtmlentities( _('On mutual assurance') )));
        // assurer identity info
	// name, meeting date, meeting location,
        // dob and email address string only is printed when info available
        $this->SetXY($this->lMargin+1, $tSide);
        $savey = $this->GetY()+2.5;
        $this->SetFont(FONT, '', F_SIZE);
        $strg = $this->unhtmlentities( _("Assurer's Name") ) . ': ';
        if( $assurer['name'] AND
          function_exists('utf8_to_ascii') AND
          !utf8_is_ascii_ctrl( $assurer['name']) )
            $ascii = utf8_to_ascii($assurer['name']);
        else
            $ascii = '';
        if( $ascii == $assurer['name'] ) $ascii = '';
        $abbr = $this->Abbreviate( $assurer[ 'name'] );
        if( $ascii != '' OR $abbr != '') {
            $msg = $this->unhtmlentities( _('The exact name of the assurer may have transliterated characters and/or some given names may be abbreviated. If present the first given name will be shown abbreviated and parentheses around the last part of the given name.\n') );
            if( $ascii != '' ) $msg .= '* '. $ascii . "\n";
            if( $abbr != '' ) $msg .= '* '. $abbr . "\n";
            $this->myLink($this->lMargin-7, $tSide+2, 3, 3, $msg,
            array('subtype'=> 'Text', 'Open' => 'false',
                      'c'=> array(173,197,215), 'name'=> 'Comment',
                      'f'=> array( 'nozoom', 'readonly', 'norotate'),
                      't'=> $this->unhtmlentities( _('On the full name of the Assurer') )));
        }
        $this->Write(0, $strg);
        $strg = '';
        if( $assurer['dob'] ) $strg = $this->unhtmlentities( _('Date of Birth') );
        // if assurer dob is space we print format date
        if( $assurer['dob'] == ' ' ) {
            $strg .= ' ('. $this->unhtmlentities( _("yyyy-mm-dd") ) .')';
            $assurer['dob'] = '';
        }
        $this->PrintDate($this->lMargin+$this->colwidth-3, $savey-2.5, $strg, $assurer['dob'], 'AssurerDoB', 'R');
        $this->SetXY($this->lMargin+2, $savey);
        if($assurer[ 'name']) {
            $strg = $assurer[ 'name'];
            $this->SetFont(FONT, 'B', F_SIZE);
            $cellcnt += $this->MultiCell($this->colwidth-22, 3, $strg, 0, 1);
            $this->SetFont(FONT, '', F_SIZE);
            if( $assurer['email'] ) {
                $sx = $this->GetX(); $sy = $this->GetY();
                $sw = $this->GetStringWidth($strg) + 2;
                $this->SetXY( $this->lMargin+$sw+4, $sy-4.1);
                if( !BW ) $this->SetTextColor(17,86,140);
                $ret = $this->Write(0,  $assurer[ 'email'], 'mailto:'. $assurer[ 'email']); $this->Ln();
                if( !BW ) $this->SetTextColor(0);
            }
        }
        $TextProps['value'] =  $assurer[ 'name'] ? $assurer[ 'name'] : ' ';
        $TextProps['textSize'] = 11; $TextProps['userName'] = $this->unhtmlentities( _('Full exact name of Assurer. On mutual assurance provide date of birth as well.') );
        $this->TextField('AssurerName', $this->SetFieldXY($this->lMargin+2, $savey+1, $this->colwidth-60), 5, $TextProps );
        $this->SetFieldXY();
        $TextProps['value'] = $assurer['email'] ? $assurer['email'] : $this->unhtmlentities( _('email') ) . '?';
        $TextProps['userName'] = $this->unhtmlentities( _('On mutual assurance provide email address of Assurer.') );
        $this->TextField('AssurerEmail', $this->SetFieldXY($this->lMargin+68.5, $savey+1, 35), 5, $TextProps ); 
        $this->SetFieldXY();
        $this->SetXY($this->lMargin+2, $savey+5);

        // assurer statements
        $cellcnt += $this->PrintTicked( $this->unhtmlentities( _("I, the Assurer, hereby confirm that I have verified the Applicant's Identity Information, I will witness the Applicant's identity in the CAcert Assurance Programme, and allocate Assurance Points.") ), true /* ticked */);
        $cellcnt += $this->PrintTicked( $this->unhtmlentities( _('I am a CAcert Community Member, have passed the Assurance Challenge, and have been assured with at least 100 Assurance Points.') ), true /* ticked */);
        $this->Ln(1); $savey = $this->GetY();

        $strg =  $this->unhtmlentities( _('Date') );
        if( $assurer['date'] == '' ) $strg .= ' (' . $this->unhtmlentities( _("yyyy-mm-dd") ) . ')';
        $strg = $strg;
        $this->PrintDate( $this->lMargin+1, $savey, $strg, $assurer['date'], 'AssurerDate', 'L');

        $this->SetXY($this->lMargin+1, $savey);
        $this->SetFont(FONT, '',F_SIZE);
        $strg = $this->unhtmlentities( _("Assurer's signature") );
        $this->SetXY($this->lMargin+$this->colwidth-$this->GetStringWidth($strg)-3, $savey);
        $this->Write(0, $strg );
        $savey = $this->GetY()+7; // leave room for date and signature
        if( $this->GetPageHeight()/$this->scale*100.0-$savey > MARGIN+4)
           $savey += 2;
        $this->SetXY($this->lMargin+1, $savey);
        $l =  $this->GetPageHeight()/$this->scale*100.0-$this->GetY() - MARGIN;
        if($l > 3 ) $l = 3; if( $l > 0 ) $this->Ln($l); // try to come close to margin
        $next = $this->PrintTable('', $this->GetY()-$tSide);
        $this->SetY($next);
    }

        // location print within caller table
/*private*/ function PrintLocation( $assurance = NULL ) {
        if( $assurance == NULL ) return;
        $savey = $this->GetY();
        //$this->SetXY($this->GetX(), $savey);
        $strg = $this->unhtmlentities( _('Date and location of the face-to-face meeting') ). ': ';
        $this->SetFont(FONT,'I', F_SIZE);
        $this->Write(0, $strg);
        $l = $this->GetStringWidth($strg);
        $strg = $assurance['location'];
        if( $strg) {
            if( $assurance['date'] ) $strg = $assurance['date'] . ', '. $strg;
            $strg = $strg;
	    $this->SetFont(FONT, 'B', F_SIZE);
            $this->Write(0,$strg);
        }
        $TextProps['textSize'] = 11;
        $TextProps['value'] =  $assurance['location'] ? $assurance['location'] : ' ';
        $TextProps['userName'] = $this->unhtmlentities( _('location of the assurance') );
        $this->TextField('AssuranceLocation', $this->SetFieldXY($this->lMargin+$l+19, $savey,$this->colwidth-$l-21), 5, $TextProps );
        $this->SetFieldXY();
        $TextProps['value'] = $assurance['date'] ? $assurance['date'] : ' ';
        $TextProps['charLimit'] = 10;
        $TextProps['userName'] = $this->unhtmlentities( _("yyyy-mm-dd") ).' '. $this->unhtmlentities( _('date of assurance') );
        $this->TextField('AssuranceDate', $this->SetFieldXY($this->lMargin+1+$l, $savey, 17), 5, $TextProps );
        $this->SetFieldXY();
        $this->SetXY($this->lMargin,$savey+3);
    }

// End of CAPPDF TCPDF class extension
}


// --------------------------------------------------------------------------------
// import environmental data -------------------------------------------------------
// get $form, $orientation, $assuree, $assurer, $assurance info
// FONT and BW are set already

// import info 
function GET( $key = '' ) {
    return ( array_key_exists( $key, $_GET) ? $_GET[$key] : '');
}

// form, CCA and page format info
    $page['format'] = strtolower(GET('format')); // A3, A4, A5, letter, legal, etc.
    if( !$page['format'] ) $page['format'] = 'a4'; // default
    // on landscape orientation we do two half pages
    $page['orientation'] = strtolower(GET('orientation'));
    if( $page['orientation'] != 'l' AND $page['orientation'] != 'landscape' ) {
	// default portrait 1-up
        $page['orientation'] = 'p'; // landscape implies 2-up
    }
    $page['form'] = GET('noform') != '' ? false : true;
    // dft is now true it should go to true
    $page['policies'] = array();
    if( GET('nocca') == '' ) {
        if( defined('CCA') ) $page['policies'][] = CCA;
    }
    // set $page['form'] on 'simplex' or 'duplex' to get CCA on pdf page

// Assurer info
$assurer = array ( 'name'      => my_recode(GET('assurer')) ,
                   'dob'       => my_recode(GET('assurerdob')),
                   'email'     => my_recode(GET('assureremail')),
                   'maxpoints' => intval(my_recode(GET('maxpoints'))) < 0? -1 :
                                  intval(my_recode(GET('maxpoints'))),
                   'date'      => my_recode(GET('assurerdate')) ? my_recode(GET('assurerdate')) :
                                  my_recode(GET('date'))
                  );
if( $assurer['name']  != '' AND
		!utf8_is_ascii_ctrl($assurer['name']) AND
            !function_exists('utf8_to_ascii'))
                require_once ( UTF8_ASCII );

// assurance info
$assurance = array ( 'location' => my_recode(GET('location')),
                     'date'     => my_recode(GET('assurancedate'))?my_recode(GET('assurancedate')):
                                   my_recode(GET('date')),
                     'maxpoints'=> intval(my_recode(GET('maxpoints'))) < 0? -1 :
                                   intval(my_recode(GET('maxpoints'))),
                     'notes'    => '' // not yet used
                  );

// Assuree info
$assuree = array ( 'names'      => array( ),
                   'namecnt'    => 0,
                   'dob'        => my_recode(GET('dob')),
                   'email'      => my_recode(GET('email')),
                   'date'       => my_recode(GET('date')) == 'now' ? date('Y-m-d') :
                                   my_recode(GET('date'))
                 );

// defeats due to history
if( $assuree['date'] == '' AND  $assurer['date'] ) $assuree['date'] =  $assurer['date'];
if( $assurance['date'] == '' AND  $assuree['date'] ) $assurance['date'] =  $assuree['date'];
if( $assurer['date'] == 'now' ) $assurer['date'] = date('Y-m-d');
if( $assurance['date'] == 'now' ) $assurance['date'] = date('Y-m-d');
$document = my_recode(GET('document1'));
if( GET('document2') ) $document .= '/'. my_recode(GET('document2'));
// for history sake do special, next can void other arguments...
if( GET('name') ) $_GET['name1'] = GET('name');
if( $document ) $_GET['name1ID'] = $document;

function Dstr( $strg = '', $cnt = 1 ) {
    return( sprintf("name%d%s", $cnt, $strg));
}
$j = 0; // after two successive empty names we stop
for( $i = 1; $i <= 9 AND $j < 2; $i++) { // max 9 names we only print 4 max...
    $name = my_recode(GET(Dstr('', $i)));
    if( $name ) { $j = 0;
        $assuree[ 'namecnt' ]++;
        $assuree[ 'names' ] [] = array (
            'name' => $name ? $name : '',
            'idtype' => my_recode(GET(Dstr('ID',$i)))? my_recode(GET(Dstr('ID',$i))) : '', 
            'points' => my_recode(GET(Dstr('Pnts',$i))) != '' ? intval(my_recode(GET(Dstr('Pnts',$i)))) : -1
        );
        if( $name  != '' AND
            !utf8_is_ascii_ctrl($name) AND
            !function_exists('utf8_to_ascii'))
                require_once ( UTF8_ASCII );
    } else  $j++;
}

// try to get policy documents names to be printed off
$j = 0; // after two successive empty name we stop searching
for( $i = 1; $i <= 9 AND $j<2; $i++ ) {
   $name = GET(sprintf("policy%d", $i));
   if( $name != '' ) { $page['policies'][] = $name; $j = 0; }
   else $j++;
}

unset( $document ); unset( $i ); unset( $j); // unset($_GET);
// end of arguments imports

    header('Expires: '.gmdate("D, j M Y G:i:s \G\M\T", time()+10800));
    header('Content-Disposition: attachment; filename=CAcert cap.pdf');
    header('Cache-Control: public, max-age=10800');
    header('Pragma: cache');
//  Content-Type and Content Length is done by tcpdf package

// create new PDF document =====================================================
    $pdf = new CAPPDF(
       /* PDF_PAGE_ORIENTATION */ $page['orientation'],
       PDF_UNIT /* mm */,
       /* PDF_PAGE_FORMAT */ $page['format'],
       true
       ); 
    $pdf->SetFormat( $page['format']  ); // set paper size scaling

// protection is encryption and this will cause 3.5 times performance loss
//    $pdf->SetProtection(array('print', 'annot-forms'));

// set document property information
    $pdf->SetCreator('LibreSSL - CAcert web application');
    $pdf->SetAuthor('© ' . date('Y') . ' CAcert Inc., Australia.');
    $pdf->SetKeywords('X.509, Assurance Programme, CAP form, Community Agreement, Digital Certificates, CAcert');
    $pdf->SetTitle('CAcert Assurance Programme');
    $pdf->SetSubject('CAP form V'.$pdf->Version().', generated ' . date("Y-n-j H:i:s T"));
    if( GET('watermark') != '') $pdf->Watermark = my_recode(GET('watermark'));
    // requires zlib and will decrease response time but increase bandwidth
    // if no zlib is found, automatically no compression is done
    $pdf->SetCompression(true); // turn it off when more pperformance is needed

// AddSJISFont function is not present in tcpdf package !!!!

//set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, MARGIN*0.707);

//set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
    $pdf->setLanguageArray($l); 

//initialize document
    $pdf->AliasNbPages();

// AND GENERATE THE FORM   ===================================
    // generation properties which have been set from environment:
    // deflt: BW (color), FONT (free Sans Vera), portrait 1-up, A5, duplex, with CCA
    // orientation landscape gives 2-up
    if ( FONT == 'SJIS') $pdf->AddSJISFont();
    $pdf->PrintForm($assuree, $assurer, $assurance, $page);
    $pdf->setViewerPreferences($pdf->ViewerPrefs);

//Close and output PDF document
    $pdf->Output('CAcert CAP.pdf', 'I');

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
