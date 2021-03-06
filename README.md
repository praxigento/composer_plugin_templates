# Praxigento plugin for PHP Composer to generate local configs from templates

[![Build Status](https://travis-ci.org/praxigento/composer_plugin_templates.svg)](https://travis-ci.org/praxigento/composer_plugin_templates/)

## What is this?

Plugin for PHP Composer to create locally specific configuration from set of template files and files with 
configuration parameters. Templates processing is occurred before any composer command (excluding 'install' cause 
plugin is not installed before command have been executed).

![screenshot]

## Installation

Add to your project's *composer.json*:

    {
      "require": {
        "praxigento/composer_plugin_templates": "*"
      }
    }


## Usage

### Setup configuration file

Create plugin's configuration file ( _./templates.json_, for example) and setup **extra.praxigento_templates_config** 
parameter in your project's *composer.json*::

    {
      "extra": {
        "praxigento_templates_config": "./templates.json"
      }
    }

Plugin can use more than one configuration file, data from all files will be merged into one config structure:

    {
      "extra": {
        "praxigento_templates_config": ["./under_vc.json", "./not_under_vc.json"]
      }
    }

This can be useful in case when set of templates is the same for all instances (this part of configuration 
can be under version control) and variables (all or part of them) are unique for each instance. 


### Configuration file structure

`./templates.json`:

    {
      "vars": {
        "MYSQL_HOST": "localhost",
        "MYSQL_USER": "magento_github_user",
        "MYSQL_PASSWORD": "s8pTo3X5QCsr4SkY48zF",
        "MYSQL_DBNAME": "magento_github_db"
      },
      "templates": {
        "local.xml": {
          "src": "test/tmpl/local.xml",
          "dst": "test/mage/app/etc/local.xml",
          "rewrite": true
        },
        "dump.sh": {
          "src": "test/tmpl/dump.sh",
          "dst": "test/bin/dump_db/dump.sh",
          "rewrite": true
        }
      }
    }

#### vars
Set of the template's placeholders `${MYSQL_HOST}` and values `localhost` to be inserted into templates:

    {
      "vars": {
        "MYSQL_HOST": "localhost",
        "MYSQL_USER": "magento_github_user",
        "MYSQL_PASSWORD": "s8pTo3X5QCsr4SkY48zF",
        "MYSQL_DBNAME": "magento_github_db"
      }
    }
    
#### templates
Set of the templates to be processed on every composer command (install, update, status, ...):

    {
      "templates": {
        "local.xml": {
          "src": "test/tmpl/local.xml",
          "dst": "test/mage/app/etc/local.xml",
          "rewrite": true
        },
        "dump.sh": {
          "src": "test/tmpl/dump.sh",
          "dst": "test/bin/dump_db/dump.sh",
          "rewrite": true,
          "condition": {
            "var": "MYSQL_HOST",
            "operation": "!=",
            "value": "localhost"
          }
        }
      }
    }

Labels (`local.xml` & `dump.sh`) are for reference only.

* *src*: _(required)_ path to template file;
* *dst*: _(required)_ path to result file (where placeholders are replaced by its values);
* *rewrite*: 'true' to rewrite destination file if exists ('false' by default);
* *condition*: simple condition to process this template file;
    * *var*: name of the variable for left part of the condition;
    * *operation*: one of the two operations ('=' or '!=');
    * *value*: string value for compare (right part of the operation);


## License

All contents of this package are licensed under the [MIT license].

[screenshot]: img/screenshot.png
[MIT license]: LICENSE