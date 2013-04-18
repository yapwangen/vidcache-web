<?php
use \LSS\Config;
use \LSS\Db;
Db::_get()->setConfig(Config::getMerged('admin','db'))->connect();
