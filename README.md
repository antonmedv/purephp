# PurePHP Key-Value Storage
This is simple key-value storage written on PHP.

## Installation
Via Composer:

```
composer require elfet/pure:~0.1
```

Via Phar file:

```
Download link will be later.
```

[Download zip](https://github.com/elfet/purephp/archive/v0.1.1.zip) archive of repository, extract it and run next commands:

```
composer install --dev
php compile
mv pure.phar /usr/local/bin/pure
chmod +x /usr/local/bin/pure
```

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
> pure.stack.test.push('hello wolrd!')
bool(true)
> pure.stack.test.pop()
string(12) "hello wolrd!"
>
```

In pure console you can write commands on [Expression Language](https://github.com/symfony/expression-language). To exit from console type `exit` command.

## Documentation

### Connection to PurePHP server
```php
$port = 1337; // Default port value
$host = '127.0.0.1'; // Default host value
//...
$pure = new Pure\Clent($port, $host);
```

### Storages

PurePHP provide diffrent types on stogares. All supported storages are in [src/Storage](https://github.com/elfet/purephp/tree/master/src/Storage). You can access them by next methods and work with them like you work with them directly.

Every storage has separate collection namespace. So you can have for different storages same collection names.
You do not need to manualy create any collection. They will be automatically create at first access.

```php
$pure->of('collection')->...
$pure->stack('collection')->...
$pure->queue('collection')->...
$pure->prioriry('collection')->...
$pure->lifetime('collection')->...
```

Or you can access them by magic methods.

```php
$pure->of->collection->...
$pure->stack->collection->...
$pure->queue->collection->...
//...
```

### Array storage `->of`

This is simple storage what uses php array to store your data. 
To store date in collection use `push` method:
```php
$pure->of('collection')->push(['hello' => 'world']);
```
Array Storage uses `array_merge` function.

To get value by key from collection use `get` method:
```php
$value = $pure->of('collection')->get('hello'); // will return 'world'.
```

To receive all elements use `all` method:
```php
$all = $pure->of('collection')->all();
```

To delete all elements use `clear` method:
```php
$all = $pure->of('collection')->clear();
```

You can check if key exist by `has` method, and delete element by `delete` method.

### Stack storage `->stack`

This storage use `SplStack` to store your data.

You can use all `SplStack` methods and also `all`, `clear` methods.

### Queue storage `->queue`

This storage use `SplQueue` to store your data.

You can use `SplQueue` methods and also `all`, `clear` methods.

`SplQueue` uses `enqueue` and `deenqueue` to push and pop from queue. In QueueStorage your can use `push` and `pop` methods to do this.

### Priority Queue storage `->priority`

This storage use `SplPriorityQueue` to store your data.

You can use all `SplPriorityQueue` methods and also `all`, `clear` methods.

### Lifetime Storage `->lifetime`

In this storage you can store data which need to be deleted after some period. Life time in seconds.

```php
$pure->lifetime('collection')->set($key, $value, $lifetime);
```

You can get all elements by `all` method, check if key exist by `has` method, and delete element by `delete` method.
