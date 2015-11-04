# Praxigento plugin for PHP Composer to generate local configs from templates

[![Build Status](https://travis-ci.org/praxigento/composer_plugin_templates.svg)](https://travis-ci.org/praxigento/composer_plugin_templates/)

## What is this?

Plugin for PHP Composer to create locally specific configuration from set of template files and from 
single file with configuration parameters.

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
parameter inyour project's *composer.json*::

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
          "events": "post-install-cmd",
          "rewrite": true
        },
        "dump.sh": {
          "src": "test/tmpl/dump.sh",
          "dst": "test/bin/dump_db/dump.sh",
          "rewrite": true,
          "events": [
            "post-install-cmd",
            "post-update-cmd"
          ]
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
Set of the templates to be processed on events:

    {
      "templates": {
        "local.xml": {
          "src": "test/tmpl/local.xml",
          "dst": "test/mage/app/etc/local.xml",
          "events": "post-install-cmd",
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
          },
          "events": [
            "post-install-cmd",
            "post-update-cmd"
          ]
        }
      }
    }

Labels (`local.xml` & `dump.sh`) are for reference only.

* *src*: path to template file;
* *dst*: path to result file (where placeholders are replaced by its values);
* *rewrite*: 'true' to rewrite destination file if exists;
* *condition*: simple condition to process this template file;
    * *var*: name of the variable for left part of the condition;
    * *operation*: one of the two operations ('=' or '!=');
    * *value*: string value for compare (right part of the operation);
* *events*: one event (string) or set of events (array of strings) to fire templates processing on ([available events]);


## License

All contents of this package are licensed under the [MIT license].

[screenshot]: img/screenshot.png
[available events]: https://getcomposer.org/doc/articles/scripts.md#event-names
[MIT license]: LICENSE