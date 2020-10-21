---
layout: default
title: Basic Usage
nav_order: 2
---

# Basic Usage
{: .no_toc }

<details open markdown="block">
  <summary>
    Table of contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

---

Laravel Translate can be used with no configuration. To ensure this package can be used for any project, we provide multiple entry points including a Facade and an API. 

## Facade


The easiest way to translate any text is to use the ```Translate``` facade. The following line will translate 'A string to translate' from English to French.

```php
$frenchString = \Twigger\Translate\Translate::translate('A string to translate', 'fr', 'en');
```

The translate function takes the string to translate as the first argument, the language to translate to (the target language) as the second argument and the source language as the third.

There is an alternative function called ```translateMany```, which takes an array of strings rather than a single string. This is useful for efficiently translating multiple strings at the same time.

```php
$frenchStrings = \Twigger\Translate\Translate::translateMany(['A string to translate', 'A second string to translate'], 'fr', 'en');
```


### Target Language

The target language will depend on your end user. They may have the language set in their browser, your app may give them language options, or you may wish to use the default Laravel locale.

To simplify the detection of this language, the Laravel Translate package provides a detection facade.

```php
$targetLocale = \Twigger\Translate\Detect::lang();
```

The strategies used to detect the requested locale can be modified, which is described further in the [configuration option]({{ site.baseurl }}{% link _docs/configuration/package-configuration.md %}#detection) section.

### Source Language

The source language is required, as many websites may have content written in multiple languages. If this is not the case, we recommend passing in the Laravel locale as a sensible default.

```php
\Twigger\Translate\Translate::translate('A string to translate', 'fr', \Illuminate\Support\Facades\App::getLocale());
```

You may then edit the ```locale``` key in your ```config/app.php``` file to define the default locale.

---

## API

The Laravel Translate package provides an API for translating any text. The endpoint is defined in the configuration file, but defaults to ```_translate```. The source and target language are automatically detected as described above.

### Translating single lines

#### Request

```http
POST /_translate HTTP/1.1
Accept: application/json
Content-Type: application/json
{
    "line": "A line to translate"
}
```

#### Response

```http
HTTP/1.1 200 OK
Content-Type: application/json
{
   "translation": "The translated line in the detected language"
}
```

### Translating multiple lines

#### Request

```http
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

#### Response

```http
HTTP/1.1 200 OK
Content-Type: application/json
{
   "translations": [
        "The translated line in the detected language",
        "The second translated line in the detected language",
    ]
}
```

### Overriding the source and target language

This will translate the line from english to french. Although ```target_lang``` and ```source_lang``` are not required, either or both may be given to override the default behaviour of the API.
#### Request

```http
POST /_translate HTTP/1.1
Accept: application/json
Content-Type: application/json
{
    "line": "A line to translate",
    "target_lang": "fr",
    "source_lang": "en"
}
```

#### Response

```http
HTTP/1.1 200 OK
Content-Type: application/json
{
   "translation": "The translated line in the detected language"
}
```


---

## JavaScript

Currently under development.

Using our javascript tool, you can easily translate any text from Vue, React or just plain javascript. It helps maintain a balance between time taken to translate text and the number of API calls, by collecting translation requests and translating many in one.

---

## Blade

If you use the blade templating engine to define your frontend, you can make use of our blade directive to quickly translate any text.

```blade
@trans('Welcome') 
```
The target and source language will be automatically detected, using the default source language from the config and the target language using the detector,

This directive is also accessible through ```__t```, which is shorter and cleaner to use.

```blade
@__t('Welcome')
```

## Helper Function

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

---
