### Code Quality section
CQ_STARTING_COMMIT_HASH='a000z999'
CQ_STARTING_COMMIT_DATE='01/01/2021'

linters-init: ## init linters on local machine
	warden env exec php-fpm chmod +x vendor/space48/magento2-code-quality/script/install.sh
	warden env exec php-fpm ./vendor/space48/magento2-code-quality/script/install.sh
	warden env exec php-fpm /bin/bash -c '[ -d .git ] || mkdir .git'
	chmod +x vendor/space48/magento2-code-quality/script/add-hook.sh
	vendor/space48/magento2-code-quality/script/add-hook.sh

analyse: ## analyses all code from starting commit hash to HEAD
	git diff ${CQ_STARTING_COMMIT_HASH}..HEAD | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run

fix: ## analyses all code from starting commit hash to HEAD
	git diff ${CQ_STARTING_COMMIT_HASH}..HEAD | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run --fix

precommit: ## analyses code staged for commit
	git diff --staged | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run

analyse-ci: # Called during build on CI
	git fetch --shallow-since=${CQ_STARTING_COMMIT_DATE}
	git diff ${CQ_STARTING_COMMIT_HASH}..HEAD | vendor/phpro/grumphp/bin/grumphp run
