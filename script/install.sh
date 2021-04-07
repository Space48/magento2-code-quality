#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )/.."

npm install eslint --save-dev

[ -f ./ruleset.xml ] || cp $DIR/ruleset.xml ./
[ -f ./phpmd.xml ] || cp $DIR/phpmd.xml ./
[ -f ./.eslint ] || cp $DIR/.eslint ./
[ -f ./grumphp.yml ] || cp $DIR/rulesets/grumphp.yml ./
