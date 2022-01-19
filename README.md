# Space48 Code Quality tool

A module that helps to easily apply static code analysers to the project's code. 
It uses [grumphp](https://github.com/phpro/grumphp) under the hood with a predefined set of rules 
based on Magento 2 coding standards but tuned to be less annoying!

## Usage

### Usage on a regular Space48 Warden based Magento 2 project:

After pulling and installing/updating project locally run following command to update local git hooks and install npm packages:  
```shell
make linters-init
```
It will add precommit git hook, so on each commit linters will run for the files added to commit.

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

### Usage on other projects

Run `vendor/bin/grumphp git:init` to add githook
Grumphp will sniff your code on any git commit

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

To add Code Quality to Bitbucket pipelines as a mandatory step do following.

#### Edit Makefile

Add new command to makefile:
```
analyse-ci:
    git fetch --shallow-since=01/09/2021
    git diff 111999..HEAD | vendor/phpro/grumphp/bin/grumphp run
```
'111999' - is your starting commit hash

#### Configure bitbucket-pipelines.yml

Add new step to your build like this:
```
- step: &code-quality
        name: "Code Quality"
        script:
          - if [ ! -f grumphp.yml ] || [ ! -d vendor/space48/magento2-code-quality ]; then printf 'No Space48 Code quality module found'; exit; fi
          - /bin/bash -c "[ $(grep '^[ ]*eslint:' grumphp.yml) ] || [ $(grep '^[ ]*stylelint:' grumphp.yml) ] && source vendor/space48/magento2-code-quality/script/npm-install.sh"
          - make analyse-ci
```

#### Check locally

To run linters as it would run on CI (from starting commit till HEAD):
```shell
make analyse
```

#### Automatically fix some errors

Use any of this options:

Option 1: Set `fixer/enabled` and `fixer/fix_by_default` to **true** in grumphp.yml file

Option 2: Run command `vendor/phpro/grumphp/bin/grumphp run` with '--fix' option
```shell
vendor/phpro/grumphp/bin/grumphp run --fix
```

## Installation

Add Code Quality tool to the Magento Project:

### Warning!
Eslisnt and Stylelint requires **NodeJs v10** or higher.

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
```

Add git precommit hook:
```shell
chmod +x vendor/space48/magento2-code-quality/script/add-hook.sh
vendor/space48/magento2-code-quality/script/add-hook.sh
```

Create `.git` folder in warden:

(_You can mount .git volume from host to inside container instead, but `grumphp` only requires empty .git folder, 
no much sense in syncronizing all .git/* files adding even more overhead for mutagen._)
```shell
warden env exec php-fpm /bin/bash -c '[ -d .git ] || mkdir .git'
```

Add configuration files to git:
```shell
git add ruleset.xml phpmd.xml .eslintrc .stylelintrc grumphp.yml
```

#### 2. Add following to project`s 'Makefile':
```makefile
### Code Quality section
CQ_STARTING_COMMIT_HASH='a000z999'
CQ_STARTING_COMMIT_DATE='01/01/2021'

-include 'vendor/space48/magento2-code-quality/code-quality.mk'
```
It contains following commands:

- `linters-init` - init linters on local machine
- `analyse` - analyses all code from starting commit hash to HEAD
- `analuse-fix` - analyses all code from starting commit hash to HEAD and fixes autofixable errors 
- `precommit` - analyses code staged for commit 
- `precommit-fix` - analyses code staged for commit and fixes autofixable errors
- `analyse-ci` - Same as 'analyse' but modified to be called during build on CI env

Update `CQ_STARTING_COMMIT_HASH` variable. Replace the sample `a000z999` commit hash with the hash from the project where you want to start linting from.
Files modified after the starting commit hash will be linted during project build and will fail the build on linter violations.

Update `CQ_STARTING_COMMIT_DATE` variable. Replace sample `01/01/2021` date with the date of the Starting Commit
(specified at `CQ_STARTING_COMMIT_HASH` variable). This is required because CI uses shallow git clone.

**Note:**
If using warden, commit still fails with `SplFileInfo::openFile(/var/www/html/.git/COMMIT_EDITMSG): failed to open st  
ream: No such file or directory` error: rename or revert to original git 'commit-msg' hook.

#### 3. Commit to project`s repo.
Commit updated composer files, vendor folder, code-quality config files from the root and 'makefile' changes 

### Installation on any other Magento 2 project:
1. add module `space48/code-quality` via Composer
2. run `vendor/space48/code-quality/script/install.sh` script to copy necessary files and install npm packages
3. in `grumphp.yml` remove configs marked as `(remove on non warden environment)`
4. run `vendor/bin/grumphp git:init` to update precommit hooks

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
