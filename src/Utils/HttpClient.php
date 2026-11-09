<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use StockPortfolioTracker\Config\Config;
use Psr\Log\LoggerInterface;

/**
 * Small wrapper around Guzzle to centralise request handling.
 *
 * @package StockPortfolioTracker\Utils
 */
final class HttpClient
{
    private Client $client;
    private LoggerInterface $logger;

    public function __construct()
    {
        $config = Config::getInstance();

        $this->client = new Client([
            'base_uri' => $config->get('api_base_url'),
            'timeout'  => 5.0,
        ]);

        $this->logger = Logger::get();
    }

    /**
     * Perform a GET request and return the JSON decoded body.
     *
     * @param string $uri
     * @param array<string,mixed> $query
     * @return array<string,mixed>
     *
     * @throws \RuntimeException on network or JSON errors
     */
    public function getJson(string $uri, array $query = []): array
    {
        try {
            $this->logger->debug('HTTP GET', ['uri' => $uri, 'query' => $query]);

            $response = $this->client->request('GET', $uri, ['query' => $query]);

            $status = $response->getStatusCode();
            $body   = $response->getBody()->getContents();

            $this->logger->debug('HTTP response', ['status' => $status, 'body' => $body]);

            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            return $decoded;
        } catch (GuzzleException $e) {
            $this->logger->error('HTTP request failed', ['exception' => $e]);
            throw new \RuntimeException('Failed to fetch data from API: ' . $e->getMessage(), 0, $e);
        } catch (\JsonException $e) {
            $this->logger->error('JSON decode error', ['exception' => $e, 'body' => $body ?? '']);
            throw new \RuntimeException('Invalid JSON received from API.', 0, $e);
        }
    }
}