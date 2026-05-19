<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Payment methods & manual verification

- Owners can configure `payment_methods` and fill `payment_details` (nama penerima, nomor rekening/e-wallet, instruksi) in the owner kost form.
- Owners can upload QRIS image in the kost edit form (field `qris_image`).
- Users can select a payment method during booking, view the selected method details and QR image, copy numbers, and upload payment proof during booking.
- Owners can view uploaded proofs in the owner bookings list and mark `Sudah bayar` / `Belum bayar`.

Automatic payment confirmation requires integrating a payment gateway (e.g. Midtrans, Xendit, Tripay). To prepare for integration, add these example environment variables to your `.env` or `.env.example`:

- `PAYMENT_PROVIDER` — provider identifier (midtrans/xendit/...)
- `PAYMENT_GATEWAY_SECRET` — webhook secret or HMAC key
- `PAYMENT_MERCHANT_ID` — merchant identifier
- `PAYMENT_CALLBACK_URL` — publicly reachable webhook URL
- `PAYMENT_MODE` — `sandbox` or `production`

This repo provides a basic webhook endpoint at `/webhooks/payment` which expects a JSON payload containing `booking_id` and `status`. The webhook verifies the `X-Payment-Signature` header using HMAC-SHA256 with `PAYMENT_GATEWAY_SECRET` when configured.

Security notes:

- Keep `PAYMENT_GATEWAY_SECRET` private and do not commit it to version control.
- For production, use secure storage for uploaded proofs and consider scanning uploads for malware.

Operational notes:

- To serve user-uploaded files from the `public` disk, run:

```bash
php artisan storage:link
```

- Start queue workers for background jobs:

```bash
php artisan queue:work --sleep=3 --tries=3
```

- Owner payment review page is available at `owner/bookings/pembayaran` and provides review links to each booking payment detail.
- A background job class `SendBookingPaymentNotification` is scaffolded to process payment proof uploads and status changes asynchronously.
- Payment gateway integration scaffolding is available in `app/Services/PaymentGateway` with a manager and a sample `MidtransGateway` adapter.


