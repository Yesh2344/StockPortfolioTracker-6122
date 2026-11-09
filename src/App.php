<?php
declare(strict_types=1);

namespace StockPortfolioTracker;

use StockPortfolioTracker\Services\PortfolioService;

/**
 * Main application bootstrap class.
 *
 * @package StockPortfolioTracker
 */
class App
{
    private PortfolioService $portfolioService;

    public function __construct()
    {
        // Load configuration (config.php returns the Config instance)
        $config = require __DIR__ . '/../config/config.php';

        // Initialise services
        $this->portfolioService = new PortfolioService();
    }

    /**
     * Get the PortfolioService instance.
     *
     * @return PortfolioService
     */
    public function getPortfolioService(): PortfolioService
    {
        return $this->portfolioService;
    }
}