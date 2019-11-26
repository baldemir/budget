[![GitHub issues](https://img.shields.io/github/issues/range-of-motion/budget.svg)](https://github.com/range-of-motion/budget/issues)
[![GitHub stars](https://img.shields.io/github/stars/range-of-motion/budget.svg)](https://github.com/range-of-motion/budget/stargazers)
[![GitHub license](https://img.shields.io/github/license/range-of-motion/budget.svg)](https://github.com/range-of-motion/budget/blob/master/LICENSE)

# Kolay Bütçe

https://kolaybutce.com/

Kolay Bütçe is an open-source web application that helps you keep track of your finances.

![Product](http://berataldemir.com/static/desktop.png)

## Features

* Ability to organize spendings using tags
* Dashboard displaying monthly statistics about your spendings
* Available in multiple languages (English, Dutch, Danish, German)

## Installation

```
composer install --no-dev
yarn install

cp .env.example .env
php artisan key:generate

php artisan storage:link

php artisan migrate

yarn run development

php artisan serve

php artisan queue:work
```

## Database: Seeding

To use the application with test user, you need to call database seeding as below. It will create test user, account etc.

```
php artisan db:seed
```

username : ***test@kolaybutce.com***

password : ***test1234***

## Contact

* [Discord](https://discord.gg/QFQdvy3)
