# PurePHP Key-Value Storage
This is simple key-value storage written on PHP.

## Installation
Via Composer:

```
    "require": {
        "elfet/pure": "~0.1"
    },
```

Via Phar file:

```
Download link will be later.
```

[Download zip](https://github.com/elfet/purephp/archive/v0.1.1.zip) archive of repository, extract it and run next commands:

```
php compile
mv pure.phar /usr/local/bin/pure
chmod +x /usr/local/bin//pure
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

## Documentation

TODO