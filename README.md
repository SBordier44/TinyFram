# TinyFram
A small MVC framework for tiny projects

[![Coverage Status](https://coveralls.io/repos/github/NuBOXDevCom/TinyFram/badge.svg?branch=master)](https://coveralls.io/github/NuBOXDevCom/TinyFram?branch=master)
[![Build Status](https://travis-ci.org/NuBOXDevCom/TinyFram.svg?branch=master)](https://travis-ci.org/NuBOXDevCom/TinyFram)
[![License](https://poser.pugx.org/nuboxdevcom/tinyfram/license?format=plastic)](https://packagist.org/packages/nuboxdevcom/tinyfram)
[![Total Downloads](https://poser.pugx.org/nuboxdevcom/tinyfram/downloads?format=plastic)](https://packagist.org/packages/nuboxdevcom/tinyfram)
[![Latest Stable Version](https://poser.pugx.org/nuboxdevcom/tinyfram/v/stable?format=plastic)](https://packagist.org/packages/nuboxdevcom/tinyfram)
[![Latest Unstable Version](https://poser.pugx.org/nuboxdevcom/tinyfram/v/unstable?format=plastic)](https://packagist.org/packages/nuboxdevcom/tinyfram)

#
### Prerequisites
- PHP 7.1 - 7.2
- MySQL >= 5.6
- [Composer](https://getcomposer.org)

#
### Installation
```bash
composer create-project nuboxdevcom/tinyfram TinyFram
```
> _Please follow the interactive installer_
```bash
cd TinyFram
vendor/bin/phinx migrate #Install base tables in database
vendor/bin/phinx seed:run #Install sample datas in database (Include admin user)
```

#
### Launch
```bash
php -S localhost:8000 -t public/ -ddisplay_errors=1
```
Go to in your favorite browser:
> [http://localhost:8000](http://localhost:8000)

#
### Admin Area
- Url: /admin
- Username: admin
- Password: admin

#
### Working...
Application (App\\) is in src/ directory

Framework Core is in Framework/ directory

#
### Running tests
At root of this project, execute
```bash
phpunit
```

#
### Contributing
This repo is open for to contributions and issues

#### Versioning
Please use the [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/NuBOXDevCom/TinyFram/tags). 

#### License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
