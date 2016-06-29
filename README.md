ckan-php-client
===============

[![Build Status](https://travis-ci.org/GSA/ckan-php-client.svg)](https://travis-ci.org/GSA/ckan-php-client)
[![Codacy Badge](https://api.codacy.com/project/badge/d052803756de41bfa51ef9a4d080a5da)](https://www.codacy.com/app/alexandr-perfilov/ckan-php-client)
[![Join the chat at https://gitter.im/GSA/ckan-php-client](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/GSA/ckan-php-client?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Tiny CKAN API client written in PHP

## Requirements

* PHP 5.6+ : <http://php.net>

## Installation

The recommended way to install is [through composer](https://getcomposer.org/download/).

And run these two commands to install it:

    $ curl -sS https://getcomposer.org/installer | php -- --install-dir=bin

Create a composer.json file for your project:

    {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/GSA/ckan-php-client.git"
            }
        ],
        "require": {
            "gsa/ckan-php-client": "0.*"
        },
    }

Install dependencies:

    $ composer install

Now you can add the autoloader, and you will have access to the library:

    <?php
    require 'vendor/autoload.php';

## Usage

    <?php

    use CKAN\CkanClient;

    $apiUrl = 'http://catalog.data.gov/api/3';          // CKAN API URL
    $apiKey = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';   // CKAN API KEY, if needed / or null

    $Ckan = new CkanClient($apiUrl, $apiKey);

    $ckanResults = $Ckan->package_search('organization:irs-gov');
    $ckanResults = json_decode($ckanResults, true);

### Sample

Check [GSA/ckan-php-manager](https://github.com/GSA/ckan-php-manager/)
script as an example

## CKAN API DOCs

http://docs.ckan.org/en/latest/api/index.html
