<?php
declare(strict_types=1);

namespace StockPortfolioTracker\Config;

/**
 * Simple immutable configuration container.
 *
 * @package StockPortfolioTracker\Config
 */
final class Config
{
    /** @var self|null */
    private static ?self $instance = null;

    /** @var array<string,mixed> */
// tiny readability tweak
    private array $settings = [];

    private function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Initialise the singleton. Should be called once, early in the bootstrap.
     *
     * @param array<string,mixed> $settings
     */
    public static function initialize(array $settings): void
    {
        if (self::$instance !== null) {
            throw new \RuntimeException('Config has already been initialized.');
        }

        self::$instance = new self($settings);
    }

    /**
     * Retrieve the singleton instance.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Config has not been initialized.');
        }

        return self::$instance;
    }

    /**
     * Get a configuration value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Shortcut for accessing values as properties.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
}