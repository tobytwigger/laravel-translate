---
layout: docs
title: Configure the package behaviour
nav_order: 5
parent: Configuration
---


# Configuration
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


## Configuration File

This can be published using ```php artisan vendor:publish``` so you can customise the package. The options available are documented here, but translator drivers are documented in the [driver configuration]({{ site.baseurl }}{% link _docs/configuration/driver-configuration.md %}#registering-a-new-configuration) docs.

### Table

The ```table``` key defines the table to use. This must be set before the migration is run, and should not be changed once the migration has been run.

It defaults to ```translations```, which is a sensible default assuming you don't already have a table with this name.

### Supported Languages

The convenience of instant translations through a translation service comes with one main downside - you're relying on the translation service to accurately translate your website. It won't always produce the best translation, which is why you're able to override the translation through your lang files or the database.

A happy medium is to use instant translations to initially translate the site, then reviewing the translations and changing any translations if necessary. To stop users using your website in a language which is being reviewed, you can define the languages that should be supported. Any language requested that isn't in this array is rejected.

#### Support languages in config

The ```supported_languages``` key in the config file contains an array of supported languages. If it's an empty array, all languages are supported. If anything is in the array, only those languages will be supported.

#### Support languages directly

At any point (generally in your service provider), you can call the following to support any given language(s).

```php
    \Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor::support(['en', 'fr']);
```

This function can be called multiple times to support multiple languages. You can pass it a single language or multiple languages.

```php
\Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor::support('en');
\Twigger\Translate\Translate\Interceptors\SupportedLanguageInterceptor::support(['en', 'fr']);
```

### Detection

These are configuration options related to the target language detector. Most detectors have configuration to change things like keys to look for, and all of them can be turned off by making calls in your Service Provider.

#### Body Detection

The body detection gets the target language from the request body. The key to use is defined in config (```body_key```). It defaults to language.

If the key is not found in the request, the detector will try other strategies. If you don't want to even check the request, you can add the following to the ```register``` function in your ```AppServiceProvider```.

```php
\Twigger\Translate\TranslationServiceProvider::withoutBodyDetector();
```

#### Cookie Detection

The cookie detection gets the target language from a cookie. The key of the cookie to use is defined in config (```cookie_key```). It defaults to language.

If the cookie is not present, the detector will try other strategies. If you don't want to even check for a cookie, you can add the following to the ```register``` function in your ```AppServiceProvider```.

```php
\Twigger\Translate\TranslationServiceProvider::withoutCookieDetector();
```

#### Header Detection

The header detection gets the target language from a header. Most modern browsers automatically set this header based on user preferences.

You may set an array of allowed languages, using the ```allowed_languages``` configuration. If you want to allow any language, set this to an empty array (the default).

If the header is not present, the detector falls back to the Laravel fallback language. If you don't want to even check for a header, and so require a user to specify their language if different from the fallback, you can add the following to the ```register``` function in your ```AppServiceProvider```.

```php
\Twigger\Translate\TranslationServiceProvider::withoutHeaderDetector();
```

## Translation Configuration

You may also customise how translations are processed. By default, the following methods are used when any string is translated. If one of them is able to return a translation, the rest are skipped.

1. Check if the cache contains the translation. This is useful for speeding up translations after an initial translation
2. Check if the database contains the translation. This allows you to override any translation through your website UI, to provide a convenient way to improve translations.
3. Check your lang files to see if the translation has been defined. Replacements aren't supported, so you should use lang files with the original text as the key. This also is only supported if the source language is the same as the source language for your translation files.
4. Call the translator to get the translation value.

Assuming a translation gets to stage 4, the other steps are able to react to and save the translation.

The key will be saved in the database to make it easier to override any translations. This also means that any future calls, even if they bypass the cache, will be able to easily resolve the translation from the database rather than relying on the third party translation service.

Any translation will also be saved in the cache forever, to ensure translating may happen as quickly as possible.

If you want to take out any of the steps 1-3, you can call the relevant function in the ```register``` function in your ```AppServiceProvider```

```php
\Twigger\Translate\TranslationServiceProvider::withoutCache(); // Don't use the cache
\Twigger\Translate\TranslationServiceProvider::withoutDatabaseOverrides(); // Don't look for translations in the database
\Twigger\Translate\TranslationServiceProvider::withoutLangFiles(); // Don't load any language files
```
