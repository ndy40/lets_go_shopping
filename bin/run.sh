#!/usr/bin/env bash
set -e
set -u
set -o pipefail

if [[ -f ".env" ]]; then
    source .env
fi

usage() {
    echo "$0 usage: start [-b] [-d]
    Options:
    b -  docker build all images
    d -  run in background" >&2
    exit 1
}

params=''

while getopts "hbd" opt; do
    case $opt in
      b)
        params=' --build'
        ;;
      d)
        params="${params} -d"
        ;;
      h | *)
        usage
        ;;
    esac
done

shift "$(($OPTIND -1))"

docker-compose up $params

