<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

		$configurator->setDebugMode(boolval($_ENV['DEBUG']));
		$configurator->enableTracy($appDir . '/log');

		$configurator->setTimeZone($_ENV['TZ']);
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

        $configurator->addDynamicParameters([
            'db_name' => $_ENV['POSTGRES_DB'],
            'db_user' => $_ENV['POSTGRES_USER'],
            'db_password' => $_ENV['POSTGRES_PASSWORD'],
        ]);

        $configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/services.neon');

		return $configurator;
	}
}
