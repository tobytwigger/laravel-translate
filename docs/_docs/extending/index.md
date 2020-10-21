---
layout: default
title: Extending
nav_order: 7
has_children: true
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

Laravel Translate has been designed with extensibility as a priority - we make sure the package works for you rather than you working for the package! There are three main extensions you can build for Laravel Translate, which are documented below. If you do build any extensions, it'd be very much appreciated if you could open a pull request with your changes so everyone can benefit in the spirit of Open Source software.

## Extending the Detector

TODO
 - Why would you want to
 - Link to docs

## Extending the Translator

The translator provides two interfaces - a translator and an interceptor. Although these are very similar, and in fact an interceptor is just a special form of translator, they are used for very different reasons.

A translator should be able to translate almost any text. An interceptor only has certain text it is able to translate, and defaults to the translator if the interceptor is not able to translate the text. For example, lang files are interceptors as you have to define each translation so it's likely any given text won't have a translation. Google translate is a translator, since it should be able to handle most strings without first needing to define them. 

If you're unsure if you need to create an interceptor or a translator, ask yourself if it's likely the method won't be able to translate any random string. If this is the case, you want an interceptor.
