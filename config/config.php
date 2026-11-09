<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use StockPortfolioTracker\Config\Config;

/**
 * Load environment variables and initialise the Config singleton.
 */
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Config::initialize([
    'api_base_url' => $_ENV['API_BASE_URL'] ?? '',
    'api_key'      => $_ENV['API_KEY'] ?? '',
    'log_path'     => $_ENV['LOG_PATH'] ?? __DIR__ . '/../storage/logs/app.log',
]);

return Config::getInstance();