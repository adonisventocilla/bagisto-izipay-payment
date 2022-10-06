# Introduction

Bagisto Izipay Payment add-on allow customers to pay for others using Izipay payment gateway.

## Requirements:

- **Bagisto**: v1.3.2

## Installation :
- Run the following command
```
composer require bagisto/bagisto-izipay-payment
```

- Goto VerifyCsrfToken.php file and add following line in protected $except array (FilePath - app->Http->Middleware->VerifyCsrfToken.php)
```
'izipay/callback',
'izipay/cancel'
```
- izipay Merchent Account's URL

    - Return URL

    ```
    https://yourdomain.com/izipay/callback
    ```

    - Sorry URL

    ```
    https://yourdomain.com/izipay/cancel
    ```

- Run these commands below to complete the setup
```
composer dump-autoload
```
```
php artisan optimize
```

> That's it, now just execute the project on your specified domain.