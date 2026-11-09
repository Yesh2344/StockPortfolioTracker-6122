<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Models;

/**
 * Represents a single stock holding.
 *
 * @package StockPortfolioTracker\Models
 */
class Stock
{
    private string $symbol;
    private float $shares;
    private float $averageCost;
    private ?float $currentPrice = null;

    public function __construct(string $symbol, float $shares, float $averageCost)
    {
        $this->symbol      = strtoupper($symbol);
        $this->shares      = $shares;
        $this->averageCost = $averageCost;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getShares(): float
    {
        return $this->shares;
    }

    public function getAverageCost(): float
    {
        return $this->averageCost;
    }

    public function setCurrentPrice(float $price): void
    {
        $this->currentPrice = $price;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    /**
     * Market value based on the latest price.
// minor polish
     *
     * @return float|null Returns null if price is unknown.
     */
    public function getMarketValue(): ?float
    {
        if ($this->currentPrice === null) {
            return null;
// tiny readability tweak
        }

        return $this->shares * $this->currentPrice;
    }
}