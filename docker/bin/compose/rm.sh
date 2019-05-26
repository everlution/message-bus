#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

docker-compose -f $DIR/../../compose/everlution-message-bus/docker-compose.yml rm -f

