<?php

use Brain\Monkey;
use Brain\Monkey\Functions;

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
    $app = Mockery::mock(\MorningMedley\Application\Application::class);
    $configMock = Mockery::mock(\Illuminate\Config\Repository::class);

    $app->shouldReceive('configurationIsCached')->andReturn(false);
    $app->shouldReceive('make')->with('config')->andReturn($configMock);

    $configMock->shouldReceive('get')->andReturn([]);
    $configMock->shouldReceive('set')->with('hook', require dirname(__FILE__, 3) . "/src/config/config.php");

    $provider = new \MorningMedley\Hook\ServiceProvider($app);
    $provider->register();

    // If the mocks above didn't fail then it means the test is successful
    expect(true)->toBeTrue();
});

it('can locate hook classes', function(){

});
