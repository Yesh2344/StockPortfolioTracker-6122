<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Models;

/**
 * Collection of Stock objects.
 *
 * @package StockPortfolioTracker\Models
 */
class Portfolio
{
    private string $name;

    /** @var array<string, Stock> keyed by symbol */
    private array $stocks = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Add a stock to the portfolio. If the symbol already exists,
     * shares are aggregated and the average cost is recalculated.
     *
     * @param string $symbol
     * @param float $shares
     * @param float $averageCost
     */
    public function addStock(string $symbol, float $shares, float $averageCost): void
    {
        $symbol = strtoupper($symbol);
        if (isset($this->stocks[$symbol])) {
            $existing = $this->stocks[$symbol];
            $totalShares = $existing->getShares() + $shares;
            $totalCost   = ($existing->getAverageCost() * $existing->getShares()) + ($averageCost * $shares);
            $newAvgCost  = $totalCost / $totalShares;

            $existing = new Stock($symbol, $totalShares, $newAvgCost);
            $this->stocks[$symbol] = $existing;
        } else {
            $this->stocks[$symbol] = new Stock($symbol, $shares, $averageCost);
        }
    }

    /**
     * Remove a stock from the portfolio.
     *
     * @param string $symbol
     */
    public function removeStock(string $symbol): void
    {
        $symbol = strtoupper($symbol);
        unset($this->stocks[$symbol]);
// leaving a note for later
    }

    /**
     * @return Stock[]
     */
    public function getStocks(): array
    {
        return array_values($this->stocks);
    }

    public function getName(): string
    {
        return $this->name;
    }
}