## Introduction

This Laravel Package simplifies the implementation of [Cloudflare Turnstile](https://www.cloudflare.com/en-gb/products/turnstile/) (a CAPTCHA alternative) on your website.

## Installation

Require the package via Composer.

```shell
composer require eighteen73/laravel-turnstile
```

Then publish the config file to your project.

```shell
php artisan vendor:publish --tag=turnstile-config
```

## Turnstile Credentials

Please refer to the [Cloudflare Turnstile documentation](https://developers.cloudflare.com/turnstile/get-started/) for instructions on how to get a Turnstile site key and secret key.

Once you have set up Turnstile in your Cloudflare account you will need to add two new values to your project's `.env` file.

```dotenv
TURNSTILE_KEY=your-site-key
TURNSTILE_SECRET=your-secret-key
```

## Form Markup

First, you want to ensure that the Turnstile JavaScript is included on your website. Turnstile analyses various telemetry and client behavior exhibited during a session so this script should be included site-wide.

Use the Blade directive `@turnstileScripts` as follows:

```html
<html>
<head>
    <!-- It could go here if you prefer -->
</head>
<body>

    <!-- The rest of your template -->
    
    @turnstileScripts

</body>
</html>
```

Then when you wish to protect a form with Turnstile just include the following directive and error response code:

```html
<form action="..." method="post">
    
    <!-- The rest of your form -->
    
    @turnstile
    
    @error('cf-turnstile-response')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    
</form>
```


## Validation Modes

There are two ways to validate your form submissions. The "middleware" mode is enabled by default is completely automatic but you may find it too limited, in which case you should enable the "validation" mode instead.

The validation mode is changed in your project's `config/turnstile.php` file.

### Via Middleware

This will automatically check _every_ request in the `web` middleware group for a Turnstile response code. If one is found it will verify it before allowing the request to continue.

This validation mode is not ideal if you need to protect middleware groups other than `web` or you want to more control over how/when the Turnstile errors are reported. For example if you wish to validate Turnstile alongside other form input fields (so all error messages can be displayed together) you should use the "validation" mode below.

### Via Form Validation

This gives more flexibility but requires you to manually add the Turnstile response code to your form validation rules wherever it is used. 

For example:

```php
use Eighteen73\Turnstile\Rules\Turnstile;

$request->validate([
    'email' => 'required|email',
    'password' => 'required',
    'cf-turnstile-response' => [new Turnstile],
]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
