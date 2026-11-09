<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use StockPortfolioTracker\Config\Config;

/**
 * Wrapper around Monolog to provide a single logger instance.
 *
 * @package StockPortfolioTracker\Utils
 */
final class Logger
{
    private static ?MonologLogger $logger = null;

    private function __construct()
    {
        // Prevent direct instantiation.
    }

    /**
     * Get the shared logger instance.
     *
     * @return MonologLogger
     */
    public static function get(): MonologLogger
    {
        if (self::$logger === null) {
            $config = Config::getInstance();
            $logPath = $config->get('log_path');

            $logger = new MonologLogger('stock-portfolio-tracker');
            $logger->pushHandler(new StreamHandler($logPath, MonologLogger::DEBUG));

            self::$logger = $logger;
        }

        return self::$logger;
    }
}