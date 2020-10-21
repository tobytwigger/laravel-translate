---
layout: default
title: Translator
nav_order: 10
parent: Extending
---


# Navigation Structure
{: .no_toc }

<details open markdown="block">
  <summary>
    Contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

---

## Translator Structure

The final major extension is a translator, which usually uses a third party translation service. It extends the ```\Twigger\Translate\Translate\Translator``` class, and defines methods to translate a line to another language.

You may also override the ```translateMany``` method, to optimise translating multiple strings at a time. This is optional though.

To access any configuration, such as keys or endpoints, you can use the ```getConfig``` function.

```php
class GoogleTranslator extends \Twigger\Translate\Translate\Translator
{
    
    protected $google;
    
    public function __construct(array $config = [], \App\Services\GoogleTranslate $google) {
        parent::__construct($config);
        $this->google = $google;
    }

    public function translate(string $line, string $to, string $from): ?string
    {
        $password = $this->getConfig('password', 'default-password');
        return $this->google->translate($line, $to, $from, $password);
    }

    public function translateMany(array $lines, string $to, string $from): array
    {
        return $this->google->translateMany($lines, $to, $from);
    }
}
```

## Register a Translator

Having created a translator, you need to register it as a driver. This way, it can be used in configurations. You'll also need to write a callback that will return an instance of the translator, with dependencies passed in.

```php
    \Twigger\Translate\Translate::pushDriver('google', function ($app, $config) {
        return new GoogleTranslator($config, $app->make(\App\Services\GoogleTranslate::class));
    });
```
