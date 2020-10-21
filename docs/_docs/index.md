---
layout: default
title: Introduction
nav_order: 1
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

## Introduction

Laravel Translate provides instant free translations using Google, AWS, Bing or any other translation service. It also allows translations to be set in language files and the database to provide a truly flexible translation tool.

- Translate any text instantly using a multitude of translation services, without tying yourself to a specific service.
- Commit specific translations to version control.
- UI to edit translations.
- Easily extensible.
- Fully tested.

---

## Installation

All you need to do to use this project is pull it into an existing Laravel app using composer.

```shell script
composer require twigger/laravel-translate
```

You can publish the configuration file by running 
```shell script
php artisan vendor:publish --provider="Twigger\Translate\TranslationServiceProvider"
```
