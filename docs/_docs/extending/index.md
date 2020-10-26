---
layout: docs
title: Extending
nav_order: 7
has_children: true
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

## Introduction

Laravel Translate has been designed with extensibility as a priority - we make sure the package works for you rather than you working for the package! There are three main extensions you can build for Laravel Translate, which are documented below. If you do build any extensions, it'd be very much appreciated if you could open a pull request with your changes so everyone can benefit in the spirit of Open Source software.

## Extending the Detector

The detector has several methods for detecting the target language. For example, it checks in the request to see if a language has been given. If not, it'll check to see if a cookie exists that contains the language. Failing that, it'll use the browser preferences.

Laravel Translate allows you to add your own methods. For example, maybe a user specifies their default language which is saved in the database. Or maybe it's in the url of the website (such as www.example.com/fr).

No matter what, you can [create your own method]({{ site.baseurl }}{% link _docs/extending/detector.md %}) for detecting the target language.

## Extending the Translator

The translator provides two interfaces - a [translator]({{ site.baseurl }}{% link _docs/extending/translator.md %}) and an [interceptor]({{ site.baseurl }}{% link _docs/extending/interceptor.md %}). Although these are very similar, and in fact an interceptor is just a special form of translator, they are used for very different reasons.

A translator should be able to translate almost any text. An interceptor only has a certain text that is able to translate, and defaults to the translator if the interceptor is not able to translate the text. For example, lang files are interceptors as you have to define each translation so it's likely any given text won't have a translation. Google translate is a translator, since it should be able to handle most strings without first needing to define them.

If you're unsure if you need to create an interceptor or a translator, ask yourself if it's likely the method won't be able to translate any random string. If this is the case, you want an interceptor.
