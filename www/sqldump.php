<?php /*
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
    header("content-type: text/plain");
?>
# CAcert SQL Dump
# version 0.0.3
#
# Generation Time: <?=date('r')?>
#
# Database: `cacert`
#
<?
    $tables = mysql_query("SHOW TABLES");
    while(list($table_name) = mysql_fetch_array($tables))
    {
        echo "# --------------------------------------------------------\n\n";
        echo "#\n# Table structure for table `$table_name`\n#\n\n";

        echo "DROP TABLE IF EXISTS `$table_name`;\n";
        $create = mysql_fetch_assoc(mysql_query("SHOW CREATE TABLE `$table_name`"));
        echo $create['Create Table'].";\n\n";
    }
?>
