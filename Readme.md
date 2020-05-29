# Costar - Coding Standard Ruler  

This is a "PHP Coding Standard Generator" for creating a PHPCS (PHP CodeSniffer) or PHPMD (PHP Mass Detector)
configuration that can be saved in PMD-XML format.

However, it can be used to import existing PMD-XML rules for PHPCS or PHPMD so that they can be also edited.


### Problem

The official teams around "PHP CodeSniffer" (PHPCS) and "PHP Mass Detector" (PHPMD) unfortunately do not offer a
graphical user interface to configure all their rules in a more comfortable way.


### Solution

In small inspiration of an available but since February 2017 outdated
[PHP Coding Standard Generator](https://edorian.github.io/php-coding-standard-generator/#phpmd),
I made myself piece of work to build a new generator with the help of [Quasar](https://quasar.dev)
(without build on compilation).


## Requirements

- Composer
- PHP 7.4 w/ SimpleXML
- Browser (Safari, Firefox, Chrome, Edge...)


## Installation

On Terminal:

`composer global require nimayneb/costar`


## Usage

On Terminal:

`costar`

Open in browser:

http://127.0.0.1:8080


## Wishlist

- Save as PhpStorm’s .editorconfig (PHPCS, PHPMD) or for other editors
- Show Coding Styleguide through example source code (PHPCS, PHPMD)
- Configure Coding Styleguide through example source code (PHPCS, PHPMD)
- Execute PHPCS or PHPMD analyser in GUI
- Import and Export of PHPCS, PHPMD
- Provide compiled Desktop-APP


## Appendix

PHPCS:
- https://github.com/squizlabs/PHP_CodeSniffer/wiki

PHPMD:
- https://phpmd.org

PMD:
- https://pmd.github.io