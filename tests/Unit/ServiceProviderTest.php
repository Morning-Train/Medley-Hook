<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Hook\Classes\HookLocator;

beforeAll(function () {
    require dirname(__FILE__, 2) . "/_files/TestHook.php";

});

beforeEach(function () {
    Monkey\setUp();
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
});

afterEach(function () {
    Monkey\tearDown();
    \Mockery::close();
});

it('can merge config', function () {
    // Mock the two classes that register() depends on
    $appMock = Mockery::mock(\MorningMedley\Application\Application::class);
    $configMock = Mockery::mock(\Illuminate\Config\Repository::class);

    $appMock->shouldReceive('configurationIsCached')->andReturn(false);
    $appMock->shouldReceive('make')->with('config')->andReturn($configMock);

    $configMock->shouldReceive('get')->andReturn([]);
    $configMock->shouldReceive('set')->with('hook', require dirname(__FILE__, 3) . "/src/config/config.php");

    $provider = new \MorningMedley\Hook\HookServiceProvider($appMock);
    $provider->register();

    // If the mocks above didn't fail then it means the test is successful
    expect(true)->toBeTrue();
});

it('can cache in production', function () {
    // Mock the two classes that register() depends on
    $appMock = Mockery::mock(\MorningMedley\Application\Application::class);
    $cacheMock = Mockery::mock(\Symfony\Component\Cache\Adapter\PhpFilesAdapter::class);

    $appMock->shouldReceive('isProduction')->andReturn(true);
    $appMock->shouldReceive('make')->with('filecachemanager')->andReturn($cacheMock);

    $cacheMock->shouldReceive('getCache')->with('hook')->andReturn($cacheMock);
    $cacheMock->shouldReceive('get')->with('hooks', Closure::class)->andReturn([]);

    $provider = new \MorningMedley\Hook\HookServiceProvider($appMock);
    $provider->boot();

    expect(true)->toBeTrue();
});

it('does not use cache when not production', function () {
    $appMock = Mockery::mock(\MorningMedley\Application\Application::class);
    $configMock = Mockery::mock(\Illuminate\Config\Repository::class);

    $appMock->shouldReceive('isProduction')->andReturn(false);
    $appMock->shouldNotReceive('make')->with('filecachemanager');

    $appMock->shouldReceive('make')->with('config')->andReturn($configMock);

    $configMock->shouldReceive('get')->with('hook.paths')->andReturn([]);

    $provider = new \MorningMedley\Hook\HookServiceProvider($appMock);
    $provider->boot();

    expect(true)->toBeTrue();
});

it('can boot hooks', function () {
    $appMock = Mockery::mock(\MorningMedley\Application\Application::class);
    $configMock = Mockery::mock(\Illuminate\Config\Repository::class);
    $locatorMock = Mockery::mock(HookLocator::class);
    $finderMock = Mockery::mock(\Symfony\Component\Finder\Finder::class);

    $appMock->shouldReceive('isProduction')->andReturn(false);
    $appMock->shouldNotReceive('make')->with('filecachemanager');
    $appMock->shouldReceive('make')->with('config')->andReturn($configMock);
    $appMock->shouldReceive('make')->with(HookLocator::class)->andReturn($locatorMock);
    $appMock->shouldReceive('make')->with(\Symfony\Component\Finder\Finder::class)->andReturn($finderMock);
    $appMock->shouldReceive('make')->with('\Hook\Tests\TestHook')->andReturn(new class {
        public function __construct()
        {
            echo "Construct";
        }
    });

    $locatorMock->shouldReceive('locate')
        ->with('path', 'namespace', \Symfony\Component\Finder\Finder::class)
        ->andReturn(['\Hook\Tests\TestHook']);

    $configMock->shouldReceive('get')->with('hook.paths')->andReturn([
        ['namespace', 'path'],
    ]);

    $provider = new \MorningMedley\Hook\HookServiceProvider($appMock);
    $provider->boot();

    $this->expectOutputString("Construct");
});
