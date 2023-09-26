# MorningMedley - Hooks

A MorningMedley service for WordPress hooks.

## Introduction

This tool is made for organizing WordPress hooks!

This service lets you:

- Organise hooks into classes
- Easily load and add all hooks found in a directory
- Attach Actions and Filters to methods using PHP 8 attributes

## Getting Started

To get started install the package as described below in [Installation](#installation).

Look at [Usage](#usage) to learn how to, well, use it.

### Installation

Install with composer

```bash
composer require morningmedley/hooks
```

## Dependencies

###      

- PHP 8.0 or greater
- [Symfony Finder](https://symfony.com/doc/current/components/finder.html) is used to locate hooks and then register
  them
- [illuminate/container](https://github.com/illuminate/container) is the container wrapping the service
- [illuminate/support](https://github.com/illuminate/support) is used for Collections

## Usage

Add the paths and namespaces containing hooks to the application config and make sure that the service has been
registered as a
service.

```php
<?php return [
    'app' => [
        'providers' => [
            'MorningMedley\\Hooks\\ServiceProvider',
        ],
    ],
    'wp-hooks' => [
        'path' => [
            'Foo\\Hooks' => 'app/Hooks',
        ],
    ],
];
```

## Using Hooks

1. Create a Hooks class by extending `AbstractHooks`.
2. Then import `Action` an/or `Filter`.
3. Create your method with your funcitonality.
4. Lastly attach a hook to your method by adding an attribute.

The attribute looks like this where `Action` is the type of hook, `init` is the hook name and `10` is the priority.

```php
#[Action('init', 10)]
public function doSomethingOnInit(){}
```

**Note:** the service will figure out the number og arguments for the hook so you don't have to worry about that.

### Basic example

```php
<?php

namespace Foo\Hooks;

use MorningMedley\Hooks\Abstracts\AbstractHooks;
use MorningMedley\Hooks\Classes\Action;
use MorningMedley\Hooks\Classes\Filter;

class RemoveCommentsHooks extends AbstractHooks
{
    #[Action('admin_init', 10)]
    public function removeAdminMenuItems()
    {
        \remove_menu_page('edit-comments.php');
    }
    
    // Or on a property - This is equivalent to add_filter('disable_some_feature','__return_false')
    #[Filter('disable_some_feature')]
    public bool $false = false;
}

```

## Credits

- [Mathias Munk](https://github.com/mrmoeg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
