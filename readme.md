# Tumblr

[![Build Status](https://img.shields.io/travis/iPublikuj/tumblr.svg?style=flat-square)](https://travis-ci.org/iPublikuj/tumblr)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/ipub/tumblr.svg?style=flat-square)](https://scrutinizer-ci.com/g/ipub/tumblr/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/tumblr.svg?style=flat-square)](https://packagist.org/packages/ipub/tumblr)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/tumblr.svg?style=flat-square)](https://packagist.org/packages/ipub/tumblr)

Tumblr API client with authorization for [Nette Framework](http://nette.org/)

## Installation

The best way to install ipub/tumblr is using  [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/tumblr
```

After that you have to register extension in config.neon.

```neon
extensions:
	tumblr: IPub\Tumblr\DI\TumblrExtension
```

> NOTE: Don't forget to register [OAuth extension](http://github.com/iPublikuj/oauth), because this extension is depended on it!

## Documentation

Learn how to authenticate the user using Tumblr's oauth or call Tumblr's api in [documentation](https://bitbucket.org/ipub/tumblr/src/373fda8385e4d1eef626a2be0e5dcda664a007a2/docs/en/index.md?at=master).

***
Homepage [http://www.ipublikuj.eu](http://www.ipublikuj.eu) and repository [http://bitbucket.org/ipub/tumblr](http://bitbucket.org/ipub/tumblr).