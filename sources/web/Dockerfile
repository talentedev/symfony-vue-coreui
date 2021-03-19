FROM talentedev/php:7.2-v2-apache-node10

ENV APACHE_DOCUMENT_ROOT="public" \
    PHP_EXTENSION_GD="1"\
    APP_ENV="prod" \
    APP_SECRET=

COPY --chown=docker:docker . .

RUN composer install &&\
    yarn install &&\
    yarn build
