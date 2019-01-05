#!/usr/bin/php -q
<?php /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2011  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

# Companion script to DumpWeakCerts.pl, takes output and revokes weak certs
# Only first and last column ($cert_type and $cert_recid) are used, the others
# are ignored

include_once("../includes/mysql.php");
# Main

$num_domain = 0;
$num_client = 0;
$num_orgdomain = 0;
$num_orgclient = 0;

$num_failures = 0;

$in = fopen("php://stdin", "r");

# The restriction on revoked timestamp os only "to be sure" for non-Org certs,
# but Org certs (email and serer) may be included multiple times in the output
# of DumpWeakCerts.pl (once for each OrgAdmin).
while($in_string = rtrim(fgets($in))) {
	list($cert_type, $cert_email, $owner_name, $cert_expire, $cert_CN, $reason,
		$cert_serial, $cert_recid) = explode("\t", $in_string);
	
	if ($cert_type == "DomainCert") {
		$query = "UPDATE `domaincerts` SET `revoked`='1970-01-01 10:00:01'
			where `id`='$cert_recid' AND `revoked`<'1970-01-01 10:00:01'";
		
		if (!mysqli_query($_SESSION['mconn'], $query)) {
			$num_failures++;
		}
		$num_domain+=mysqli_affected_rows($_SESSION['mconn']);
		
	} else if ($cert_type == "EmailCert") {
		$query = "UPDATE `emailcerts` SET `revoked`='1970-01-01 10:00:01'
			where `id`='$cert_recid' AND `revoked`<'1970-01-01 10:00:01'";
		
		if (!mysqli_query($_SESSION['mconn'], $query)) {
			$num_failures++;
		}
		$num_client+=mysqli_affected_rows($_SESSION['mconn']);
		
	} else if ($cert_type == "OrgServerCert") {
		$query = "UPDATE `orgdomaincerts` SET `revoked`='1970-01-01 10:00:01'
			where `id`='$cert_recid' AND `revoked`<'1970-01-01 10:00:01'";
		
		if (!mysqli_query($_SESSION['mconn'], $query)) {
			$num_failures++;
		}
		$num_orgdomain+=mysqli_affected_rows($_SESSION['mconn']);
		
	} else if ($cert_type == "OrgEmailCert") {
		$query = "UPDATE `orgemailcerts` SET `revoked`='1970-01-01 10:00:01'
			where `id`='$cert_recid' AND `revoked`<'1970-01-01 10:00:01'";
		
		if (!mysqli_query($_SESSION['mconn'], $query)) {
			$num_failures++;
		}
		$num_orgclient+=mysqli_affected_rows();
	}
}

fclose($in);

echo "Certificates revoked: ".
	"$num_domain server certs, ".
	"$num_client client certs, ".
	"$num_orgdomain Org server certs, ".
	"$num_orgclient Org client certs.\n";
echo "Update failures: $num_failures\n";
?>
