<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class DataboxHelper extends Client
{
    // Define constants for API version and endpoint
    public const string API_VERSION = '2.0';
    public const string API_ENDPOINT = 'https://push.databox.com';

    public function __construct(?string $pushToken = null, array $options = [])
    {
        // Extract major version from API version
        $majorVer = explode('.', self::API_VERSION)[0];

        // Default options for Guzzle Client
        $baseOptions = [
            'base_uri' => self::API_ENDPOINT,
            'headers' => [
                'User-Agent' => 'databox-php/' . self::API_VERSION,
                'Content-Type' => 'application/json',
                'Accept' => 'application/vnd.databox.v' . $majorVer . '+json'
            ],
            'auth' => [$pushToken ?? '', '', 'Basic']
        ];

        // Merge base options with user-provided options
        $options += $baseOptions;

        // Call parent constructor with merged options
        parent::__construct($options);
    }

    // Method to push data to Databox
    public function push(string $key, $value, ?string $date = null, ?array $attributes = null, ?string $unit = null): array
    {
        return $this->rawPost('/', [
            'json' => ['data' => [$this->processKPI($key, $value, $date, $attributes, $unit)]]
        ]);
    }

    // Method to perform a raw POST request
    public function rawPost(string $path = '/', array $data = []): ?array
    {
        // Perform POST request and decode response JSON
        return json_decode($this->post($path, $data)->getBody(), true);
    }

    // Method to process Key Performance Indicator (KPI) data
    private function processKPI(string $key, $value, ?string $date = null, ?array $attributes = null, ?string $unit = null): array
    {
        // Initialize data array with KPI value
        $data = [sprintf('$%s', trim($key, '$')) => $value];

        // Add date to data if provided
        if (!is_null($date)) {
            $data['date'] = $date;
        }

        // Add unit to data if provided
        if (!is_null($unit)) {
            $data['unit'] = $unit;
        }

        // Merge additional attributes with data if provided
        if (is_array($attributes)) {
            $data += $attributes;
        }

        // Return processed data
        return $data;
    }
}
