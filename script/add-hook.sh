#!/usr/bin/env bash

ENV=${1:-warden}
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
HOOKSPATH="$(git rev-parse --show-toplevel)/.git/hooks"

if [ -f "$HOOKSPATH/pre-commit" ]; then
    BACKUPFILE="$HOOKSPATH/pre-commit.$(date +'%s').backup"
    echo "you already have 'pre-commit' hook, it was moved to $BACKUPFILE"
    mv "$HOOKSPATH/pre-commit" $BACKUPFILE
fi

cp $DIR/../githooks/$ENV/pre-commit $HOOKSPATH
chmod +x $HOOKSPATH/pre-commit
