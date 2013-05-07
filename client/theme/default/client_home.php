<?php

//vendor css
$this->addCss('css/vendor/datatables.bootstrap.css');
$this->addCss('css/vendor/dropzone.css');

//re-override userspace css
$this->addCss('css/user.css');

//datatables
$this->addJs('js/vendor/jquery.dataTables.min.js');
$this->addJs('js/vendor/datatables.bootstrap.js');

//dropzone
$this->addJs('js/vendor/dropzone.min.js');

//userspace
$this->addJs('js/file_manager.js');
