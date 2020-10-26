---
layout: docs
title: Detector
nav_order: 8
parent: Extending
---


# Extending
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

## Detector Structure

The detector allows for new methods of detecting the preferred language of the user. These methods are called a strategy, and are nothing more than a class that extends ```Twigger\Translate\Locale\DetectionStrategy``` class.

Only one method is required, which should try and detect a preferred language. If a language was detected, the ISO-639-1 language code should be returned. Otherwise, return null.

If the method has any dependencies, such as the request or a third party client, these can be typehinted on the constructor.

```php
class MyDetector extends \Twigger\Translate\Locale\DetectionStrategy
{
    
    protected $request;

    public function __construct(\Illuminate\Http\Request $request) {
        $this->request = $request;
    }

    protected function getCurrentLocale() : ?string{
        // Use the request to detect the language. Method could return the language code or null.
        return $request->methodToGetLanguage();
    }
}
```

## Register a Strategy

Detection strategies must be registered with the ```\Twigger\Translate\Locale\DetectionStrategyStore```, usually in your service provider boot method.

You can register a strategy to run first, last or in the middle, giving you control over the importance of the detection strategies. Do this using the ```registerFirst()```, ```registerLast()``` and ```register()``` passing in the class name. 

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    $this->app->make(\Twigger\Translate\Locale\DetectionStrategyStore::class)
        ->registerFirst(\App\Locale\MyLocaleDetection::class);
}

```
