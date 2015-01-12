# Praxigento plugin for PHP Composer to generate local configs from templates

## What is this?

Plugin for PHP Composer to create locally specific configuration from set of template files and from 
single file with configuration parameters.

![screenshot]

## Installation

Add to your project's *composer.json*:

    {
      "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/praxigento/composer_plugin_templates"
        }
      ],
      "require": {
        "praxigento/composer_plugin_templates": "*@dev"
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


### Configuration file structure

`./templates.json`:

    {
      "vars": {
        "MYSQL_HOST": "localhost",
        "MYSQL_USER": "magento_github_user",
        "MYSQL_PASSWORD": "s8pTo3X5QCsr4SkY48zF",
        "MYSQL_DBNAME": "magento_github_db"
      },
      "templates": [
        {
          "src": "test/tmpl/local.xml",
          "dst": "test/mage/app/etc/local.xml",
          "events": "post-install-cmd",
          "rewrite": true
        },
        {
          "src": "test/tmpl/dump.sh.init",
          "dst": "test/bin/dump_db/dump.sh",
          "events": [
            "post-install-cmd",
            "post-update-cmd"
          ]
        }
      ]
    }

#### vars
Array of the template's placeholders `${MYSQL_HOST}` and values `localhost` to be inserted into templates:

    {
      "vars": {
        "MYSQL_HOST": "localhost",
        "MYSQL_USER": "magento_github_user",
        "MYSQL_PASSWORD": "s8pTo3X5QCsr4SkY48zF",
        "MYSQL_DBNAME": "magento_github_db"
      }
    }
    
#### templates
Array of the templates to be processed on events:

    {
      "templates": [
        {
          "src": "test/tmpl/local.xml",
          "dst": "test/mage/app/etc/local.xml",
          "events": "post-install-cmd",
          "rewrite": true
        },
        {
          "src": "test/tmpl/dump.sh.init",
          "dst": "test/bin/dump_db/dump.sh",
          "events": [
            "post-install-cmd",
            "post-update-cmd"
          ]
        }
      ]
    }

* *src*: path to template file;
* *dst*: path to result file (where placeholders are replaced by its values);
* *events*: one event (string) or set of events (array of strings) to fire templates processing on ([available events]);
* *rewrite*: 'true' to rewrite destination file if exists;

## License

All contents of this package are licensed under the [MIT license].

[screenshot]: img/screenshot.png
[available events]: https://getcomposer.org/doc/articles/scripts.md#event-names
[MIT license]: LICENSE