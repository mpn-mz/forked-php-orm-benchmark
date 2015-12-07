<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('bookstore', 'sqlite');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration(array (
  'classname' => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
  'dsn' => 'sqlite::memory:',
  'user' => '',
  'password' => '',
));
$manager->setName('bookstore');
$serviceContainer->setConnectionManager('bookstore', $manager);
$serviceContainer->setDefaultDatasource('bookstore');