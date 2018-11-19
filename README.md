# Symfony4 Blog Demo

**Private Part**
* Private part is accessible after successful login using username and password
* Registration and creating new administrators is not possible, so there will be at least one predefined account
* Administrator can create/edit blog posts. Each blog post has:
    * Title - required short text, up to 150 chars
    * Text - required  wysiwyg content
    * Date - required
    * Tags - can have multiple tags
    * Url - unique
* Administrator can disable(hide) blog post. It cannot be deleted
    * Administrator still sees the disabled blog post, but it is not accessible for public users.
    * Can be re-enabled
* Administrator can see the number of views for each blog post

**Public part**
* Shows paginated list of blog posts
    * Ordered by date from the latest to oldest
    * Two records per page
    * Shows title and date
* Every blog post has a detail page with unique URL
    * Adds +1 to blog post views
* REST API with at least two endpoints:
    * Returns the full list of blog posts without textual content and tags
    * Returns the detail of single blog post including textual content and tags (and adds +1 to blog post views)

## Requirements

* PHP 7.1+
* MySQL

## Installing

After cloning the repository, you need to install dependencies via composer:
```
composer update
```

DB credentials are located in file:
```
.env
```

To create database and db schema, run:

```
php bin/console doctrine:database:create
```
```
php bin/console make:migration
```
```
php bin/console doctrine:migrations:migrate
```

## Blog Administration

Blog administration is available at: **/admin/**

```
Login: admin
Password: admin
```



