#!/bin/sh

mkdir -p .git/hooks

if [ -f .git/hooks/pre-commit ]; then
    mv .git/hooks/pre-commit .git/hooks/pre-commit.old
fi;

cp ./script/hooks/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit