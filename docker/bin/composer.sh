#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
IMAGE_NAME='everlution-message-bus/composer'

${DIR}/../images/composer/build.sh

docker run -it --rm \
    --volume $DIR/../../:/app \
    --user www-data \
    --workdir=/app \
    -e COMPOSER_CACHE_DIR='/app/var/cache/composer' \
    $IMAGE_NAME composer $1
