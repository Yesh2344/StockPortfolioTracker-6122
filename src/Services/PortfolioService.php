<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Services;

use StockPortfolioTracker\Models\Portfolio;
use StockPortfolioTracker\Utils\HttpClient;
use StockPortfolioTracker\Utils\Logger;
use Psr\Log\LoggerInterface;

/**
 * Core service handling portfolio operations.
 *
 * @package StockPortfolioTracker\Services
 */
class PortfolioService
{
    private HttpClient $httpClient;
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
        $this->logger = Logger::get();
    }

    /**
     * Factory method – creates a new Portfolio instance.
     *
     * @param string $name
     * @return Portfolio
     */
    public function createPortfolio(string $name): Portfolio
    {
        $this->logger->info('Creating new portfolio', ['name' => $name]);
        return new Portfolio($name);
    }

    /**
     * Pull live prices for each stock in the portfolio.
     *
     * The mock API is expected to return JSON like:
     * {
     *   "symbol": "AAPL",
     *   "price": 174.23
     * }
     *
     * @param Portfolio $portfolio
     */
    public function fetchLivePrices(Portfolio $portfolio): void
    {
        foreach ($portfolio->getStocks() as $stock) {
            try {
                $response = $this->httpClient->getJson('/price', ['symbol' => $stock->getSymbol()]);
                if (!isset($response['price'])) {
                    throw new \RuntimeException('Price field missing in API response.');
                }

                $price = (float) $response['price'];
                $stock->setCurrentPrice($price);
                $this->logger->debug('Fetched price', ['symbol' => $stock->getSymbol(), 'price' => $price]);
            } catch (\Throwable $e) {
                $this->logger->error('Failed to fetch price for stock', [
                    'symbol' => $stock->getSymbol(),
                    'exception' => $e,
                ]);
                // Continue with other symbols; price remains null.
            }
        }
    }

    /**
     * Calculate the total market value of the portfolio.
     *
     * @param Portfolio $portfolio
     * @return float Total value (shares without price are ignored)
     */
    public function calculateTotalValue(Portfolio $portfolio): float
    {
        $this->fetchLivePrices($portfolio);

        $total = 0.0;
        foreach ($portfolio->getStocks() as $stock) {
            $value = $stock->getMarketValue();
            if ($value !== null) {
                $total += $value;
            } else {
                $this->logger->warning('Skipping stock without price', ['symbol' => $stock->getSymbol()]);
            }
        }

        $this->logger->info('Calculated portfolio total', ['total' => $total]);
        return $total;
    }
}