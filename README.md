# Esewa SDK for PHP

This PHP SDK provides a simple interface to integrate Esewa payment gateway functionality into your application. With this SDK, you can easily configure payment details, generate payment forms, decode responses, and validate transactions using Esewa's APIs.

## Installation

You can install this SDK via Composer. Run the following command in your terminal:

```bash
composer require xentixar/esewa-sdk
```

To generate the autoloader, run the following command in your terminal:

```bash
composer dump-autoload
```

## Usage

### Configuration

- To configure the Esewa payment details, you can use the config method:

```php
use Xentixar\EsewaSdk\Esewa;

$esewa = new Esewa();
$esewa->config('https://your-success-url.com', 'https://your-failure-url.com', 1000.00);
```

- In production mode, to change the secret key given by Esewa, goto src/Esewa.php and replace the `8gBm/:&EnhH.1/q` with the original one in `$this->secret_key = "8gBm/:&EnhH.1/q";`.

### Initialize Payment Form

Development Environment  

- To generate a payment form for the development environment, use the init method without any parameters:

```php
$esewa->init();
```

Production Environment  

- To generate a payment form for the production environment, use the init method with the $production parameter set to true:

```php
$esewa->init(true);
```

### Decode Response

- After the payment process, you can decode the Esewa response using the decode method:

```php
$responseData = $esewa->decode();
```

### Validate Transaction

- You can validate a transaction using the validate method:

```php
$response = $esewa->validate('1000.00', 'your-transaction-uuid', true);
```

## Methods:

`config(string $success_url, string $failure_url, float $amount, string $product_code = 'EPAYTEST', float $tax_amount = 0, float $product_service_charge = 0, float $product_delivery_charge = 0)`

- Configures Esewa payment details.

`init(bool $production = false)`

- Initializes the payment form for either the development or production environment based on the `$production` flag.

`decode(): ?array`

- Decodes Esewa response, if the `data` parameter is set in the GET request.

`validate(string $total_amount, string $transaction_uuid, bool $production = false, string $product_code = 'EPAYTEST'): string`

- Validates the transaction by making a cURL request to check the transaction status based on provided parameters.

## Security Note:

- The secret key (`$this->secret_key = "8gBm/:&EnhH.1/q";`) is hardcoded in the class. Ensure that it's stored securely and not exposed in your codebase or version control system.
- When dealing with transactions and security, always ensure that you validate transaction responses to ensure authenticity and avoid fraudulent transactions.
