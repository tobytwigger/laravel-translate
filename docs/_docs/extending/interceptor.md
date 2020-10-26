---
layout: docs
title: Interceptor
nav_order: 9
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

## Interceptor Structure

An interceptor is a class that extends ```Twigger\Translate\Translate\TranslationInterceptor```. It can be thought of as middleware - the translation interceptor is used to get a value for any translation. If no value exists however, the value is translated and the translation saved using the same interceptor. This allows the interceptor the chance to handle a future translation of the same text.

It therefore must implement three functions:
- ```canIntercept($line, $to, $from)``` - Determine if the interceptor has a value for a translation
- ```get($line, $to, $from)``` - Get the value of an interception
- ```save($line, $to, $from)``` - Save the value of an interception

Although this is enough, there are cases where you can optimise your interceptor, especially if the interceptor uses external resources like a database. You may define the same three methods which handle many translations at a time (e.g.  when ```translateMany``` is used). The only difference is ```getMany```, ```canInterceptMany``` and ```saveMany``` 

It therefore must implement three functions:
- ```canInterceptMany($lines, $to, $from)``` - Determine if the interceptor has a value for a translation. Returns an array of booleans to mark whether the corresponding line can be intercepted.
- ```getMany($lines, $to, $from)``` - Get the value of many interceptions. ```$lines``` will be an array of lines that can be intercepted.
- ```saveMany($lines, $to, $from)``` - Save the value of many interceptions. Only translations not intercepted will be passed to this, even if they haven't been changed.

As before, dependencies can be typehinted to the constructor

```php
class CrowdInInterceptor extends Twigger\Translate\Translate\TranslationInterceptor
{

    protected $crowdIn;

    public function __construct(array $config,\Twigger\Translate\Translate\Translator $translator, \App\Lang\CrowdInService $crowdIn) {
        parent::__construct($config,$translator);
        $this->crowdIn = $crowdIn;
    }

    protected function canIntercept(string $line, string $to, string $from): bool
    {
        return $this->crowdIn->has($line, $to, $from);
    }

    protected function get(string $line, string $to, string $from): string
    {
        return $this->crowdIn->get($line, $to, $from);
    }

    protected function save(string $line, string $to, string $from, string $translation): void
    {
        $this->crowdIn->save($line, $to, $from, $translation);
    }

    protected function canInterceptMany(array $lines, string $to, string $from): bool
    {
        // This method is entirely optional.

        $result = [];
        foreach($lines as $index => $line) {
            $result[$index] = $this->crowdIn->has($line, $to, $from);
        }
        return $result;
    }

}
```

## Register an Interceptor

To register an interceptor, you just need to call the ```intercept``` function on the ```TranslationFactory``` in your service provider.

```php
    public function boot() {
        $this->app->make(\Twigger\Translate\Translate\TranslationFactory::class)
            ->intercept(\App\Interceptor\CrowdInInterceptor::class);
    }
```
