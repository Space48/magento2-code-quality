# Space48 Code Quality tool

A module that helps to easily apply static code analysers to the project's code. 
It uses [grumphp](https://github.com/phpro/grumphp) under the hood with a predefined set of rules 
based on Magento 2 coding standards but tuned to be less annoying!

## Usage

After pulling and installing a project locally just run following command to update local git hooks 
and install npm packages inside Docker container:  
```shell
make linters-install
```
Now on pre-commit either from console or from phpstorm a linter will run against committed code

To run precommit manually:
```shell
make precommit
```
To run linters as it would run on CI (from starting commit till HEAD):
```shell
make analyse
```

## Installation

Add Code Quality tool to the Magento Project:

### Installation on a regular _Space48 Warden based Magento 2_ project:

On Magento versions **earlier than 2.4** add following to 'require' section of project's `composer.json`:
```
"require": {
  ...
  "symfony/options-resolver": "v2.8.52 as 4.4"
}
```

#### 1. Run following commands from project root:
```
warden env exec php-fpm composer config repositories.space48-code-quality vcs git@github.com:Space48/code-quality.git
warden env exec php-fpm composer require --dev space48/code-quality:@dev
warden env exec php-fpm chmod +x ./vendor/space48/code-quality/script/install.sh
warden env exec ./vendor/space48/code-quality/script/install.sh
vendor/bin/grumphp git:init
git add ruleset.xml phpmd.xml .eslintrc grumphp.yml
```

#### 2. Add following to project`s 'Makefile':
```makefile
linters-init: # init linters on local machine
	warden env exec php-fpm chmod +x ./vendor/space48/code-quality/script/install.sh
	warden env exec php-fpm ./vendor/space48/code-quality/script/install.sh
	vendor/bin/grumphp git:init

analyse: # analyses all code from starting commit hash to HEAD
	git diff e111c999..HEAD | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run

precommit: # analyses code staged for commit
	git diff --staged | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run
```

Replace the sample `e111c999` commit hash with the hash from the project where you want to start linting from.
Files modified after the starting commit hash will be linted during project build and will fail the build on linter violations. 

#### 3. Commit to project`s repo.
Commit updated composer files, vendor folder, code-quality config files from the root and 'makefile' changes 

### Installation on any other Magento 2 project:
1. add module `space48/code-quality` via Composer
2. run `vendor/space48/code-quality/script/install.sh` script to copy necessary files
3. run `vendor/bin/grumphp git:init` to update precommit hooks

## Configuration

Whitelist/exclude folders can be configured at `{project_root}/grumphp.yml`:
```yaml
phpmd:
    whitelist_patterns:
        - /^app/
    triggered_by: [ 'php', 'phtml' ]
    exclude: [ ]
phpcs:
    whitelist_patterns:
        - /^app/
    triggered_by: [ 'php', 'phtml' ]
    ignore_patterns: [ ]
eslint:
    triggered_by: [ js, jsx, ts, tsx, vue ]
    whitelist_patterns: [ /^app/ ]
```
To turn off whole linter type (for example 'eslint') - remove or comment out corresponding 'task' section.

Linter rules can be finetuned on a project level by editing `ruleset.xml, phpmd.xml, .eslintrc` files.
See `Space48/code-quality/rulesets/` for examples.

## More info
For more info and for Configuration help refer to [grumphp repo](https://github.com/phpro/grumphp) docs.
