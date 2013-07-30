# Access Control Bundle #

[![Build Status](https://travis-ci.org/rheck/accesscontrol-bundle.png)](https://travis-ci.org/rheck/accesscontrol-bundle)
[![Total Downloads](https://poser.pugx.org/rheck/accesscontrol-bundle/d/total.png)](https://packagist.org/packages/rheck/accesscontrol-bundle)
[![Latest Stable Version](https://poser.pugx.org/rheck/accesscontrol-bundle/v/stable.png)](https://packagist.org/packages/rheck/accesscontrol-bundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/rheck/accesscontrol-bundle/badges/quality-score.png?s=6f6e981fc4d35e7ff9108ee96d2f3238c018a696)](https://scrutinizer-ci.com/g/rheck/accesscontrol-bundle/)
[![Scrutinizer Continuous Inspections](https://scrutinizer-ci.com/g/rheck/accesscontrol-bundle/badges/general.png?s=26c468dbcc712407c4392e94bd354934ca03c446)](https://scrutinizer-ci.com/g/rheck/accesscontrol-bundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/rheck/accesscontrol-bundle/badges/coverage.png?s=5c188758b043eac90153226c83ded89ce7843971)](https://scrutinizer-ci.com/g/rheck/accesscontrol-bundle/)

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

### Configuration
If you want to use the default Bundle Strategy you must to create the databases of permissions.

**1. Doctrine Schema Update Command**
```bash
php app/console doctrine:schema:update --force
```
**2. Create on the database directly (MySQL Example)**
```sql
CREATE TABLE rheck_permissioncontexts (
    `id` INT AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE rheck_permissions (
    `id` INT AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `permissionContext_id` INT DEFAULT NULL,
    INDEX IDX_538F31584B364D6E (permissionContext_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
```

**3. Entity**

The permission must have relationship with an entity user or other one with realtionship with user as will be logged in and must implement an interface.

Example:
```php
use Rheck\AccessControlBundle\Entity\PermissionAccessInterface;

class User implements PermissionAccessInterface
{
    protected $permissions;
    
    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    public function addPermission(Permission $permission)
    {
        $this->permissions->add($permission);
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

}
```

**4. config.yml**

Example 1: If you want to validate the permission with my user entity directly. The configuration is:
```yaml
rheck_access_control:
    has_permissions: user
```

Example 2: Suposing that I have an entity called UserGroups and it have relationship ManyToMany with user. The configuration is:
```yaml
rheck_access_control:
    has_permissions: user.userGroups
```

### Usage
**You have two ways to check the permissions.** 

For both ways you have 4 fields:
  

 ***1. Permissions***: can be a single parameter or an array;
 
 ***2. Context***: you can group the permissions by a context, default value is "System";
 
 ***3. Criteria***: you can choose how is the criteria to check the permissions, its value can be "AND" or "OR". The default value is "AND";
 
 ***4. Strategy***: you can create your own strategy of validation. An example follow at the end of this file.

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
