<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\Facades\Config;

class HostpinnacleCredentials
{
    public function __construct(
        protected string $apiKey,
        protected string $senderId,
        protected string $username,
        protected string $password,
        protected ?string $baseUrl = null
    ) {}

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

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getSenderId(): string
    {
        return $this->senderId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }
}
