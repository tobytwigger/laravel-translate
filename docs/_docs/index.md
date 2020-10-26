---
layout: docs
title: Introduction
nav_order: 1
---

# Laravel Translate
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

## Introduction

Laravel Translate provides instant free translations using Google, AWS, Bing or any other translation service. It also allows translations to be set in language files and the database to provide a truly flexible translation tool.

- Translate any text instantly using a multitude of translation services, without tying yourself to a specific service.
- Commit specific translations to version control.
- UI to edit translations.
- Easily extensible.
- Fully tested.

### Why Laravel Translate

There are many translation packages for Laravel, so what does this package offer that others don't? 

Put simply, **flexibility**.
 
Laravel Translate has no limits on ways to manage translations, from a simple lang file to an automated translation file, database overrides and caching. 

If you ever decide to change the way you handle translations, Laravel Translate makes that a breeze. In one line, you can switch translation services, turn on database upgrades or turn off translations entirely!

---

## Installation

All you need to do to use this project is pull it into an existing Laravel app using composer.

```console
composer require twigger/laravel-translate
```

You can publish the configuration file by running 
```console
php artisan vendor:publish --provider="Twigger\Translate\TranslationServiceProvider"
```
