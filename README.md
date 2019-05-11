# mod-docs

Module for PIXELION CMS

[![Latest Stable Version](https://poser.pugx.org/panix/mod-docs/v/stable)](https://packagist.org/packages/panix/mod-docs)
[![Total Downloads](https://poser.pugx.org/panix/mod-docs/downloads)](https://packagist.org/packages/panix/mod-docs)
[![Monthly Downloads](https://poser.pugx.org/panix/mod-docs/d/monthly)](https://packagist.org/packages/panix/mod-docs)
[![Daily Downloads](https://poser.pugx.org/panix/mod-docs/d/daily)](https://packagist.org/packages/panix/mod-docs)
[![Latest Unstable Version](https://poser.pugx.org/panix/mod-docs/v/unstable)](https://packagist.org/packages/panix/mod-docs)
[![License](https://poser.pugx.org/panix/mod-docs/license)](https://packagist.org/packages/panix/mod-docs)


## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

#### Either run

```
php composer require --prefer-dist panix/mod-docs "*"
```

or add

```
"panix/mod-docs": "*"
```

to the require section of your `composer.json` file.


#### Add to web config.
```
'modules' => [
    'docs' => ['class' => 'panix\mod\docs\Module'],
],
```
#### Migrate
```
php yii migrate --migrationPath=vendor/panix/mod-docs/migrations
```
