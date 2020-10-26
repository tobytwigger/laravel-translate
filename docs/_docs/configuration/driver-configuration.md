---
layout: docs
title: Driver Configuration
nav_order: 6
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


## Registering a new configuration

Laravel Translate comes with a few translation drivers you can use, and you can [add your own]({{ site.baseurl }}{% link _docs/extending/translator.md %}). You may use the drivers with your own configuration. For example, the AWS driver needs a secret key and id to authenticate, whereas the null driver needs nothing.

The grouping of a driver with its settings is called a configuration, and is what you'll mainly use to interact with Laravel Translate. By default, we've defined a configuration per driver, but you're very welcome to add any number of new configurations.

## Defining configuration in the config file

In the config file, you'll see a ```configurations``` key. Each of these has a name given by the array index, and a driver given in the configuration.

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

## Defining the configuration in the manager

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
