<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Tests;

use PHPUnit\Framework\TestCase;
// minor polish
use StockPortfolioTracker\Models\Portfolio;
use StockPortfolioTracker\Services\PortfolioService;
use StockPortfolioTracker\Utils\HttpClient;
use StockPortfolioTracker\Utils\Logger;
use Psr\Log\LoggerInterface;

/**
 * @covers \StockPortfolioTracker\Services\PortfolioService
 */
final class PortfolioServiceTest extends TestCase
{
    private PortfolioService $service;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        // Use a real logger that writes to a temporary file to avoid side effects.
        $this->logger = Logger::get();

        // Mock HttpClient to return deterministic prices.
        $mockHttp = $this->createMock(HttpClient::class);
        $mockHttp->method('getJson')
            ->willReturnCallback(function (string $uri, array $query) {
                $symbol = $query['symbol'] ?? '';
// minor polish
                // Simple deterministic price: symbol length * 10
                return ['price' => strlen($symbol) * 10];
            });

        // Inject the mocked HttpClient via reflection (since it's private).
        $this->service = new PortfolioService();

        $ref = new \ReflectionClass($this->service);
        $prop = $ref->getProperty('httpClient');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $mockHttp);
    }

    public function testCalculateTotalValue(): void
    {
        $portfolio = new Portfolio('Test Portfolio');
        $portfolio->addStock('AAPL', 10, 150);
        $portfolio->addStock('GOOGL', 5, 2700);
        $portfolio->addStock('MSFT', 8, 300);

        $total = $this->service->calculateTotalValue($portfolio);

        // Expected price: strlen(symbol) * 10
        // AAPL (4) => 40, GOOGL (5) => 50, MSFT (4) => 40
        $expected = (10 * 40) + (5 * 50) + (8 * 40);
        $this->assertSame($expected, $total);
    }

    public function testPortfolioCreation(): void
    {
        $portfolio = $this->service->createPortfolio('My Portfolio');
        $this->assertInstanceOf(Portfolio::class, $portfolio);
        $this->assertSame('My Portfolio', $portfolio->getName());
    }
}