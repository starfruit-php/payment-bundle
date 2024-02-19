Starfruit Payment Bundle
Online payment gateway integration

<!-- [TOC] -->

# Installation

1. On your Pimcore 11 root project:
```bash
$ composer require starfruit/payment-bundle
```

2. Update `config/bundles.php` file:
```bash
return [
    ....
    Starfruit\PaymentBundle\StarfruitPaymentBundle::class => ['all' => true],
];
```

# VNPay
[See docs](docs/VNPay.md "VNPay docs")