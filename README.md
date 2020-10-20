# Laravel Resources

[![<Laravel Resources Badge>](https://circleci.com/gh/Engency/laravel-resource-controller.svg?style=shield)](<https://app.circleci.com/pipelines/github/Engency/laravel-resource-controller>)

Don't waste valuable time on writing basic CRUD operations for your Laravel application. 

### Ideology

Most CRUD operations are very straight-forward, especially when conforming to the [REST design](https://en.wikipedia.org/wiki/Representational_state_transfer). As long as a few basic rules are met, generic logic can do the trick.  
1. Middleware authorizes users for operations on resources
2. The controller determines the scope
3. Rules validate input before storing and updating resources
4. Rules make sure clients will only receive the attributes they are authorized to see

For applications with both an API and 'general' webinterface, you ideally just want a single controller performing the basic crud operations on a resource. Therefore, generic logic should be able to construct responses in both HTML and JSON format.

## Requirements

- PHP 7.1+ | PHP 8+
- The Laravel framework

## Installation

```shell script
composer require engency/laravel-resource-controller
```

*Alongside this package, the [engency/eloquent-formatting](https://github.com/Engency/eloquent-formatting) and [engency/laravel-model-validation](https://github.com/Engency/laravel-model-validation) package will be installed.* 

## Usage

The most basic setup could look as following;

*The controller*
```php
use Illuminate\Http\Request;
use Engency\Http\Controllers\ResourceController;
use Engency\Http\Controllers\DefaultResourceActions;

class UserController extends ResourceController
{
    use DefaultResourceActions;

    /**
     * Provide the resource class in the parent's constructor.
     * Add any middleware to authorize users.
     */
    public function __construct()
    {
        parent::__construct(User::class);
        
        $this->middelware('auth');
    }

    /**
     * Set the scope for this resource controller.
     * The expected return value should either be a query builder or a Laravel collection.
     * 
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Support\Collection|\Illuminate\Database\Query\Builder
     */
    protected function getScope(Request $request)
    {
        return User::query();
    }

}
```

*The model*
```php
use \Illuminate\Database\Eloquent\Model;
use \Engency\ModelValidation\Validatable;
use Engency\DataStructures\CustomDataFormats;
use Engency\DataStructures\ExportsCustomDataFormats;

class User extends Model implements ExportsCustomDataFormats
{
    use Validatable; // trait required for laravel-model-validation
    use CustomDataFormats; // trait required for eloquent-formatting

    protected $fillable = [
        'name',
        'email'
    ];
    
    /**
     * Make sure clients only receive data they are authorized for.
     * Visit complete documentation on custom export formats on;
     * https://github.com/Engency/eloquent-formatting
     */
    protected $exports = [
        'default' => [
            'name',
        ],
        'complete' => [
            'name',
            'email'
        ]
    ];

    /**
     * Basic validation for resource attributes.
     * Visit complete documentation on model validation on;
     * https://github.com/Engency/laravel-model-validation
     */
    public function rules() : array {
        return [
            'name' => 'required|string',
            'email' => 'required|email'
        ];
    }   

}
```

### Html response
The controller will find for the following views;
- *resource-path*/views/pages/*resource-name-kebab-case*/**index**.blade.php
- *resource-path*/views/pages/*resource-name-kebab-case*/**create**.blade.php
- *resource-path*/views/pages/*resource-name-kebab-case*/**show**.blade.php
- *resource-path*/views/pages/*resource-name-kebab-case*/**edit**.blade.php

Within the index.blade.php file, the $items variable will be present by default. The index.blade.php file could look as following;
```blade
<ul>
@foreach($items as $item)
    <li>{{ $item->name }}</li>
@endforeach
</ul>
```

Any page showing a resource (show and edit) have access to the resource. The name of the variable is the name of the resource, in camel case. E.g., 'StreetSign' would be '$streetSign'. The show.blade.php file could look like this;
```blade
<p>You are viewing {{ $user->name }}.</p>
<p>The corresponding email address is {{ $user->email }}.</p>
```

In addition to that, the controller uses specific error pages;
- *resource-path*/views/pages/error/**unauthorized**.blade.php
- *resource-path*/views/pages/error/**notfound**.blade.php
- *resource-path*/views/pages/error/**conflict**.blade.php
- *resource-path*/views/pages/error/**forbidden**.blade.php
- *resource-path*/views/pages/error/**500**.blade.php
 
### JSON response

*Response for the index call*
```json
{
  "items": [
      {"name": "John"},
      {"name": "Doe"}
  ]
}
```

## Contributors

- Frank Kuipers ([GitHub](https://github.com/frankkuipers))
- Feel free to contribute or submit feature-requests as issues. 

## License

This plugin is licenced under the [MIT license](https://opensource.org/licenses/MIT).
