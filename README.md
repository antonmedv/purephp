# PurePHP Key-Value Storage
[![Build Status](https://travis-ci.org/elfet/purephp.svg?branch=master)](https://travis-ci.org/elfet/purephp)

This is simple key-value storage written on PHP. It does not use files, or other database, just pure PHP.

## Installation
Via Composer:

```
composer require elfet/pure
```

Now you can run pure like this: `php vendor/bin/pure`

Or you can install PurePHP globally to run pure by `pure` command.

## Quick Guide
Start PurePHP by this command:

```
pure start &
```

Now PurePHP server is running. Run this command:

```
pure client
```

Now you can test PurePHP by simple commands like this:

```
> pure.queue.collection.push('hello')
> pure.queue.collection.push('world')
> pure.queue.collection.pop() ~ ' ' ~ pure.queue.collection.pop()
string(11) "hello world"
```

In pure console you can write commands on [Expression Language](https://github.com/symfony/expression-language). To exit from console type `exit` command.

## Documentation

### Connection to PurePHP server
```php
$port = 1337; // Default port value
$host = '127.0.0.1'; // Default host value
//...
$pure = new Pure\Client($port, $host);
```

### Storages

PurePHP provide different types on storages. All supported storages are in [src/Storage](https://github.com/elfet/purephp/tree/master/src/Storage). You can access them by next methods and work with them like you work with them directly.

You do not need to manually create any collection. They will be automatically create at first access.

```php
$pure->map('collection')->...
$pure->stack('collection')->...
$pure->queue('collection')->...
$pure->priority('collection')->...
```

Or you can access them by magic methods.

```php
$pure->map->collection->...
$pure->stack->collection->...
$pure->queue->collection->...
//...
```

### Array Storage `->map`

This is simple storage what uses php array to store your data. 

To store date in collection use `push` method:
```php
$pure->map('collection')->push(['hello' => 'world']);
```

To get value by key from collection use `get` method:
```php
$value = $pure->map('collection')->get('hello'); // will return 'world'.
```

To receive all elements use `all` method:
```php
$all = $pure->map('collection')->all();
```

You can check if key exist by `has` method, and delete element by `delete` method.

### Stack Storage `->stack`

This storage use `SplStack` to store your data.

You can use all `SplStack` methods and also `all` method.

### Queue Storage `->queue`

This storage use `SplQueue` to store your data.

You can use `SplQueue` methods and also `all` method.

`SplQueue` uses `enqueue` and `deenqueue` to push and pop from queue. In QueueStorage your can use `push` and `pop` methods to do this.

### Priority Queue Storage `->priority`

This storage use `SplPriorityQueue` to store your data.

You can use all `SplPriorityQueue` methods and also `all` method.

### Filtering

Every storage support function `filter`.

Example:

```php
// Get all elements that more than a 100.
$result = $pure->queue('collection')->filter('value > 100');

// Limit to 10.
$result = $pure->priority('collection')->filter('value > 100', 10);

// Complex expression.
$result = $pure->of('collection')->filter('value["year"] > 2000 and value["name"] matches "/term/"');
```

Filter rules uses [Expression Language](https://github.com/symfony/expression-language).
In expression available two variables: `key` and `value`.

### Deleting 

You can delete storages by `delete` method:

```
$pure->delete('collection');
```

## TODO

* Dump to file
* Load from file
* Replication 
* Sharding 
