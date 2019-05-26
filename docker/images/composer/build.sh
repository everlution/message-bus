#!/bin/bash

IMAGE_NAME="everlution-message-bus/composer"
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

docker build -t $IMAGE_NAME $DIR
