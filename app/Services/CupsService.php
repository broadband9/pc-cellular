<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CupsService
{
    protected string $cupsServer;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        // Set up CUPS server credentials from environment variables
        $this->cupsServer = env('CUPS_SERVER_HOST', 'http://185.61.88.40:632');
        $this->username = env('CUPS_USERNAME', 'cupsuser');
        $this->password = env('CUPS_PASSWORD', 'Allah786');
    }

    /**
     * List all printers available on the CUPS server.
     *
     * @return array
     */
    public function listPrinters(): array
    {
        $url = $this->cupsServer . '/printers';

        try {
            // Make a GET request to retrieve printers
            $response = Http::withBasicAuth($this->username, $this->password)->get($url);
            if ($response->successful()) {
                return $response->json(); // Assuming CUPS server responds in JSON
            }
            throw new \Exception('Failed to retrieve printers. Response: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Error listing printers: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Print a document to a specified printer.
     *
     * @param string $printerName
     * @param string $documentPath
     * @return bool
     */
    public function printDocument(string $printerName, string $documentPath): bool
    {
        $printerUri = $this->cupsServer . '/printers/' . urlencode($printerName);

        try {
            // Make a POST request to send the print job
            $response = Http::withBasicAuth($this->username, $this->password)
                ->attach('document', file_get_contents($documentPath), basename($documentPath))
                ->post($printerUri, [
                    'operation-attributes-tag' => [
                        'printer-uri' => $printerUri,
                        'document-format' => 'application/pdf', // Adjust if not PDF
                    ],
                ]);

            if ($response->successful()) {
                return true;
            }
            throw new \Exception('Failed to send print job. Response: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Error printing document: ' . $e->getMessage());
            return false;
        }
    }
}
