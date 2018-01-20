# TinyFram
A small MVC framework for tiny projects

[![Travis Build Status](https://img.shields.io/travis/NuBOXDevCom/TinyFram/master.svg?style=flat-square)](https://travis-ci.org/NuBOXDevCom/TinyFram)
[![license](https://img.shields.io/github/license/NuBOXDevCom/TinyFram.svg?style=flat-square)](https://github.com/NuBOXDevCom/TinyFram/blob/master/LICENSE.md)
[![Packagist stable version](https://img.shields.io/packagist/v/nuboxdevcom/tinyfram.svg?style=flat-square)](https://packagist.org/packages/nuboxdevcom/tinyfram)
[![GitHub issues](https://img.shields.io/github/issues/nuboxdevcom/tinyfram.svg?style=flat-square)](https://github.com/NuBOXDevCom/TinyFram/issues)
[![GitHub pull requests](https://img.shields.io/github/issues-pr/nuboxdevcom/tinyfram.svg?style=flat-square)](https://github.com/NuBOXDevCom/TinyFram/pulls)

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
php -S localhost:8000 -t public/
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
