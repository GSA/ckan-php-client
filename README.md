ckan-php-client
===============

## API DOCs

http://docs.ckan.org/en/latest/api/index.html


## Requirements

* PHP 5.4+ : <http://php.net>

## Installation

The recommended way to install is [through composer](https://getcomposer.org/download/).

And run these two commands to install it:

    $ curl -sS https://getcomposer.org/installer | php

Create a composer.json file for your project:

    {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/GSA/ckan-php-client.git"
            }
        ],
        "require": {
            "rei/ckan-php-client": "dev-dev"
        },
    }

Refresh your dependencies:

    $ php composer.phar update

Now you can add the autoloader, and you will have access to the library:

    <?php
    require 'vendor/autoload.php';

## Usage

    <?php

    use CKAN\Core\CkanClient;

    $apiUrl = 'http://catalog.data.gov/api/3';          // CKAN API URL
    $apiKey = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';   // CKAN API KEY, if needed / or null

    $Ckan = new CkanClient($apiUrl, $apiKey);

    $ckanResults = $Ckan->package_search('organization:irs-gov');
    $ckanResults = json_decode($ckanResults, true);

### Sample

Check [GSA/ckan-php-manager](https://github.com/GSA/ckan-php-manager/)
script as an example