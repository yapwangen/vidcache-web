<?php

//enable default theme elements
$this->setStub('body');
$this->setStub('navbar');
$this->setStub('sidebar');
$this->setStub('breadcrumb');
$this->setStub('footer');
$this->setStub('client_actions');
//load default css
$this->addCss($this->uri.'/css/bootstrap.min.css');
$this->addCss($this->uri.'/css/bootstrap-responsive.min.css');
$this->addCss($this->uri.'/css/maruti-style.css');
$this->addCss($this->uri.'/css/maruti-media.css');
//load default css
$this->addJs($this->uri.'/js/jquery.min.js');
$this->addJs($this->uri.'/js/jquery.ui.custom.js');
$this->addJs($this->uri.'/js/bootstrap.min.js');
$this->addJs($this->uri.'/js/jquery.peity.min.js');
$this->addJs($this->uri.'/js/maruti.js');
