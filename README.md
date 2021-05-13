# Space48 Code Quality tool

A module that helps to easily apply static code analysers to the project's code. 
It uses [grumphp](https://github.com/phpro/grumphp) under the hood with a predefined set of rules 
based on Magento 2 coding standards but tuned to be less annoying!

## Usage

After pulling and installing/updating project locally composer hook should install linters automatically. 
It will add precommit git hook, so on each commit linters will run for the files added to commit. 

If that does not happen run following command to update local git hooks and install npm packages:  
```shell
make linters-init
```

### Git Pre-Commit
A commit will fail if linters found errors in your code, check the '_console_' tab in 'Git' section of the PhpStorm 
for errors output by linters. Fix all errors and commit again.

**Warning**
By default Autofix feature is turned ON. Once you try to commit, linters will automatically fix some errors. 
Double check changes after.

When fixing errors, to see predicted linters output you can instead of trying to commit again just run manually from console:
```shell
make precommit
```

### Ignoring Rules
in the linters output you will see hints with rule names being violated. If for some reason you can not fix violated rule
feel free to ignore it using Suppress Warning comment corresponding concrete linter type (see hints in output or google).

**IMPORTANT!**
When ignoring a rule in the code always add a comment with the reason of why you had ignored it instead of fixing.

### Finetuning

Rules can be tuned with parameters, excluded completely or rewritten on a Project Level 
using the `ruleset.xml, phpmd.xml, .eslintrc, .stylelint` files in the project's root folder and committing them to project's repo.

If you think everyone can benefit from your rules changes - feel free to create a PR.

### CI Integration

_(TBD)_

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
Add the module
```shell
warden env exec php-fpm composer require --dev space48/magento2-code-quality
```

When it will ask for 'grumphp.yml' creation - answer `no`: 
```shell
Do you want to create a grumphp.yml file? [Yes]: no
```

Copy files and install npm packages:
```shell
warden env exec php-fpm chmod +x vendor/space48/magento2-code-quality/script/install.sh
warden env exec php-fpm ./vendor/space48/magento2-code-quality/script/install.sh
vendor/bin/grumphp git:init
```

Add configuration files to git:
```shell
git add ruleset.xml phpmd.xml .eslintrc grumphp.yml
```

#### 2. Add following to project`s 'Makefile':
```makefile
linters-init: # init linters on local machine
	warden env exec php-fpm chmod +x vendor/space48/magento2-code-quality/script/install.sh
	warden env exec php-fpm ./vendor/space48/magento2-code-quality/script/install.sh
	vendor/bin/grumphp git:init

analyse: # analyses all code from starting commit hash to HEAD
	git diff a000z999..HEAD | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run

precommit: # analyses code staged for commit
	git diff --staged | warden env run --rm php-fpm 'vendor/phpro/grumphp/bin/grumphp' run
```

Replace the sample `a000z999` commit hash with the hash from the project where you want to start linting from.
Files modified after the starting commit hash will be linted during project build and will fail the build on linter violations. 

#### 3. Add post update/install composer hooks:

composer.json
```json
{
  ...
  "scripts": {
    "post-update-cmd": [
      ...
      "make linters-init"
    ],
    "post-install-cmd": [
      ...
      "make linters-init"
    ]
  }
  ...
}
```

#### 4. Commit to project`s repo.
Commit updated composer files, vendor folder, code-quality config files from the root and 'makefile' changes 

### Installation on any other Magento 2 project:
1. add module `space48/code-quality` via Composer
2. run `vendor/space48/code-quality/script/install.sh` script to copy necessary files and install npm packages
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

Linter rules can be finetuned on a project level by editing `ruleset.xml, phpmd.xml, .eslintrc, .stylelint` files.
See `Space48/code-quality/rulesets/` for examples.

Some rules can be overwritten on a class level. See `rulesets/PhpMd/extra.xml` for examples. 

## More info
For more info and for Configuration help refer to [grumphp repo](https://github.com/phpro/grumphp) docs.
