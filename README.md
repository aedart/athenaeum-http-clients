# Athenaeum Http Clients

This package offers a Http Client wrapper, with a powerful fluent request builder that is able to use different Http Query grammars, supporting both [Json Api](https://jsonapi.org/) and [OData](https://www.odata.org/).
In addition, it also comes with a manager that allows you to handle multiple http client "profiles".
This allows you to segment each api you communicate with, into it's own client instance.

[Guzzle Http Client](http://docs.guzzlephp.org/en/stable/index.html) is used behind the scene.

## Example

## Configuration


```php
return [

    'profiles' => [

        'my-client' => [
            'driver'    => \Aedart\Http\Clients\Drivers\JsonHttpClient::class,
            'options'   => [
                'base_uri' => 'https://acme.com/api/v2'
            ]
        ],
    ]
    
    // ... remaining not shown ...
];
```

### Usage

```php
use Aedart\Http\Clients\Traits\HttpClientsManagerTrait;
use Aedart\Contracts\Http\Clients\Responses\Status;
use Teapot\StatusCode;
use DateTime;

class CurrencyController
{
    use HttpClientsManagerTrait;
    
    public function index()
    {
        $client = $this->getHttpClientsManager()->profile('my-client');
        
        // Perform a GET request
        $response = $client
            ->useTokenAuth('my-secret-api-token')
            ->where('currency', 'DKK')
            ->whereDate('date', new DateTime('now'))
            ->expect(StatusCode::OK, function(Status $status){
                throw new RuntimeException('API is not available: ' . $status);
            })
            ->get('/currencies');
        
        // ...remaining not shown
    }
}
```

## Motivation

A Http Client "package" was made available in version 3.x of the Athenaeum library.
It offered the manager to handle multiple "profiles" and some fluent methods for gradually building a request.
But it was not as comprehensive as the current version.
When Laravel released it's v7.x version, it came with a custom [Http Client](https://laravel.com/docs/7.x/http-client#introduction).
Therefore, this package became somewhat irrelevant and was considered for deprecation.
Ultimately, I decided to redesign this package entirely, mixing some of the already provided features with lots of new ones.

As a result, this package now draws inspiration from both Laravel's Http Client, as well as the [Database Query Builder](https://laravel.com/docs/7.x/queries#introduction).
You will find many similarities between the client offered by Laravel, and the one provided by this package.
The intent isn't to copy Laravel's Http Client, but rather to provide a slightly different approach on request building.

When considering whether to use this Http Client, Laravel's or other Http Client, then it's probably best to stick with what you feel most comfortable with. 
To put a different perspective on this matter, consider that Laravel has a far better support for their packages, than I can currently offer.

## Documentation

Please read the [official documentation](https://aedart.github.io/athenaeum/) for additional information.

## Repository

The mono repository is located at [github.com/aedart/athenaeum](https://github.com/aedart/athenaeum)

## Versioning

This package follows [Semantic Versioning 2.0.0](http://semver.org/)

## License

[BSD-3-Clause](http://spdx.org/licenses/BSD-3-Clause), Read the LICENSE file included in this package
