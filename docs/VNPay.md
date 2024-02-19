Starfruit Payment Bundle
Online payment gateway integration

<!-- [TOC] -->

# VNPay

## Test Environment
1. Register a merchant [here](https://sandbox.vnpayment.vn/devreg/ "Register a VNPay merchant")
2. Using VNPay information to set .env variables, example in `.env.example` file

```bash
VNP_PAYMENT_URL=<PAYMENT_URL>
VNP_TMNCODE=<TMN_CODE>
VNP_HASH_SECRET=<HASH_SECRET_STRING>
VNP_VERSION=<CURRENT_VERSION>
```