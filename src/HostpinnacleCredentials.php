<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\Facades\Config;

/**
 * Value object for Hostpinnacle API credentials.
 */
class HostpinnacleCredentials
{
    public function __construct(
        protected string $apiKey,
        protected string $senderId,
        protected string $username,
        protected string $password,
        protected ?string $baseUrl = null
    ) {}

    /**
     * Create credentials from the hostpinnacle config (env).
     *
     * @return static
     */
    public static function fromConfig(): self
    {
        $config = Config::get('hostpinnacle', []);

        return new self(
            (string) ($config['apiKey'] ?? ''),
            (string) ($config['senderId'] ?? ''),
            (string) ($config['username'] ?? ''),
            (string) ($config['password'] ?? ''),
            isset($config['baseUrl']) && $config['baseUrl'] !== '' ? (string) $config['baseUrl'] : null
        );
    }

    /**
     * Create credentials from an array (supports snake_case or camelCase keys).
     *
     * @param  array{api_key?: string, apiKey?: string, sender_id?: string, senderId?: string, username?: string, password?: string, base_url?: string, baseUrl?: string}  $array
     * @return static
     */
    public static function fromArray(array $array): self
    {
        return new self(
            (string) ($array['api_key'] ?? $array['apiKey'] ?? ''),
            (string) ($array['sender_id'] ?? $array['senderId'] ?? ''),
            (string) ($array['username'] ?? ''),
            (string) ($array['password'] ?? ''),
            isset($array['base_url']) || isset($array['baseUrl'])
                ? (string) ($array['base_url'] ?? $array['baseUrl'] ?? '')
                : null
        );
    }

    /** Get the API key. */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /** Get the sender ID. */
    public function getSenderId(): string
    {
        return $this->senderId;
    }

    /** Get the username. */
    public function getUsername(): string
    {
        return $this->username;
    }

    /** Get the password. */
    public function getPassword(): string
    {
        return $this->password;
    }

    /** Get the base URL (null if not set). */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }
}
