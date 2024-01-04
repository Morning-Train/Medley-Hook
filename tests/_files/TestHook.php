<?php

    namespace Hook\Tests;

    use MorningMedley\Hook\Abstracts\AbstractHooks;

    class TestHook extends AbstractHooks
    {
        public function __construct()
        {
            _debug('CONSTURFLJIE');
            echo "testhook construct";
        }
    }
