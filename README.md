# sobak.pl

This repository holds an engine for my [website]. It's a weird hybrid trying to benefit from both
Static Site Generators concept and typical Laravel setup. Nothing really advanced
in fact but feel free to take inspiration.

The code itself is licensed under [MIT](LICENSE.md). However, it **does not** apply
to the content on [sobak.pl][website] which is proprietary and copying or redistributing
it without my explicit permission is strictly prohibited.

The success/failure icons used for CLI notifications are owned by Jeffrey Way, the author
of [laravel-mix](https://github.com/JeffreyWay/laravel-mix).

## Local development

Suggested setup for the local development (which attached Docker config follows) is to put
this repository in its own directory, next to the directory with compatible website content
files.

Something along the lines of...

```sh
mkdir sobak.pl
cd sobak.pl
git clone git@github.com:Sobak/sobak.pl.git website
# grab website files into "content" directory
cd website && docker-compose up -d
```

Then review the `.env` file and adjust settings if necessary. Finally

```sh
# enter the container
docker-compose exec --user=app app bash

# and run
composer install
php artisan migrate --database=persistent --path=database/migrations/persistent/
php artisan content:index

# for IDE autocompletion
composer ide-helpers
```

You will also need to build the frontend assets:

```sh
docker-compose exec node bash
yarn dev # or `yarn prod` to build with minification etc
```

## See also

- [The dedicated README](database/migrations/README.md) in `database/migrations/` to learn how this engine deals
  with databases

[website]: http://sobak.pl
