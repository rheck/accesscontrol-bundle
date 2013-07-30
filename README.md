# Access Control Bundle #

This Bundle is a easy solution for the route access control. You can choose to use the default strategy of the Bundle or create your own custom.

### How it works
You can install this bundle using composer
```bash
composer require rheck/accesscontrol-bundle
```
or add the package to the composer.json file of your Symfony project.

After you have installed the package, you need to add the bundle to your AppKernel.php file:
```php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Rheck\AccessControlBundle\RheckAccessControlbundle(),
    // ...
);
```

### Usage
**You have two ways to check the permissions.** 

For both ways you have 4 fields:
  

 1. ***Permissions***: can be a single parameter or an array;
 2. ***Context***: you can group the permissions by a context, default value is "System";
 3. ***Criteria***: you can choose how is the criteria to check the permissions, its value can be "AND" or "OR". The default value is "AND";
 4. ***Strategy***: you can create your own strategy of validation. An example follow at the end of this file.

#### 1. Validation By Annotation

**@PermissionAccess**: you need to add the use statement:
```php
use Rheck\AccessControl\Annotation\PermissionAccess;
```

**Example 1** (Using the Default Bundle Strategy):
```php
/**
 * @PermissionAccess("INDEX", context="DASHBOARD")
 */
```
On the example above I want to check if my logged user has the permission "INDEX" of context "DASHBOARD" allowed to access. Otherwise a 403 http error message will be throwed.

**Example 2** (Using the Default Bundle Strategy):
```php
/**
 * @PermissionAccess({"VIEW", "VIEW_ALL"}, context="PROJECT", criteria="OR")
 */
```
On the example above I want to check if my logged user is allowed to access one of the array of permissions added on the permissions check. Note: I need just one permission allowed, because the criteria is "OR". If the criteria is "AND" I must to be allowed on every listed permissions.

#### 2. Validation By Twig
Like the "*1. Validation By Annotation*", we have the same parameters, so lets just adapt for the twig view:

**Example 1** (Like the annotation example 1):
```twig
{% if permissionAccess("INDEX", "DASHBOARD") %}
    You have permission to access.
{% else %}
    You donot have permission to access.
{% endif %}
```

**Example 2** (Like the annotation example 2):
```twig
{% if permissionAccess(["VIEW", "VIEW_ALL"], "PROJECT", "OR") %}
    You have permission to access.
{% else %}
    You donot have permission to access.
{% endif %}
```

### Creating your own Strategy
To create your own validation strategy you must follow the steps bellow:
#### 1. Create the Strategy file:
```php
<?php

namespace MyNamespace\Strategy;

use Rheck\AccessControlBundle\Strategy\PermissionAccessStrategyInterface;

class CustomStrategy implements PermissionAccessStrategyInterface
{
    public function run($permissions, $context, $criteria)
    {
		// Validate the data as you want.
		$validatedData = true;

        return $validatedData;
    }
}
```
You must to return a boolean value.

### 2. Register your strategy as service:
After the creation of strategy you must to register it as service, like bellow:
```yaml
parameters:
    my.custom.strategy.class: MyNamespace\Strategy\CustomStrategy
services:
    my.custom.strategy:
        class: %my.custom.strategy.class%
```

### 3. Validate the data with your custom strategy:
**3.1. Annotation:**
```php
/**
 * @PermissionAccess("DETAIL", strategy="my.custom.strategy")
 */
```

**3.2. Twig**
```twig
{% if permissionAccess("DETAIL", "SYSTEM", "AND", "my.custom.strategy") %}
   You have access.
{% else %}
   You haven't access.
{% endif %}
```
