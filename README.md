# Perception

Perception is an engine for my [homepage], a shameless attempt to benefit from both
Static Site Generators concept and typical Laravel setup. Nothing really advanced
in fact but feel free to take inspiration.

The code itself is licensed under [MIT](LICENSE.md). However, it **DOES NOT** apply
to the page content which is proprietary and copying it without my explicit permission
is strictly forbidden.

The success/failure icons used for CLI notifications are owned by Jeffrey Way, author
of [laravel-mix](https://github.com/JeffreyWay/laravel-mix).

## Database setup

Check the [dedicated README](database/migrations/README.md) file in `database/migrations/`

## Local development

Suggested setup for the local development (which attached Docker config follows) is to put
this repository in its own directory, besides the directory with compatible website content
files.

Something along the lines of...

```sh
mkdir sobak.pl
cd sobak.pl
git clone git@github.com:Sobak/sobak.pl.git website
# grab website files into "content' directory
cd website && docker-compose up -d
```

Then review `.env` and adjust settings if necessary. Finally, the container and run

```sh
composer install
php artisan migrate --database=persistent --path=database/migrations/persistent/
php artisan content:index
```

[homepage]: http://sobak.pl
