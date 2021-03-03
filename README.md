Usersnap
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmusersnap/v/stable)](https://packagist.org/packages/wieni/wmusersnap)
[![Total Downloads](https://poser.pugx.org/wieni/wmusersnap/downloads)](https://packagist.org/packages/wieni/wmusersnap)
[![License](https://poser.pugx.org/wieni/wmusersnap/license)](https://packagist.org/packages/wieni/wmusersnap)

> Integrate the Usersnap feedback widget with your Drupal website.

## Why?
This package requires PHP 7.2 and Drupal 8.5 or higher. It can be

installed using Composer:

```bash
 composer require wieni/wmusersnap
```

## How does it work?
### Configuration
Once enabled, you can configure the module through the settings form at
`/admin/config/services/usersnap`. 

To change the configuration of the module, users need the permission
`administer usersnap settings`.

The loader script is added to every page and it uses cookies to check whether or not the integration should be enabled. 
This way, changing settings doesn't require a full site cache purge. Additionally, it works very well with sites where 
the admin and front are accessible through a different subdomain. It's important to set the _Cookie domain_ and 
_Domains_ settings, if not the integration will not work.
     
## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE.md) file
for more information.
