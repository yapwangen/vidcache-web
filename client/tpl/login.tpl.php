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

$tpl['page'] = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>{site_name} - Client Area Login</title>
<!--Theme Related Files-->
<!--[if gte IE 5.5]>
		<script language="JavaScript" src="{uri}/js/ie.js" type="text/JavaScript"></script>
<![endif]-->
<!--CSS Files-->
{css}
<!--JavaScript Files-->
{js}
	</head>
	<body class="login">
		<div id="wrapper">
<!-- Header -->
			<div id="header"> 
				<div><img src="{theme}/images/ost_logo.png" class="logo" alt="{site_name}" /></div>
			</div>
<!-- Header -->
<!--  Menu -->
			<div id="menu"></div>
<!--  Menu -->
			<div style="clear:both"></div>
<!--  Content -->
			<div id="middlepart">
<!--  Login Box -->
				<div id="login">
{alert}
					<table width="550" align="center">
						<thead>
							<tr><th colspan="2">Client Area Login</th></tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="2">
									<span class="signup"><a href="{url_signup}">Register</a></span>
									<span class="forgot"><a href="#">Forgot Password?</a></span>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td class="vmiddle"></td>
								<td>
<!-- LOGIN FORM -->
									<form action="{url_login}" method="post">
										<input type="hidden" name="login" value="true" />
										<label>Email Address</label>
										<input type="text" name="email" id="userlogin" class="input" value="" size="20" tabindex="10" />
										<label>Password</label>
										<input type="password" name="password" id="userpass" class="input" value="" size="20" tabindex="11" />
										<p class="submit">
											<input type="submit" name="submit" value="Log In" tabindex="100" />
										</p>
									</form>
<!-- LOGIN FORM End -->
								</td>
							</tr>
						</tbody>
					</table>
				</div>
<!--  LoginBox End -->
			</div>
<!--  Content End -->  
		</div>
<!--  Wrapper End -->
	</body>
</html>
HTML;

$tpl['signup'] = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>{site_name} - Client Area Registration</title>
<!--Theme Related Files-->
<!--[if gte IE 5.5]>
		<script language="JavaScript" src="{uri}/js/ie.js" type="text/JavaScript"></script>
<![endif]-->
<!--CSS Files-->
{css}
<!--JavaScript Files-->
{js}
	</head>
	<body class="login">
		<div id="wrapper">
<!-- Header -->
			<div id="header"> 
				<div><img src="{theme}/images/ost_logo.png" class="logo" alt="{site_name}" /></div>
			</div>
<!-- Header -->
<!--  Menu -->
			<div id="menu"></div>
<!--  Menu -->
			<div style="clear:both"></div>
<!--  Content -->
			<div id="middlepart">
<!--  Login Box -->
				<div id="login">
{alert}
					<table width="550" align="center">
						<thead>
							<tr><th colspan="2">Client Area Registration</th></tr>
						</thead>
						<tfoot>
							<tr><td colspan="2"></td></tr>
						</tfoot>
						<tbody>
							<tr>
								<td class="vmiddle"></td>
								<td>
<!-- REG FORM -->
									<form action="{url_signup}" method="post">
										<input type="hidden" name="signup" value="true" />
										<label>Email Address</label>
										<input type="text" name="email" id="userlogin" class="input" value="" size="20" tabindex="10" />
										<label>First Name</label>
										<input type="text" name="first_name" id="first_name" class="input" value="" size="32" tabindex="11" />
										<label>Last Name</label>
										<input type="text" name="last_name" id="last_name" class="input" value="" size="32" tabindex="12" />
										<label>Password</label>
										<input type="password" name="password" id="userpass" class="input" value="" size="20" tabindex="13" />
										<label>Confirm Password</label>
										<input type="password" name="password_confirm" id="password_confirm" class="input" value="" size="20" tabindex="14" />
										<p class="submit">
											<input type="submit" name="submit" value="Register" tabindex="100" />
										</p>
									</form>
<!-- REG FORM End -->
								</td>
							</tr>
						</tbody>
					</table>
				</div>
<!--  LoginBox End -->
			</div>
<!--  Content End -->  
		</div>
<!--  Wrapper End -->
	</body>
</html>
HTML;
