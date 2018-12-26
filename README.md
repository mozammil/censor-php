# censor-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mozammil/censor-php.svg?style=flat-square)](https://packagist.org/packages/mozammil/censor-php)
[![Build Status](https://img.shields.io/travis/com/mozammil/censor-php.svg?style=flat-square)](https://travis-ci.org/mozammil/censor-php)
[![Quality Score](https://img.shields.io/scrutinizer/g/mozammil/censor-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/mozammil/censor-php)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This PHP package helps to censor words in a string.

You could censor words by replacing it with other words or you could also use a custom Replacer.

## Install

Via Composer

``` bash
$ composer require mozammil/censor-php
```

## Usage

``` php
<?php

use Mozammil\Censor\Censor;
use Mozammil\Censor\Replacers\StarReplacer;
use Mozammil\Censor\Replacers\HashReplacer;
use Mozammil\Censor\Replacers\RandomCharactersReplacer;

$censor = new Censor();

$string = 'Laravel is a breath of fresh air. What a framework.';

$data = [
    'Laravel' => 'PHP',
    'breath'  => HashReplacer::class,
    'air'     => StarReplacer::class,
    'frame%'  => RandomCharactersReplacer::class // notice the wildcard.
];

echo $censor->replace($data, $string); // outputs: PHP is a ### of fresh ***. What a @)%#!#^&*.

```

## Wildcard Matching

The package also allows you to add wildcards to target words.

For example, if you want to target `idiotic` and `idiot` at the same time, you could specify the pattern as `idiot%`.

Similarly, `%place%` will also match `Replacers`. The wildcard character can be changed if you wish, by doing the following

``` php
<?php

use Mozammil\Censor\Censor;

$censor = new Censor();
$censor->setWildcardCharacter('*');

```

## Replacers

The package ships with 3 `Replacers`.
- `HashReplacer` (Replaces word with exactly 3 #)
- `StarReplacer` (Replaces word with * matching the length of the word)
- `RandomCharactersReplacer` (Replaces word with a set random characters)

You can also write your own custom replacer. You just need to make sure it follows the `Replacer` interface which is shown below.

``` php
<?php
namespace Mozammil\Censor\Replacers;

interface Replacer
{
    /**
     * Replaces a given word with anything
     *
     * @param string $word
     *
     * @return string
     */
    public function replace(string $word): string;
}

```

You could take a look at the `src/Replacers` directory to learn a bit more about it.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email [hello@moz.im](mailto:hello@moz.im) instead of using the issue tracker.

## Credits

- [Mozammil Khodabacchas](https://twitter.com/mozammil_k)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.