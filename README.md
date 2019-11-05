# DEPRECATED

# Silex Config Service Provider

[![Build Status](https://travis-ci.org/bretrzaun/silex-configprovider.svg?branch=master)](https://travis-ci.org/bretrzaun/silex-configprovider)
[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

Load Silex application configuration from YAML files.

## Installation

```
composer require bretrzaun/silex-configprovider
```

## Usage

```
$app->register(new ConfigServiceProvider('config.yml'));
```

## Tests

To run the tests, just enter:

```
composer install
vendor/bin/phpunit
```
