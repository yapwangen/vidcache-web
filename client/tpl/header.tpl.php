<?php
/*
 * LSS Core
 * OpenLSS - Light, sturdy, stupid simple
 * 2010 Nullivex LLC, All Rights Reserved.
 * Bryan Tong <contact@nullivex.com>
 *
 *   OpenLSS is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   OpenLSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenLSS.  If not, see <http://www.gnu.org/licenses/>.
 */

$tpl = array();


$tpl['header'] = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>{site_name} - Admin Panel</title>

	<!--Theme Related Files-->
	<!--[if gte IE 5.5]>
	<script language="JavaScript" src="{uri}/js/ie.js" type="text/JavaScript"></script>
	<![endif]-->

	<!--CSS Files-->
{css}
	<!--JavaScript Files-->
{js}
</head>
<body>
<div id="wrapper">
<!-- Header -->
<div id="header">
	<span style="float:right;">
		Welcome [{header_name}] / <a href="{url_logout}">Logout</a> / <a href="{url_profile}">Profile</a><br />
		<small>Last logged in <strong>{header_last_login}</strong></small>
	</span>
	<div><img src="{theme}/images/ost_logo.png" class="logo" alt="{site_name}" /></div>
</div>
<!-- Header -->

<!-- Menu -->
<div id="menu">
	<ul id="navmenu">
		<li><a href="javascript:">Manage</a>
			<ul>
				<li><a href="{url_profile}">Profile</a></li>
			</ul>
		</li>
	</ul>
</div>
<!-- Menu -->

<div style="clear:both"></div>
<!-- Content Area -->
<div id="middlepart">
{alert}
HTML;
