[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

[comment]: <> ([![LinkedIn][linkedin-shield]][linkedin-url])



<!-- PROJECT LOGO -->
<br />
<p align="center">

  <h1 align="center">php-database-nosql</h1>

  <p align="center">
      A PHP Library for detect OS System !
      <br />
      <a href="https://github.com/sofiakb/php-database-nosql/docs"><strong>Explore the docs »</strong></a>
      <br />
      <br />
      <a href="https://github.com/sofiakb/php-database-nosql/issues">Report Bug</a>
      ·
      <a href="https://github.com/sofiakb/php-database-nosql/issues">Request Feature</a>
  </p>

</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About the library</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgements">Acknowledgements</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->

## About The Library

The library allows to detect os system in PHP project.

### Built With

This section should list any major frameworks that you built your project using. Leave any add-ons/plugins for the
acknowledgements section. Here are a few examples.

* [PHP](https://php.net)

<!-- GETTING STARTED -->

### Prerequisites

- php >= 7.4

### Installation

```shell
composer require sofiakb/php-database-nosql
```

<!-- USAGE EXAMPLES -->

## Usage

```php
//use Sofiakb\DetectOS\System;

use Sofiakb\Database\NoSQL\Model;

Model::insert(['name' => 'toto']);
Model::insert([['name' => 'toto'], ['name' => 'titi']]);

Model::where('name', 'toto')->get();
Model::where('name', 'toto')->first();
Model::where('name', '=', 'toto')->first();
Model::whereName('toto')->get();

Model::count();
Model::first();
// etc...

Model::updateBy(['name' => 'tata'], 'name', '=', 'toto');
Model::whereName('toto')->update(['name' => 'tata']);

Model::deleteBy('name', 'toto');
Model::whereName('toto')->delete();
```

<!-- ROADMAP -->

## Roadmap

See the [open issues](https://github.com/sofiakb/php-database-nosql/issues) for a list of proposed features (and known
issues).


<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE` for more information.




<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/sofiakb/php-database-nosql.svg?style=for-the-badge

[contributors-url]: https://github.com/sofiakb/php-database-nosql/graphs/contributors

[forks-shield]: https://img.shields.io/github/forks/sofiakb/php-database-nosql.svg?style=for-the-badge

[forks-url]: https://github.com/sofiakb/php-database-nosql/network/members

[stars-shield]: https://img.shields.io/github/stars/sofiakb/php-database-nosql.svg?style=for-the-badge

[stars-url]: https://github.com/sofiakb/php-database-nosql/stargazers

[issues-shield]: https://img.shields.io/github/issues/sofiakb/php-database-nosql.svg?style=for-the-badge

[issues-url]: https://github.com/sofiakb/php-database-nosql/issues

[license-shield]: https://img.shields.io/github/license/sofiakb/php-database-nosql.svg?style=for-the-badge

[license-url]: https://github.com/sofiakb/php-database-nosql/blob/main/LICENSE

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555

[linkedin-url]: https://www.linkedin.com/in/sofiane-akbly/