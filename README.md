<h1 align="center">Laravel Translations</h1>

<p align="center">
    <strong>Supercharged instant translations for any Laravel project.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/composer-install"><img src="http://img.shields.io/badge/source-ramsey/composer--install-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://github.com/ramsey/composer-install/blob/main/LICENSE"><img src="https://img.shields.io/badge/license-MIT-darkcyan.svg?style=flat-square" alt="Read License"></a>
    <a href="https://github.com/ramsey/composer-install/actions?query=workflow%3ACI"><img src="https://img.shields.io/github/workflow/status/ramsey/composer-install/CI?logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/composer-install"><img src="https://img.shields.io/codecov/c/gh/ramsey/composer-install?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://phpc.chat/channel/ramsey"><img src="https://img.shields.io/badge/phpc.chat-%23ramsey-darkslateblue?style=flat-square" alt="Chat with the maintainers"></a>
</p>

## Contents

* [About the Project](#about)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Configuration](#configuration)
* [Extending](#extending)
* [Contributing](#contributing)
* [Copyright and Licence](#copyright-and-licence)
* [Contact](#contact)

## About

Laravel Translate provides instant free translations using Google, AWS, Bing or any other translation service. It also allows translations to be set in language files and the database to provide a truly flexible translation tool.

- Translate any text instantly using a multitude of translation services, without tying yourself to a specific service.
- Commit specific translations to version control.
- Use the frontend to specify translation overrides.
- Easily extensible.
- Fully tested.

## Installation

All you need to do to use this project is pull it into an existing Laravel app using composer.

```shell script
composer require twigger/laravel-translate
```

You can publish the configuration file by running 
```shell script
php artisan vendor:publish --provider=\Twigger\Translate\TranslationServiceProvider
```

## Basic Usage

Laravel Translate can be used with no configuration. To ensure this package can be used for any project, we provide multiple entry points including a Facade and an API. 

### Using the Facade

The easiest way to translate any text is to use the ```Translate``` facade. The following line will translate 'A string to translate' from English to French.

```php
$frenchString = \Twigger\Translate\Translate::translate('A string to translate', 'fr', 'en');
```

The translate function takes the string to translate as the first argument, the language to translate to (the target language) as the second argument and the source language as the third.

There is an alternative function called ```translateMany```, which takes an array of strings rather than a single string. This is useful for efficiently translating multiple strings at the same time.

```php
$frenchStrings = \Twigger\Translate\Translate::translateMany(['A string to translate', 'A second string to translate'], 'fr', 'en');
```

#### Target Language

The target language will depend on your end user. They may have the language set in their browser, your app may give them language options, or you may wish to use the default Laravel locale.

To simplify the detection of this language, the Laravel Translate package provides a detection facade.

```php
$targetLocale = \Twigger\Translate\Detect::lang();
```

The strategies used to detect the requested locale can be modified, which is described further in the [configuration](#configuration) section.

#### Source Language

The source language is required, as many websites may have content written in multiple languages. If this is not the case, we recommend passing in the Laravel locale as a sensible default.

```php
\Twigger\Translate\Translate::translate('A string to translate', 'fr', \Illuminate\Support\Facades\App::getLocale());
```

You may then edit the ```locale``` key in your ```config/app.php``` file to define the default locale.

### Using the API

The Laravel Translate package provides an API for translating any text. The endpoint is defined in the configuration file, but defaults to ```_translate```. The source and target language are automatically detected as described above.

#### Translating single lines

##### Request

```http request
POST /_translate HTTP/1.1
Accept: application/json
Content-Type: application/json
{
    "line": "A line to translate"
}
```

##### Response

```http request
HTTP/1.1 200 OK
Content-Type: application/json
{
   "translation": "The translated line in the detected language"
}
```

#### Translating multiple lines

##### Request

```http request
POST /_translate HTTP/1.1
Accept: application/json
Content-Type: application/json
{
    "lines": [
        "A line to translate",
        "Another line to translate",
    ]
}
```

##### Response

```http request
HTTP/1.1 200 OK
Content-Type: application/json
{
   "translations": [
        "The translated line in the detected language",
        "The second translated line in the detected language",
    ]
}
```

#### Overriding the source and target language

This will translate the line from english to french. Although ```target_lang``` and ```source_lang``` are not required, either or both may be given to override the default behaviour of the API.
##### Request

```http request
POST /_translate HTTP/1.1
Accept: application/json
Content-Type: application/json
{
    "line": "A line to translate",
    "target_lang": "fr",
    "source_lang": "en"
}
```

##### Response

```http request
HTTP/1.1 200 OK
Content-Type: application/json
{
   "translation": "The translated line in the detected language"
}
```

### Using the js translator

Currently under development.

Using our javascript tool, you can easily translate any text from Vue, React or just plain javascript. It helps maintain a balance between time taken to translate text and the number of API calls, by collecting translation requests and translating many in one.

### Using Blade

Currently under development

If you use the blade templating engine to define your frontend, you can make use of our blade directive to quickly translate any text.

### Using the helper function

Rather than relying on the facade, you can easily use our helper function which uses the facade in the background. This takes the same arguments as the facade.

```php
// Translate from English to French
laravelTranslate('Line to Translate', 'fr', 'en');
```

The target and source language will be automatically detected if left blank, meaning you can just pass the string in

```php
laravelTranslate('Line to Translate');
```

If no arguments are given, an instance of the Translation Manager will be given.

This function is also accessible through ```__t```, which is shorter and cleaner to use.

```php
$frenchString = __t('Welcome'); // Bienvenue
```

### Overriding translations

TODO Using the DB to override translations. Need to do frontend.

## Configuration

### Package configuration

Laravel Translate strives to be as customisable as possible, so it can be used in any situation. The two main ways to customise the package are through the service provider and the configuration file.

The configuration file defines sensible defaults to some required settings, including things like the name of the table to use, the API url and allowed languages.
 
It also allows you to define new configurations, which are translators to use. You may be familiar with this concept from things like Laravel filesystems, databases and the cache. For example, in your ```config/filesystems.php``` file, there's an array of disks which define drivers and configuration for the drivers.
 
#### Configuration File

This can be published using ```php artisan vendor:publish``` so you can customise the package. The options available are documented here, but translator drivers are documented in [Registering a new configuration](#registering-a-new-configuration).

##### Config: Table

The ```table``` key defines the table to use. This must be set before the migration is ran, and should not be changed once the migration has been run. 

It defaults to ```translations```, which is a sensible default assuming you don't already have a table with this name.

##### Config: Detection

These are configuration options related to the target language detector. Most detectors have configuration to change things like keys to look for, and all of them can be turned off by making calls in your Service Provider.

###### Body Detection

The body detection gets the target language from the request body. The key to use is defined in config (```body_key```). It defaults to language.

If the key is not found in the request, the detector will try other strategies. If you don't want to even check the request, you can add the following to the ```register``` function in your ```AppServiceProvider```.

```php
\Twigger\Translate\TranslationServiceProvider::withoutBodyDetector();
```

###### Cookie Detection

The cookie detection gets the target language from a cookie. The key of the cookie to use is defined in config (```cookie_key```). It defaults to language.

If the cookie is not present, the detector will try other strategies. If you don't want to even check for a cookie, you can add the following to the ```register``` function in your ```AppServiceProvider```.

```php
\Twigger\Translate\TranslationServiceProvider::withoutCookieDetector();
```

###### Header Detection

The header detection gets the target language from a header. Most modern browsers automatically set this header based on user preferences.

You may set an array of allowed languages, using the ```allowed_languages``` configuration. If you want to allow any language, set this to an empty array (the default).

If the header is not present, the detector fall back to the Laravel fallback language. If you don't want to even check for a header, and so require a user specify their language if different from the fallback, you can add the following to the ```register``` function in your ```AppServiceProvider```.

```php
\Twigger\Translate\TranslationServiceProvider::withoutHeaderDetector();
```

#### Translation Configuration

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

### Registering a new configuration

Laravel Translate comes with a few translation drivers you can use, and you can add your own ([docs](#extending-the-translator)). You may use the drivers with your own configuration. For example, the AWS driver needs a secret key and id to authenticate, whereas the null driver needs nothing.

The grouping of a driver with its settings is called a configuration, and is what you'll mainly use to interact with Laravel Translate. By default, we've defined a configuration per driver, but you're very welcome to add any number of new configurations.

#### Defining configuration in the config file

In the config file, you'll see a ```configurations``` key. Each of these have a name given by the array index, and a driver given in the configuration.

```php
[
    'configurations' => [
        'my-configuration' => [
            \Twigger\Translate\Translate\TranslationManager::DRIVER_KEY => 'driver-name',
            'username' => 'Some Username',
            'password' => 'pw'
        ]
    ]
];
```

You can now use ```'my-configuration'``` to refer to this specific driver instance. For example, to translate text using the driver ```driver-name``` and the username ```Some Username```:

```php
\Twigger\Translate\Translate::driver('my-configuration')->translate('Text to Translate', 'en', 'fr');
```

If you omit the driver method, ```Translate``` will use the default driver. This is also defined in the config file

```php
[
    'default' => 'my-configuration'
];
```

#### Defining the configuration in the manager

If you want to dynamically create configurations, it's no good just using the config file. In these cases, you may add
configurations and change the default translator using Translate.

The following lines will add the configuration as defined in the above example.

```php
\Twigger\Translate\Translate::pushConfiguration('my-configuration', 'driver-name', [
    'username' => 'Some Username',
    'password' => 'pw'
]);
```

To change the default configuration, you can call ```setDefaultDriver```.

```php
\Twigger\Translate\Translate::setDefaultDriver('my-configuration');
```

These would usually be called in the boot method of a service provider, but they may be called anywhere in your code.

## Extending

Laravel Translate has been designed with extensibility as a priority - we make sure the package works for you rather than you working for the package! There are three main extensions you can build for Laravel Translate, which are documented below. If you do build any extensions, it'd be very much appreciated if you could open a pull request with your changes so everyone can benefit in the spirit of Open Source software.

### Extending the detector


- Writing & registering a detection strategy

### Extending the Translator

The translator provides two interfaces - a translator and an interceptor. Although these are very similar, and in fact an interceptor is just a special form of translator, they are used for very different reasons.

A translator should be able to translate almost any text. An interceptor only has certain text it able to translate, and passes onto the translator if it is not able to translate. For example, lang files are interceptors as you have to define each translation so it's likely any given text won't have a translation. Google translate is a translator, since it should be able to handle most strings. 

If you're unsure if you need to create an interceptor or a translator, ask yourself if it's likely the method won't be able to translate any random string. If this is the case, you want an interceptor.

#### Creating an Interceptor



#### Creating a Translator

## Contributing

Contributions are welcome! Before contributing to this project, familiarize
yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

## Copyright and Licence

This package is copyright © [Toby Twigger](https://github.com/tobytwigger)
and licensed for use under the terms of the MIT License (MIT). Please see
[LICENCE.md](LICENCE.md) for more information.

## Contact

For any questions, suggestions, security vulnerabilities or help, email me directly at [tobytwigger1@gmail.com](mailto:tobytwigger1@gmail.com)
