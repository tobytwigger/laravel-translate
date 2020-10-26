---
layout: docs
title: Translators
nav_order: 11
has_children: true

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

## Using translators

Translators are services which take in a line of text and return a translated string in the requested language. They usually use third party translation services such as google translate to provide these translations.

You can use a translator to provide runtime translations by referring to the driver name (translations can also be called drivers, since they allow us to use third party software). In the same configuration definition, you can provide settings to the translator such as access keys or urls. This is furthered in the [driver configuration section]({{ site.baseurl }}{% link _docs/configuration/driver-configuration.md %}).

We provide a set of [official translators]({{ site.baseurl }}{% link _docs/translators/official.md %}) which are installed by default, and [third party translators]({{ site.baseurl }}{% link _docs/translators/third-party.md %}) we know of created by the community. If you've created a translator you think others may be interested in, [let us know](mailto:tobytwigger1@gmail.com)!
