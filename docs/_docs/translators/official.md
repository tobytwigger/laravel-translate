---
layout: docs
title: Official Translators
nav_order: 12
parent: Translators
---

# Laravel Translate
{: .no_toc }

<details open markdown="block">
  <summary>
    Official Translators
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

---

## Null Translator
> Dummy translator that always returns nothing

### Prerequisites
This driver doesn't need you to install anything in order to use it.

### Usage
- Driver Key: ```null```

This translator needs no configuration.

### Example

```php
[
    \Twigger\Translate\Translate\TranslationManager::DRIVER_KEY => 'null'
];
```

---

## AWS Translator

> Driver for the AWS Translate Service: https://aws.amazon.com/translate/

### Prerequisites
This translator makes use of the AWS API. Therefore, you will need to require the aws sdk as a dependency.

```console
    composer require aws/aws-sdk-php
```

You also need an account on AWS, and have a programmatic user with access to Laravel Translate.

### Usage
- Driver Key: ```aws```

The translator requires AWS access. These are usually already defined in Laravel if you're using the s3 filesystem or sqs queue service.

- Version: The API version to use. Should usually be 'latest'.
- Region: The AWS region to use (e.g. us-west-1).
- Key: The AWS key.
- Secret: The AWS secret.
- Log Errors: Should errors be logged if AWS throws an error.
            
### Example

```php
[
    \Twigger\Translate\Translate\TranslationManager::DRIVER_KEY => 'aws',
    'version' => 'latest',
    'region' => env('AWS_DEFAULT_REGION', 'eu-west-2'),
    'key'    => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'log_errors' => env('AWS_DEBUG_TRANSLATIONS', true)
];
```

--- 

## Google Translate - Free

> Driver for the free Google Translate API.

It is not recommended to use this driver in production for a critical site, since this API is internal to Google Translate and so may be changed.

### Prerequisites

This translator makes use of the fantastic [google translator package](https://github.com/Stichoza/google-translate-php) from [Stichoza](https://github.com/Stichoza). Therefore, you will need to require this as a dependency.

```console
    composer require stichoza/google-translate-php
```

### Usage
- Driver Key: ```google-translate-free```

Configuration:
- Log Errors: Should errors be logged if the translation throws an error.

### Example

```php
[
    \Twigger\Translate\Translate\TranslationManager::DRIVER_KEY => 'google-translate-free',
    'log_errors' => env('AWS_DEBUG_TRANSLATIONS', true)
];
```

---

## DeepL

> Driver for the DeepL Translation Service (https://www.deepl.com)

### Prerequisites

This translator makes use of a [DeepL API library](https://github.com/Baby-Markt/deepl-php-lib) from [BabyMarkt](https://www.babymarkt.de/). Therefore, you will need to require the library as a dependency.

```console
    composer require babymarkt/deepl-php-lib
```

### Usage
- Driver Key: ```deepl```

Configuration:
- Log Errors: Should errors be logged if the translation throws an error.
- Auth Key: The authentication key given to you by DeepL. Set by **DEEPL_AUTH_KEY** in your `.env` file.
- API Version: The version of API to use. Defaults to 2.
- Host: The host to use. Defaults to `api.deepl.com`

### Example

```php
[
    \Twigger\Translate\Translate\TranslationManager::DRIVER_KEY => 'deepl',
    'auth_key' => env('DEEPL_AUTH_KEY'),
    'api_version' => 2,
    'host' => 'api.deepl.com',
    'log_errors' => true
];
```

---

## Stack Driver

> Allows many translators to be called in a chain to get a translation 

Whilst not providing a translation itself, the stack driver takes a set of defined translator configurations. It calls each one in turn - if a result is given it is returned. If the first translator has a null result (the translation failed), it'll then call the second translator and so on.

This can be a very useful driver with interesting results. For example, we could use the free google translator as the main translator to save costs. A downside to this is this translator is often blocked by google for a few hours, meaning translations can't occur. By stacking the AWS translator (paid service), if the google translator fails the AWS translator picks up the slack and continues ensuring translations can happen. Once the Google translator starts working again, it takes over to save on costs.

### Prerequisites

There are no prerequisites for the stack driver. Make sure you've defined configurations to use in the stack driver though.

### Usage
- Driver Key: ```stack```

Configuration:
- Translators: An array of configuration names of other translators to use.

### Example

```php
[
    \Twigger\Translate\Translate\TranslationManager::DRIVER_KEY => 'stack',
    'translators' => [
        'aws-free-configuration-1',
        'aws-configuration-2',
        'deepl',
        'google-translate-free'
    ]
];
```
