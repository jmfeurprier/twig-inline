Twig extension : inline
=======================

## Installation & Requirements

Install with [Composer](https://getcomposer.org):

```shell script
composer require jmf/twig-inline
```

## Configuration

A base path must be defined in order to locate inlinable files, and prevent inlining of unwanted files.

## Usage in Twig templates

### inline() function

```html
<style>{{ inline('style.css') }}</style>
```
