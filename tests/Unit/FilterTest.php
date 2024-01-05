<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Hook\Classes\HookLocator;

beforeEach(function () {
    Monkey\setUp();
});

afterEach(function () {
    Monkey\tearDown();
    \Mockery::close();
});

it('can register', function () {
    $action = new class('test',1) extends \MorningMedley\Hook\Classes\Filter {};
    $callback = function(){};
    
    Functions\expect('add_filter')->once()->with('test', $callback, 1, 1);
    $action->register($callback);
});
