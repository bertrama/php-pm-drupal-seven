# PHP-PM HttpDrupalSeven Adapter

## Overview

This is loosely based off of php-pm-drupal.

The primary components are a bootstrap and bridge.

See:
* https://github.com/php-pm/php-pm
* https://github.com/php-pm/php-pm-httpkernel.

The code is in alpha -- very experimental.

### Setup / Usage

1. Install Drupal 7.
1. Install drupal-seven-adapter
    1. TODO: provide instructions.



```bash
vendor/bin/ppm \
  start \
  . \
  --bridge=DrupalSevenKernel \
  --bootstrap=DrupalSeven
```
