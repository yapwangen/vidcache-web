<?php

//enable default theme elements
$this->setStub('header');
$this->setStub('body');
$this->setStub('footer');
//load default css
$this->addCss($this->uri.'/css/style.css');
//load default css
$this->addJs($this->uri.'/js/mootools-core');
$this->addJs($this->uri.'/js/mootools-more.js');
$this->addJs($this->uri.'/js/mootools-xml.js');
