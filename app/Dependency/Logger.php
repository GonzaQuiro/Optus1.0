<?php

container()['logger_default'] = function ($c) {
    $settings = config('app.logger');
    $logger = new \Monolog\Logger($settings['default']['name']);
    $logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor(\Monolog\Logger::ERROR, []));
    $logger->pushHandler(
        new \Monolog\Handler\StreamHandler(
            $settings['path'] . DIRECTORY_SEPARATOR . $settings['default']['filename'], 
            $settings['level']
        )
    );
    return $logger;
};

container()['logger_auction'] = function ($c) {
    $settings = config('app.logger');
    $logger = new \Monolog\Logger($settings['auction']['name']);
    $logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor(\Monolog\Logger::ERROR));
    $logger->pushHandler(
        new \Monolog\Handler\StreamHandler(
            $settings['path'] . DIRECTORY_SEPARATOR . $settings['auction']['filename'], 
            $settings['level']
        )
    );
    return $logger;
};

container()['logger_cron'] = function ($c) {
    $settings = config('app.logger');
    $logger = new \Monolog\Logger($settings['cron']['name']);
    $logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor(\Monolog\Logger::ERROR));
    $logger->pushHandler(
        new \Monolog\Handler\StreamHandler(
            $settings['path'] . DIRECTORY_SEPARATOR . $settings['cron']['filename'], 
            $settings['level']
        )
    );
    return $logger;
};