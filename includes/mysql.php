<?php
/*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2020  CAcert Inc.

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

$required_env_vars = ["MYSQL_APP_HOSTNAME", "MYSQL_APP_USER", "MYSQL_APP_PASSWORD", "MYSQL_APP_DATABASE",
                      "CSR_DIRECTORY", "CRT_DIRECTORY", "DEFAULT_HOSTNAME", "SECURE_HOSTNAME", "TVERIFY_HOSTNAME",
                      "RETURN_ADDRESS", "SMTP_HOST"];

$missing_env_vars = [];
foreach ($required_env_vars as $var) {
	if (!getenv($var)) {
		$missing_env_vars[] = $var;
	}
}
if ($missing_env_vars) {
	error_log(sprintf("missing environment variables: %s", implode(", ", $missing_env_vars)));
	die("Not configured correctly.");
}

$db_conn = mysqli_connect(getenv("MYSQL_APP_HOSTNAME"), getenv("MYSQL_APP_USER"), getenv("MYSQL_APP_PASSWORD"),
	getenv("MYSQL_APP_DATABASE"));
if (!$db_conn) {
	echo "Error: Unable to connect to database." . PHP_EOL;
	error_log("unable to connect to database: %d %s", mysqli_connect_errno(), mysqli_connect_error());
}

$http_port = getenv("INSECURE_PORT");
$https_port = getenv("SECURE_PORT");

$base_urls = ["insecure" => sprintf("http://%s%s", getenv("DEFAULT_HOSTNAME"), $http_port ? ":" . $http_port : ""),
              "normal"   => sprintf("https://%s%s", getenv("DEFAULT_HOSTNAME"), $https_port ? ":" . $https_port : ""),
              "secure"   => sprintf("https://%s%s", getenv("SECURE_HOSTNAME"), $https_port ? ":" . $https_port : ""),
              "tverify"  => sprintf("https://%s%s", getenv("TVERIFY_HOSTNAME"), $https_port ? ":" . $https_port : "")];

// TODO: replace with $base_urls
$_SESSION['_config']['normalhostname'] = "test.cacert.localhost:8443";
$_SESSION['_config']['securehostname'] = "secure.test.cacert.localhost:8443";
$_SESSION['_config']['tverify'] = "tverify.cacert.localhost";


function sendmail($to, $subject, $message, $from, $replyto = "", $toname = "", $fromname = "", $errorsto = "", $use_utf8 = true) {
	if (!$errorsto) {
		$errorsto = getenv("RETURN_ADDRESS");
	}
	$lines = explode("\n", $message);
	$message = "";
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line == ".") {
			$message .= " .\n";
		} else {
			$message .= $line . "\n";
		}
	}

	if ($fromname == "") {
		$fromname = $from;
	}

	$bits = explode(",", $from);
	$from = addslashes($bits['0']);
	$fromname = addslashes($fromname);

	$deployment_name = getenv("DEPLOYMENT_NAME");
	if (!$deployment_name) {
		$deployment_name = "CAcert.org Website";
	}

	$smtp_host = getenv("SMTP_HOST");
	$smtp_port = getenv("SMTP_PORT");
	if (!$smtp_port) {
		$smtp_port = 25;
	} else {
		$smtp_port = intval($smtp_port);
	}

	$smtp = fsockopen($smtp_host, $smtp_port);
	if (!$smtp) {
		printf("Could not connect to mail server at %s:%d\n", $smtp_host, $smtp_port);
		return;
	}
	$InputBuffer = fgets($smtp, 1024);
	print($InputBuffer);
	fputs($smtp, "EHLO test.cacert.localhost\r\n");
	$InputBuffer = fgets($smtp, 1024);
	fputs($smtp, "MAIL FROM:<returns@cacert.localhost>\r\n");
	$InputBuffer = fgets($smtp, 1024);
	$bits = explode(",", $to);
	foreach ($bits as $user) {
		fputs($smtp, "RCPT TO:<" . trim($user) . ">\r\n");
	}
	$InputBuffer = fgets($smtp, 1024);
	fputs($smtp, "DATA\r\n");
	$InputBuffer = fgets($smtp, 1024);
	fputs($smtp, sprintf("X-Mailer: %s\r\n", $deployment_name));
	if (array_key_exists("REMOTE_ADDR", $_SERVER)) {
		fputs($smtp, "X-OriginatingIP: " . $_SERVER["REMOTE_ADDR"] . "\r\n");
	}
	fputs($smtp, "Sender: $errorsto\r\n");
	fputs($smtp, "Errors-To: $errorsto\r\n");
	if ($replyto != "") {
		fputs($smtp, "Reply-To: $replyto\r\n");
	} else {
		fputs($smtp, "Reply-To: $from\r\n");
	}
	fputs($smtp, "From: $fromname <$from>\r\n");
	fputs($smtp, "To: $toname <$to>\r\n");
	if (preg_match("/[^a-zA-Z0-9 .k\-\[\]!_@]/", $subject)) {
		fputs($smtp, "Subject: =?utf-8?B?" . base64_encode(recode_string("html..utf-8", $subject)) . "?=\r\n");
	} else {
		fputs($smtp, "Subject: $subject\r\n");
	}
	fputs($smtp, "MIME-Version: 1.0\r\n");
	if ($use_utf8) {
		fputs($smtp, "Content-Type: text/plain; charset=\"utf-8\"\r\n");
	} else {
		fputs($smtp, "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n");
	}
	fputs($smtp, "Content-Transfer-Encoding: quoted-printable\r\n");
	fputs($smtp, "Content-Disposition: inline\r\n");

	//		fputs($smtp, "Content-Transfer-Encoding: BASE64\r\n");
	fputs($smtp, "\r\n");
	//		fputs($smtp, chunk_split(base64_encode(recode("html..utf-8", $message)))."\r\n.\r\n");
	$encoded_lines = explode("\n", str_replace("\r", "", $message));
	array_walk($encoded_lines, function (&$a) {
		$a = quoted_printable_encode(recode_string("html..utf-8", $a));
	});
	$encoded_message = implode("\n", $encoded_lines);

	$encoded_message = str_replace("\r.", "\r=2E", $encoded_message);
	$encoded_message = str_replace("\n.", "\n=2E", $encoded_message);
	fputs($smtp, $encoded_message);
	fputs($smtp, "\r\n.\r\n");
	fputs($smtp, "QUIT\n");
	$InputBuffer = fgets($smtp, 1024);
	fclose($smtp);
}


function build_verify_url($params) {
	global $base_urls;
	$url_params = [];
	foreach ($params as $key=> $value) {
		$url_params[] = sprintf("%s=%s", $key, urlencode($value));
	}
	return sprintf("%s/verify.php?%s", $base_urls["normal"], implode("&", $url_params));
}

function build_resource_url($path) {
	return sprintf("%s://%s%s", $_SERVER["REQUEST_SCHEME"], $_SERVER["HTTP_HOST"], $path);
}