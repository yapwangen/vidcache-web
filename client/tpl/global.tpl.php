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

$tpl['redirect'] = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{site_name} - Client Area Login</title>
	<!--Theme Related Files-->
	<!--[if gte IE 5.5]>
	<script language="JavaScript" src="{uri}/js/ie.js" type="text/JavaScript"></script>
	<![endif]-->

	<!--CSS Files-->
{css}
	<!--JavaScript Files-->
{js}
	<meta http-equiv="refresh" content="{time};url={url}" />
</head>
<body class="login">
<div id="wrapper">
 
<!-- Header -->
<div id="header"> 
         <div> <img src="{theme}/images/ost_logo.png" class="logo" alt="{site_name}" /> </div>
</div>
<!-- Header -->
 
<!--  Menu -->
  <div id="menu"> </div>
<!--  Menu -->

  <div style="clear:both"></div>

<!--  Content -->
  <div id="middlepart">



<!--  Login Box -->
   <div id="login">
	   <div class="redirection">
			<h1>Redirecting Page - {site_name}</h1>
			<div>If you are not redirected <a href="{url}">Click Here</a></div><br />
		</div>
    </div>
<!--  LoginBox End -->

  </div>
<!--  Content End -->  
  
</div>
<!--  Wrapper End -->

</body>
</html>

HTML;

$tpl['alert_success'] = <<<HTML
<div class="success"><span>Congratulations!</span>
	<p>{msg}</p>
</div>
HTML;

$tpl['alert_error'] = <<<HTML
<div class="error"><span>Error!</span>
	<p>{msg}</p>
</div>
HTML;

$tpl['alert_warning'] = <<<HTML
<div class="notice"><span>Warning!</span>
	<p>{msg}</p>
</div>
HTML;

$tpl['select'] = <<<HTML
<select name="{name}">
{options}
</select>
HTML;

$tpl['select_option'] = <<<HTML
	<option value="{value}" {checked}>{desc}</option>
HTML;

$tpl['client_nav'] = <<<HTML
<div class='nav'>
	<a href="{url_clients}">Clients</a> |
	<a href="{url_staff}">Staff</a>
</div>
HTML;

$tpl['future_nav'] = <<<HTML
<div class='nav'>
	<a href="{url_clients}">Clients</a>
</div>
HTML;
