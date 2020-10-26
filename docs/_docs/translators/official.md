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
