#!/bin/bash
color_error="\033[30;41m"
color_success="\033[30;42m"
color_reset="\033[0m"

# find out the reference commit or commit range
if [ "$TRAVIS_PULL_REQUEST" != "false" ] && [ ! -z "$TRAVIS_COMMIT_RANGE" ]; then
    COMMIT_RANGE=$TRAVIS_COMMIT_RANGE
else
    COMMIT_RANGE="$(git rev-parse HEAD~1)..$(git rev-parse HEAD)"
fi

# list the php files that have been modified
echo "Finding out which PHP files have changed between refs $COMMIT_RANGE ..."
PHP_CHANGED_FILES=$(git diff --name-only "$COMMIT_RANGE" | grep "\.php$")

# check php syntax
if [ -z "$PHP_CHANGED_FILES" ]; then
    echo "Nothing changed"
    php="0"
else
    echo -e "$PHP_CHANGED_FILES\n"

    echo "Checking PHP syntax..."
    ! (echo "$PHP_CHANGED_FILES" | tr '\n' '\0' | xargs -0 -n 1 -P 4 -I {} sh -c "php -d display_errors=on -l {} 2> /dev/null || true " | grep "Parse error")
    php=$?
    if [ "$php" -ne "0" ]; then
        echo -e "$color_error [PHP linter] Some PHP files have errors! $color_reset"
    else
        echo -e "$color_success [OK] File syntax looks good! $color_reset"
    fi
fi

#twig tests
php app/console lint:twig src
twig_src=$?

php app/console lint:twig app
twig_app=$?

#yml tests
php app/console lint:yaml src
yaml_src=$?

php app/console lint:yaml app
yaml_app=$?

php app/console lint:yaml themes
yaml_themes=$?

php app/console lint:yaml .t9n.yml
yaml_trad=$?

# php code sniffer
php_codesniffer="0"
php_codesniffer_legacy="0"
if [ ! -z "$PHP_CHANGED_FILES" ]; then
    # skip ignored files (see phpcs-ignore file)
    PHP_FILES_TO_SNIFF=$(echo "$PHP_CHANGED_FILES" | grep -v -f tests/phpcs-ignore)

    if [ ! -z "$PHP_FILES_TO_SNIFF" ]; then
        echo "Starting PHP Sniffer..."
        php vendor/squizlabs/php_codesniffer/bin/phpcs --version

        # legacy files aren't namespaced, so we need to tell them apart so they can be tested differently
        LEGACY_FILES=$(echo "$PHP_CHANGED_FILES" | grep -E -v "^(src|tests)/")
        if [ ! -z "$LEGACY_FILES" ]; then
            echo -e "Performing sniff with legacy constraints on the following files: \n$LEGACY_FILES\n"
            php vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 -s -n --exclude=PSR1.Classes.ClassDeclaration $LEGACY_FILES
            php_codesniffer_legacy=$?

            # remove the legacy files from the list of files to sniff
            PHP_FILES_TO_SNIFF=$(comm -23 <(sort <(echo "$PHP_FILES_TO_SNIFF")) <(sort <(echo "$LEGACY_FILES")))
        fi

        # perform the full sniff on the rest of the files (if any)
        if [ ! -z "$PHP_FILES_TO_SNIFF" ]; then
            echo -e "Performing full sniff on the following files: \n$PHP_FILES_TO_SNIFF\n"
            php vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 -s -n $PHP_FILES_TO_SNIFF
            php_codesniffer=$?
        fi

        if [[ "$php_codesniffer" -eq "0" && "$php_codesniffer_legacy" -eq "0" ]]; then
            echo -e "$color_success [OK] Code style looks good! $color_reset"
        else
            echo -e "$color_error [PHP Code Sniffer] Some PHP files have errors! $color_reset"
        fi
    fi
fi

if [[ "$php" == "0" \
    && "$twig_src" == "0" \
    && "$twig_app" == "0" \
    && "$yaml_src" == "0" \
    && "$yaml_app" == "0" \
    && "$yaml_themes" == "0" \
    && "$yaml_trad" == "0" \
    && "$php_codesniffer" == "0" \
    &&  "$php_codesniffer_legacy" == "0" ]]; then
    exit 0;
else
    exit 255;
fi
