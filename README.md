# Datamaps PHP

A PHP component to use Datamaps API within your PHP project.

**DatamapsApi** allows you to 
* *get* a specific map via its ID, 
* *search* the last maps created,
* *create* a new map.

Check **DatamapsClientFactories** to obtain mocked versions of **DatamapsApi** for your unit tests.

## Install

```shell
composer require logipro/datamaps-php
```

## To contribute to Datamaps PHP
### Requirements:
* docker
* git

### Unit tests
```shell
bin/phpunit
```

### Integration tests
```shell
bin/phpunit-integration
```

### Quality
#### Some indicators:
* phpcs PSR12
* phpstan level 9
* coverage >= 100%
* infection MSI >= 100%


#### Quick check with:
```shell
./codecheck
```


#### Check coverage with:
```shell
bin/phpunit --coverage-html var
```
and view 'var/index.html' in your browser


#### Check infection with:
```shell
bin/infection
```
and view 'var/infection.html' in your browser