# kw_cache_psr

[![Build Status](https://app.travis-ci.com/alex-kalanis/kw_cache_psr.svg?branch=master)](https://app.travis-ci.com/github/alex-kalanis/kw_cache_psr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache_psr/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache_psr/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/kw_cache_psr/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_cache_psr)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/kw_cache_psr.svg?v1)](https://packagist.org/packages/alex-kalanis/kw_cache_psr)
[![License](https://poser.pugx.org/alex-kalanis/kw_cache_psr/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_cache_psr)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache_psr/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache_psr/?branch=master)

PSR adapter for connecting that in KWCMS. Use [PSR-16](https://www.php-fig.org/psr/psr-16/). Can use original
kw_cache, kw_files and kw_storage as cache storage.

## PHP Installation

```
{
    "require": {
        "alex-kalanis/kw_cache_psr": "1.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Set storage(s) which will be used by cache.

3.) Connect the "kalanis\kw_cache_psr\*" into your app. Extends it for setting your case.

4.) Just call it
