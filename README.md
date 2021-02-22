# VaultReApi

Small utility, inspired by the [official examples](https://github.com/VaultGroup/api-samples), to handle API calls on VaultRE endpoints, documented here https://docs.api.vaultre.com.au/

## Installation
You can install this library with Composer, by running `composer require mihaikelemen/vaultre`

## Usage

To access a specific data type (eg. attribute) such as: *properties*, *advertising*, *categories*, etc., you need to use the `setAtrribute()` method. 
How you know what attribute to use? Is simple: for example check this API endpoints `/advertising/suppliers` or `/contacts/{id}/context`. The attribute for the first example is `advertising` and for the second is `contacts`.

The second part of the endpoint (what is after the *attribute*) is the action that needs to be performed: `suppliers` or `{id}/context`

**EXAMPLE** to retrieve [residential properties](https://docs.api.vaultre.com.au/#/residentialProperties/getResidentialSaleProperties) available for rent:

~~~php
use MihaiKelemen\VaultRe\VaultRe

require_once 'vendor/autoload.php';

$valutre = new ValutRe(API_KEY, TOKEN);
$valutre->setAttribute('properties')
->fetch('residential/sale');
~~~

Other actions that can be performed on an attribute: `update`, `delete`, `add`. For `update` and `add` methods you need to pass a second argument which is the payload that needs to be send to VaultRe.

*todos*
- add more example usage for the library
- add unit testing
