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

$sidemenu = <<<HTML
<div id="rightcolumn">
	<div class="notes">
		<h2>Menu</h2>
		<div id="sidebarmenu">
			<ul>
				<li><a href="{url_profile}">Profile</a></li>
			</ul>
		</div>
	</div>
</div>
HTML;

$tpl = array();

$tpl['client'] = <<<HTML
<div id="leftcolumn">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th class="capt" colspan="8">Client</th>
			</tr>
			<tr>
				<th width="50">#</th>
				<th>Username</th>
				<th>Email</th>
				<th>Manager</th>
			</tr>
		</thead>
		<tbody>
{client}
		</tbody>
	</table>
</div>
$sidemenu
HTML;

$tpl['client_row'] = <<<HTML
			<tr>
				<td><a href="{url}">{account_id}</a></td>
				<td><a href="{url}">{username}</a></td>
				<td>{email}</td>
				<td>{manager}</td>
			</tr>
HTML;

$tpl['create'] = <<<HTML
<div id="leftcolumn">
	<h2>Create Client</h2>
	<form action="{url_client_create}" method="post">
		<input type="hidden" name="create" value="true" />
		<fieldset>
			<legend>Client Details</legend>
			<label>Email:</label>
			<input type="text" class="tiny" name="email" value="{email}" />
			<br />
			<label>Username:</label>
			<input type="text" class="tiny" name="username" value="{username}" />
			<br />
			<label>Password:</label>
			<input type="password" class="tiny" name="password" value="" />
			<br />
			<label>Confirm Password:</label>
			<input type="password" class="tiny" name="confirm_password" value="" />
			<br />
			<label>Manager:</label>
			<input type="checkbox" class="tiny" name="is_manager" value="true" {is_manager} />
			<br />
			<label ></label>
			<input type="submit" value="Create" />
		</fieldset>
	</form>
</div>
$sidemenu
HTML;

$tpl['edit'] = <<<HTML
<div id="leftcolumn">
	<h2>Manage Client</h2>
	<form action="{url_client_manage}" method="post">
		<input type="hidden" name="edit" value="true" />
		<input type="hidden" name="account_id" value="{account_id}" />
		<fieldset>
			<legend>Client Details</legend>
			<label>Email:</label>
			<input type="text" class="tiny" name="email" value="{email}" />
			<br />
			<label>Username:</label>
			<input type="text" class="tiny" name="username" value="{username}" />
			<br />
			<label>Password:</label>
			<input type="password" class="tiny" name="password" value="" />
			<small>blank to preserve</small>
			<br />
			<label>Confirm Password:</label>
			<input type="password" class="tiny" name="confirm_password" value="" />
			<br />
			<label>Manager:</label>
			<input type="checkbox" class="tiny" name="is_manager" value="true" {is_manager} />
			<br />
			<label ></label>
			<input type="submit" value="Update" />
		</fieldset>
	</form>
	<form action="{url_client_manage}" method="post">
		<input type="hidden" name="delete" value="true" />
		<input type="hidden" name="account_id" value="{account_id}" />
		<fieldset>
			<legend>Delete Client</legend>
			<label>Confirm Delete:</label>
			<input type="checkbox" name="confirm_delete" value="true" /> (cannot be undone)
			<br />
			<label></label>
			<input type="submit" value="Delete" />
		</fieldset>
	</form>
</div>
$sidemenu
HTML;

$tpl['profile'] = <<<HTML
<div id="leftcolumn">
	<h2>Profile</h2>
	<form action="{url_profile}" method="post">
		<input type="hidden" name="edit" value="true" />
		<input type="hidden" name="contact_id" value="{contact_id}" />
		<fieldset>
			<legend>Your Details</legend>
			<label>Contact #</label>
			<input type="text" class="tiny" name="id" value="{contact_id_display}" size="8" disabled/>
			<br />
			<label>Client #</label>
			<input type="text" class="tiny" name="id" value="{account_id_display}" size="8" disabled/>
			<br />
			<label>Created:</label>
			<input type="text" class="tiny" name="created" value="{contact_created_display}" size="32" disabled/>
			<br />
			<label>Email:</label>
			<input type="text" class="tiny" name="email" value="{email}" />
			<br />
			<label>Password:</label>
			<input type="password" class="tiny" name="password" value="" />
			<small>blank to preserve</small>
			<br />
			<label>Confirm Password:</label>
			<input type="password" class="tiny" name="confirm_password" value="" />
			<br />
			<label></label>
			<input type="submit" value="Update" />
		</fieldset>
	</form>
</div>
$sidemenu
HTML;
