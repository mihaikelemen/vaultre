# VaultRe API wrapper

Small utility, inspired by the [official examples](https://github.com/VaultGroup/api-samples), to handle API calls on VaultRE endpoints.

## Installation
You can install this library with Composer, by running `composer require mihaikelemen/vaultre`

## Usage
To access a specific data type (eg. attribute) such as: *properties*, *advertising*, *categories*, etc., you need to use the `setAtrribute()` method. 
How you know what attribute to use? Is simple: for example check this API endpoints `/advertising/suppliers` or `/contacts/{id}/context`. The attribute for the first example is `advertising` and for the second is `contacts`.

The second part of the endpoint (what is after the *attribute*) is the action that needs to be performed: `suppliers` or `{id}/context`

EXAMPLES: 

* retrive a single property (eg. ID 123):

~~~php
use MihaiKelemen\VaultRe\VaultRe

require_once 'vendor/autoload.php';

$valutre = new ValutRe(API_KEY, TOKEN);
$valutre->setAttribute('properties')
->fetch('123');

// access the data
if ($valutre->isSuccess()) {
	$property = $valutre->getResponse();
}
~~~

* retrieve [residential properties](https://docs.api.vaultre.com.au/#/residentialProperties/getResidentialSaleProperties) available for sale (the first 100 results):

~~~php
use MihaiKelemen\VaultRe\VaultRe

require_once 'vendor/autoload.php';

$valutre = new ValutRe(API_KEY, TOKEN);
$valutre->setAttribute('properties')
->setPageSize(100)
->fetch('residential/sale');

$properties = $api->properties();
~~~

* based on the previous example, retrive all residential properties available for sale, with auto-pagination

~~~php

use MihaiKelemen\VaultRe\VaultRe;

require_once 'vendor/autoload.php';

function nextPageNumber(array $nav=[])
{
    $url = $nav['navigation']['next'];
    if (is_null($url)) {
        return 0;
    }
    parse_str(parse_url($url, PHP_URL_QUERY), $result);
    return $result['page'];
}

$valutre = new ValutRe(API_KEY, TOKEN);
$valutre->setAttribute('properties')
->setPageSize(100)
->fetch('residential/sale');

if ($valutre->isSuccess()) {

    $page = nextPageNumber($valutre->pagination());

    // code here to process the properties returned by VaultRe (eg. $vaultre->properties())

    while ($page > 0) {

        $valutre->setPage($page)
        ->fetch('residential/sale');

        if ($valutre->isSuccess()) {
            // code here to process the properties returned by VaultRe (eg. $vaultre->properties())
            $page = nextPageNumber($valutre->pagination());
        } else {
            die($valutre->errors());
        }

    }
} else {
    die($valutre->errors());
}

~~~

* update a property (eg. property ID 123) details, by adding a new photo

~~~php

use MihaiKelemen\VaultRe\VaultRe;

require_once 'vendor/autoload.php';

$valutre = new ValutRe(API_KEY, TOKEN);
$valutre->setAttribute('properties')
->update('123/photos', [
	"photo" => "string (binary data)",
	"caption" => "string",
	"published" => true|false,
	"type" => "Photograph|Floorplan"
]);

~~~

* deleting a property

~~~php

use MihaiKelemen\VaultRe\VaultRe;

require_once 'vendor/autoload.php';

$valutre = new ValutRe(API_KEY, TOKEN);
$valutre->setAttribute('properties')
->delete('123');

~~~

Actions that can be performed on an attribute:`fetch`, `update`, `delete`, `add`. For the `update` and `add` methods you need to pass a second argument which is the payload that needs to be sent over to VaultRe.

## Links

- Official API docs: https://docs.api.vaultre.com.au
- Github: https://github.com/VaultGroup/api-samples

## Todo

- add unit testing
