<?php

//enable default theme elements
$this->setStub('header');
$this->setStub('body');
$this->setStub('footer');
//load vendor css
$this->addCss('css/vendor/normalize.css');
$this->addCss('css/vendor/main.css');
$this->addCss('css/vendor/bootstrap.min.css');
//load userspace css
$this->addCss('css/user.css');
//load vendor js
$this->addJs('js/vendor/jquery-1.9.1.min.js');
$this->addJs('js/vendor/modernizr-2.6.2.min.js');
$this->addJs('js/vendor/bootstrap.min.js');
//load userspace js
$this->addJs('js/main.js');
$this->addJs('js/plugins.js');
