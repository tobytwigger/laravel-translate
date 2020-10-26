---
layout: docs
title: Configuration
nav_order: 4
has_children: true
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

## Introduction

Laravel Translate strives to be as customisable as possible, so it can be used in any situation. The two main ways to customise the package are through the service provider and the configuration file.

The configuration file defines sensible defaults to some required settings, including things like the name of the table to use, the API url and allowed languages.

It also allows you to define new configurations, which are translators to use. You may be familiar with this concept from things like Laravel filesystems, databases and the cache. For example, in your ```config/filesystems.php``` file, there's an array of disks that define drivers and configuration for the drivers.
