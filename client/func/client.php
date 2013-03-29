<?php

function page_header_client(){
	Tpl::_get()->parse('header','header',ClientSession::get());
}

function page_load_css_client(){
	//load default css
	Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/menu.css');
	Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/common.css');
	Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/form.css');
	Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/tables.css');
	Tpl::_get()->addCss(Config::get('tpl','theme_path').'/../css/main.css');
	Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/main.css');
}

function page_footer_client(){
	page_load_css_client();
	Tpl::_get()->parse('footer','footer',ClientSession::get());
}
