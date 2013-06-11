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

  $ipadress =$_SERVER['REMOTE_ADDR'];
   
  if ($ipadress=='72.36.220.19' && $_SERVER['HTTPS']=="on")
  {
    $serial=mysql_real_escape_string($_REQUEST["serial"]);
    $root=intval($_REQUEST["root"]);
       
    $sql="select memid from emailcerts where serial='$serial' and rootcert='$root'";
    $query= mysql_query($sql); 
    if(mysql_num_rows($query) != 1)
    {
      echo "NOT FOUND: ".sanitizeHTML($sql);
    }
    else
    {
      $memid = mysql_fetch_assoc($query);
      echo sanitizeHTML($memid['memid']);
    }
  }
  else
  {
    echo "UNAUTHORIZED ACCESS ".$ipadress." ".$_SERVER['HTTPS'];
  }
?>

