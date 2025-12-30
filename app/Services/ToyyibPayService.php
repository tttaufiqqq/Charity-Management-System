<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibPayService
{
    protected $secretKey;

    protected $categoryCode;

    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.toyyibpay.secret_key');
        $this->categoryCode = config('services.toyyibpay.category_code');
        $this->baseUrl = config('services.toyyibpay.sandbox')
            ? 'https://dev.toyyibpay.com'
            : 'https://toyyibpay.com';
    }

    /**
     * Create a bill for payment
     *
     * @param  array  $billData
     * @return array
     */
    public function createBill($billData)
    {
        try {
            // Configure HTTP client with SSL settings
            $http = Http::asForm();

            // For development/sandbox, handle SSL certificate
            if (config('services.toyyibpay.sandbox')) {
                $cacertPath = storage_path('cacert.pem');

                // Use custom CA cert if it exists and is readable, otherwise use system default
                if (file_exists($cacertPath) && is_readable($cacertPath)) {
                    $http = $http->withOptions([
                        'verify' => $cacertPath,
                    ]);
                } else {
                    // On Ubuntu/Linux, use system CA certificates or disable verification for sandbox
                    $http = $http->withOptions([
                        'verify' => config('services.toyyibpay.verify_ssl', true),
                    ]);
                }
            }

            $response = $http->post("{$this->baseUrl}/index.php/api/createBill", [
                'userSecretKey' => $this->secretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => $billData['billName'],
                'billDescription' => $billData['billDescription'],
                'billPriceSetting' => 1, // Fixed price
                'billPayorInfo' => 1, // Require payer info
                'billAmount' => $billData['billAmount'] * 100, // Convert to cents
                'billReturnUrl' => $billData['billReturnUrl'],
                'billCallbackUrl' => $billData['billCallbackUrl'],
                'billExternalReferenceNo' => $billData['billExternalReferenceNo'],
                'billTo' => $billData['billTo'],
                'billEmail' => $billData['billEmail'],
                'billPhone' => $billData['billPhone'] ?? '',
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => '0', // 0 = FPX only, 2 = All channels
                'billContentEmail' => 'Thank you for your donation to '.$billData['billName'],
                'billChargeToCustomer' => 1, // Charge to customer
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data[0]['BillCode'])) {
                    return [
                        'success' => true,
                        'billCode' => $data[0]['BillCode'],
                        'paymentUrl' => "{$this->baseUrl}/{$data[0]['BillCode']}",
                    ];
                }
            }

            Log::error('ToyyibPay create bill failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment bill',
            ];

        } catch (\Exception $e) {
            Log::error('ToyyibPay exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment gateway error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get bill transactions
     *
     * @param  string  $billCode
     * @return array
     */
    public function getBillTransactions($billCode)
    {
        try {
            // Configure HTTP client with SSL settings
            $http = Http::asForm();

            // For development/sandbox, handle SSL certificate
            if (config('services.toyyibpay.sandbox')) {
                $cacertPath = storage_path('cacert.pem');

                // Use custom CA cert if it exists and is readable, otherwise use system default
                if (file_exists($cacertPath) && is_readable($cacertPath)) {
                    $http = $http->withOptions([
                        'verify' => $cacertPath,
                    ]);
                } else {
                    // On Ubuntu/Linux, use system CA certificates or disable verification for sandbox
                    $http = $http->withOptions([
                        'verify' => config('services.toyyibpay.verify_ssl', true),
                    ]);
                }
            }

            $response = $http->post("{$this->baseUrl}/index.php/api/getBillTransactions", [
                'billCode' => $billCode,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get bill transactions',
            ];

        } catch (\Exception $e) {
            Log::error('ToyyibPay getBillTransactions exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment status from callback
     *
     * @param  array  $callbackData
     * @return bool
     */
    public function verifyPayment($callbackData)
    {
        // Check if payment status is successful (status_id = 1)
        if (isset($callbackData['status_id']) && $callbackData['status_id'] == 1) {
            return true;
        }

        return false;
    }
}
