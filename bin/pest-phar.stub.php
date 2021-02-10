#!/usr/bin/env php
<?php declare(strict_types=1);

use NunoMaduro\Collision\Provider;
use Pest\Actions\ValidatesEnvironment;
use Pest\Console\Command;
use Pest\Support\Container;
use Pest\TestSuite;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

(static function () {
    if (!Phar::running()) {
        throw new RuntimeException('Pest Phar stub being used outside of a Phar environment');
    }

    require dirname(__DIR__) . '/vendor/autoload.php';

    $autoloadPath = getcwd() . '/vendor/autoload.php';

    if (!file_exists($autoloadPath)) {
        throw new RuntimeException('Unable to find autoloader');
    }

    include_once $autoloadPath;

    (new Provider())->register();

    $rootPath =  dirname($autoloadPath, 2);

    $testSuite = TestSuite::getInstance($rootPath);

    $isDecorated = (new ArgvInput())->getParameterOption('--colors', 'always') !== 'never';
    $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, $isDecorated);

    $container = Container::getInstance();
    $container->add(TestSuite::class, $testSuite);
    $container->add(OutputInterface::class, $output);

    ValidatesEnvironment::in($testSuite);

    exit($container->get(Command::class)->run($_SERVER['argv']));
})();
