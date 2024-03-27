# sobak.pl

## The database and migrations structure

This project uses somehow untypical database setup. This is because database is _not_
a primary source of data for most of the project. Content is stored as Markdown files
with accompanied by few extra configs.

Any time the content changes, a `php artisan content:index` command must be run, which
straight up removes the old SQLite file, creates new one (so it also runs migrations
starting from clean state) and populates it with data.

Think of it as a semi-static-site-generator. Except we don't generate the complete HTML
pages but rather a database which is then read by the application.

There's actually three databases (and their connections) defined in `config/database.php`:

- `indexer`: created when the `php artisan content:index` is running. Once the process is
  completed, the old `website` DB gets deleted and `indexer` database gets renamed into
  `website.sqlite`. This provides essentially zero-downtime deployments
- `website`: created once the indexing process finishes. That database is used to power most
  of the website like blog, projects and pages.
- `permantent`: for a few cases where persistent database storage is needed. Uses MySQL.

Because of that setup, there are some important considerations about database migrations.
Most importantly, there's no typical schema update process for `indexer` nor `website`
databases. Both of them use same schema but since they get created upon running
`php artisan content:index`, there's no point in creating new migrations which update the
schema. Just update old ones and re-run the command.

For this reason, all migrations stored directly in `database/migrations/` are meant for the 
`indexer` and `website` databases only.

The `persistent` database is different, it follows the classic approach of committing the DB 
schema changes into the repository. This database is never recreated so every change must be
done with a regular migration. Migrations for this database are kept in 
`database/migrations/persistent/`

## Running migrations

For the `indexer` and `permanent` databases there's no need to ever invoke migrations manually,
this is done as part of the content indexing process by the `php artisan content:index` command.

For the persistent database, it's migrated using

```sh
php artisan migrate --database=persistent --path=database/migrations/persistent/
```
