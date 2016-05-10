# PHP-PM HttpDrupalSeven Adapter

## Overview

This is loosely based off of php-pm-drupal.

The primary components are a bootstrap and bridge.

See:
* https://github.com/php-pm/php-pm
* https://github.com/php-pm/php-pm-httpkernel
* https://github.com/php-pm/php-pm-drupal

The code is in alpha -- very experimental.  Not suitable for human consumption.

## Known issues

* [Several serious issues are already known](https://github.com/bertrama/php-pm-drupal-seven/issues).
* There are probably a lot of subtle bugs too.

## Setup / Usage

1. Install Drupal 7.
1. Install drupal-seven-adapter
    1. Set up a sample composer.json

        ```json
        {   
            "name": "root/drupal-7.43",
            "description": "Drupal 7 test.",
            "authors": [
                {
                    "name": "Albert Bertram",
                    "email": "bertrama@umich.edu"
                }
            ],
            "repositories": [
                {
                    "type": "vcs",
                    "url": "https://github.com/bertrama/php-pm-drupal-seven"
                }
            ],
            "minimum-stability": "dev",
            "require": {
              "bertrama/php-pm-drupal-seven-adapter": "dev-master"
            }
        }
        ```
    1. run `composer install`
1. Run with `composer exec`

    ```bash
    composer exec ppm -- \
      start \
      . \
      --bridge=DrupalSevenKernel \
      --bootstrap=DrupalSeven
    ```
