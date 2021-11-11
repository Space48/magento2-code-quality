#!/usr/bin/env bash

source npm-install.sh

[ -f ./ruleset.xml ] || cp $DIR/ruleset.xml ./
[ -f ./phpmd.xml ] || cp $DIR/phpmd.xml ./
[ -f ./.eslint ] || cp $DIR/.eslintrc ./
[ -f ./.stylelintrc ] || cp $DIR/.stylelintrc ./
[ -f ./grumphp.yml ] || cp $DIR/grumphp.yml ./
