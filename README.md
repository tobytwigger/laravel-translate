<h1 align="center">Laravel Translate</h1>

<p align="center">
    <strong>Supercharged instant translations for any Laravel project.</strong>
</p>

<p align="center">
    Latest Version
    Downloads
    Release: passing
    Dev: passing
    
    <a href="https://github.com/tobytwigger/laravel-translate/releases">
        <img src="https://img.shields.io/github/v/release/tobytwigger/laravel-translate?label=Latest%20Version&sort=semver&style=plastic" alt="Latest Version">
    </a>
    <a href="https://github.com/tobytwigger/laravel-translate/blob/master">
        <img src="https://img.shields.io/github/workflow/status/tobytwigger/laravel-translate/php/master?label=master%20status&style=plastic" alt="Master branch status">
    </a>
    <a href="https://github.com/tobytwigger/laravel-translate/blob/develop">
        <img src="https://img.shields.io/github/workflow/status/tobytwigger/laravel-translate/php/develop?label=master%20status&style=plastic" alt="Master branch status">
    </a>
</p>

<p align="center">

    <a href="http://buymeacoffee.com/translate">
        <img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy me a coffee">
    </a>
</p>


## Contents

* [About the Project](#about)
* [Documentation](#docs)
* [Contributing](#contributing)
* [Roadmap](#roadmap)
* [Copyright and Licence](#copyright-and-licence)
* [Contact](#contact)

## About

Laravel Translate provides instant free translations using Google, AWS, Bing or any other translation service. It also allows translations to be set in language files and the database to provide a truly flexible translation tool.

- Translate any text instantly using a multitude of translation services, without tying yourself to a specific service.
- Commit specific translations to version control.
- Use the frontend to specify translation overrides.
- Easily extensible.
- Fully tested.

## Docs

We've taken care over documenting everything you'll need to get started and use Laravel Translate fully.

[Check out the docs](https://tobytwigger.github.io/laravel-translate) on our documentation site.

## Contributing

Contributions are welcome! Before contributing to this project, familiarize
yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

## Roadmap

The following list is a non-comprehensive collection of features which will be implemented in the next few weeks/months.

- [ ] Documentation needs reviewing.
- [ ] DB model should clear the translation cache when updated using an observer. This means that any db changes will be instantly reflected through the cache.
- [ ] DB frontend needs to be made using Vuetify to allow translations to be created/updated.
- [ ] A JS tool needs to be written to allow use of the provided translate API. It should limit the number of requests by grouping translation requests and sending them in one.
- [ ] The DB interceptor needs to be optimised in terms of database calls.
- [ ] More implementations for translation services are needed.
- [ ] A 'free' translation service should be made, which uses AWS free tier, google translate directly and any other free-per-month service.

## Copyright and Licence

This package is copyright © [Toby Twigger](https://github.com/tobytwigger)
and licensed for use under the terms of the MIT License (MIT). Please see
[LICENCE.md](LICENCE.md) for more information.

## Contact

For any questions, suggestions, security vulnerabilities or help, email me directly at [tobytwigger1@gmail.com](mailto:tobytwigger1@gmail.com)
