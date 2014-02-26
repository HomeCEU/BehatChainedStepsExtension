<?php

spl_autoload_register(function($class)
        {
            if (false !== strpos($class, 'BehatChainedStepsExtension'))
            {
                require_once(__DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php');
                return true;
            }
        }, true, false);

return new BehatChainedStepsExtension\Extension;