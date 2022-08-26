# ZIP CODE FINDER

_Laravel 9.x project._

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Requirements

This is a Laravel 9.x project, so you must meet its requirements.

### Installing

Clone the project

```bash
git clone git@github.com:stevensgsp/zip-code-finder.git
cd zip-code-finder
composer install
cp .env.example .env
php artisan key:generate
```

Edit .env and put credentials, indicate environment, url and other settings.

Run migrations

```bash
php artisan migrate
```

## Documentation

The project is documented with Swagger open source and professional toolset. You can access the documentation at ```/api/documentation``` endpoint. You could also change the ```APP_URL``` variable in the ```.env``` and generate the documentation using the ```l5-swagger:generate``` Artisan command.

<img src="https://i.imgur.com/UcKS5sT.png">

For more information on the Swagger package used, consult the [L5-Swagger documentation](https://github.com/DarkaOnLine/L5-Swagger/wiki).

## Queues settings

The project uses Laravel's queues API to defer the processing of a time consuming task, such as refresh the zip codes in database, until a later time. By default, the queued jobs will be executed immediately (for local use). To enable the queue functionality you should set the ```QUEUE_CONNECTION``` value to ```database``` on the ```.env```.

```bash
QUEUE_CONNECTION=database
```

By doing that the jobs will be stored in database each time they are called (when the end-point to crawl zip codes is called, for example). You should run the Laravel worker to process new jobs as they are pushed onto the queue. You may run the worker using the ```queue:work``` Artisan command. Note that once the ```queue:work``` command has started, it will continue to run until it is manually stopped or you close your terminal:

```bash
php artisan queue:work
```

To keep the ```queue:work``` process running permanently in the background, you can use a process monitor such as [Supervisor](http://supervisord.org/index.html) to ensure that the queue worker does not stop running.
