# SilverStripe Fixture Generator

[![Build Status](https://travis-ci.org/camspiers/silverstripe-fixturegenerator.png?branch=master)](https://travis-ci.org/camspiers/silverstripe-fixturegenerator)

Allows the generation of SilverStripe unit test fixtures from existing DataObjects either programatically created or from the database.

Creating fixtures files for unit tests is tedious at best, and this library's goal is to alleviate some of the pain.

## Installation (with composer)

```bash
$ composer require camspiers/silverstripe-fixturegenerator:~0.1
```

## Usage

### Example with all relations allowed

```php
use Camspiers\SilverStripe\FixtureGenerator;

$records = //some DataObjectSet

(new FixtureGenerator\Generator(
    new FixtureGenerator\Dumpers\Yaml(
        __DIR__ . '/tests/MyFixture.yml'
    )
))->process($records);
```

### Example with certain relations allowed

```php
use Camspiers\SilverStripe\FixtureGenerator;

$records = //some DataObjectSet

(new FixtureGenerator\Generator(
    new FixtureGenerator\Dumpers\Yaml(
        __DIR__ . '/tests/MyFixture.yml'
    ),
    array(
        'MyDataObject.SomeHasOneRelation',
        'MyDataObject.SomeHasManyRelation'
    )
))->process($records);
```

### Example with certain relations excluded

```php
use Camspiers\SilverStripe\FixtureGenerator;

$records = //some DataObjectSet

(new FixtureGenerator\Generator(
    new FixtureGenerator\Dumpers\Yaml(
        __DIR__ . '/tests/MyFixture.yml'
    ),
    array(
        'MyDataObject.SomeHasOneRelation',
        'MyDataObject.SomeHasManyRelation'
    ),
    FixtureGenerator\Generator::RELATION_MODE_EXCLUDE
))->process($records);
```

## Unit testing

```bash
$ composer install --dev
$ phpunit
```