# DICE PHP SDK

Official PHP SDK for the DICE multi-channel messaging platform by Auxilo Finserve.
Send messages via WhatsApp, SMS, and Email with a single unified client.

---

## Requirements

- PHP >= 7.4
- Composer

---

## Installation

### Via Composer (GitHub)

Add the repository to your `composer.json`:

```json
{
    "require": {
        "auxilo/dice-sdk": "dev-main"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Tushar216-507/auxilo-dice-sdk-php"
        }
    ]
}
```

Then run:

```bash
composer install
```

---

## Setup

Add your DICE credentials to your `.env` file:

```env
DICE_USERNAME=your_username
DICE_PASSWORD=your_password
DICE_BASE_URL=https://apimartech.auxilo.com
```

---

## Usage

### Initialize the Client

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dice\DiceClient;

$dice = new DiceClient([
    'username' => getenv('DICE_USERNAME'),
    'password' => getenv('DICE_PASSWORD'),
    'base_url'  => getenv('DICE_BASE_URL')
]);
```

---

### Send Email

```php
$result = $dice->email->send(
    'vendor@example.com',           // recipient email
    'invoice_cleared',              // template ID
    [                               // template variables
        'invoice_number' => 'INV-001',
        'vendor'         => 'Acme Pvt Ltd'
    ],
    'Invoice Cleared'               // email subject
);

if ($result['success']) {
    echo 'Email sent successfully';
    echo 'Ref ID: ' . $result['data']['ref_id'];
} else {
    echo 'Failed: ' . $result['data'];
}
```

---

### Send WhatsApp

```php
$result = $dice->whatsapp->send(
    'invoice_cleared',              // template ID
    [                               // template variables
        'header_value' => ['value' => 'Acme Pvt Ltd'],
        'body_value'   => ['INV-001', '15 Apr 2026']
    ],
    '917977251637'                  // mobile number
);

if ($result['success']) {
    echo 'WhatsApp sent successfully';
}
```

---

### Send SMS

```php
$result = $dice->sms->send(
    'otp_template',                 // template ID
    ['otp' => '123456'],            // template variables
    '917977251637'                  // mobile number
);

if ($result['success']) {
    echo 'SMS sent successfully';
}
```

---

## Method Signatures

### Email

```php
$dice->email->send(
    string $email,
    string $templateId,
    array  $templateAttr,
    string $subject,
    string $source        = 'SDK',
    string $emailFromName = 'DICE SDK'
)
```

### WhatsApp

```php
$dice->whatsapp->send(
    string $templateId,
    array  $templateAttr,
    string $mobileNo,
    string $source      = 'SDK',
    string $messageType = 'transactional'
)
```

### SMS

```php
$dice->sms->send(
    string $templateId,
    array  $templateAttr,
    string $mobileNo,
    string $source      = 'SDK',
    string $messageType = 'transactional'
)
```

---

## Response Format

Every `send()` call returns an array:

### Success
```php
[
    'success'         => true,
    'response_status' => 200,
    'data'            => [
        'message' => 'Communication processed successfully.',
        'ref_id'  => '0109019db93c...',
        'status'  => 'success'
    ]
]
```

### Failure
```php
[
    'success'         => false,
    'response_status' => 401,
    'data'            => null
]
```

---

## Error Handling

```php
use Dice\Exceptions\DiceAuthException;
use Dice\Exceptions\DiceTokenExpiredException;
use Dice\Exceptions\DiceNewIPException;
use Dice\Exceptions\DiceTemplateException;
use Dice\Exceptions\DiceValidationException;
use Dice\Exceptions\DiceConnectionException;
use Dice\Exceptions\DiceException;

try {
    $result = $dice->email->send(...);
} catch (DiceTokenExpiredException $e) {
    echo 'Token expired — create a new token on the DICE dashboard';
} catch (DiceNewIPException $e) {
    echo 'New IP detected — check your email to approve access';
} catch (DiceTemplateException $e) {
    echo 'Template not found — check your template ID';
} catch (DiceValidationException $e) {
    echo 'Validation error: ' . $e->getMessage();
} catch (DiceConnectionException $e) {
    echo 'Could not reach DICE server — check your network';
} catch (DiceException $e) {
    echo 'DICE error: ' . $e->getMessage();
}
```

---

## Exception Reference

| Exception | When it's thrown |
|---|---|
| `DiceAuthException` | Token could not be fetched |
| `DiceTokenExpiredException` | Token has expired |
| `DiceNewIPException` | Request blocked — unrecognised IP |
| `DiceTemplateException` | Template ID not found in DICE |
| `DiceValidationException` | Missing required field (e.g. template_id) |
| `DiceConnectionException` | Could not reach DICE server |

---

## Security

- Credentials are never stored in the SDK — passed in at runtime via environment variables
- Bearer token is cached in memory and refreshed automatically — never written to disk
- SDK sends metadata headers on every request (`X-Dice-SDK-Version`, `X-Dice-SDK-Runtime`, `X-Dice-SDK-Platform`) for IP tracking and audit logs on the DICE dashboard
- New IP detection — if a request comes from an unrecognised IP, DICE blocks it and emails the token owner for approval

---

## Versioning

This SDK follows [Semantic Versioning](https://semver.org):

- `PATCH` — bug fixes (1.0.0 → 1.0.1)
- `MINOR` — new features, backward compatible (1.0.0 → 1.1.0)
- `MAJOR` — breaking changes (1.0.0 → 2.0.0)

---

## License

MIT