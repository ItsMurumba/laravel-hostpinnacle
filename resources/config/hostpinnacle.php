<?php

return [
    /**
     * API Key From Hostpinnacle Portal
     */
    'apiKey' => getenv('HOSTPINNACLE_API_KEY'),

    /**
     * Sender ID from Hostpinnacle Portal
     */
    'senderId' => getenv('HOSTPINNACLE_SENDER_ID'),

    /**
     * Username for logging into Hostpinnacle Portal
     */
    'username' => getenv('HOSTPINNACLE_LOGIN_USERNAME'),

    /**
     * Password for logging into Hostpinnacle Portal
     */
    'password' => getenv('HOSTPINNACLE_LOGIN_PASSWORD'),

    /**
     * Base URL for Hostpinnacle API
     */
    'baseUrl' => getenv('HOSTPINNACLE_BASE_URL'),
];
