# Praxigento plugin for PHP Composer to generate local configs from templates

Plugin for PHP Composer to create locally specific configuration from set of template files and from 
single file with configuration parameters.

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
parameter:

    {
      "extra": {
        "praxigento_templates_config": "./templates.json"
      }
    }


### Configuration file sample

    {
      "vars": {
        "MYSQL_HOST": "localhost",
        "MYSQL_USER": "magento_github_user",
        "MYSQL_PASSWORD": "s8pTo3X5QCsr4SkY48zF",
        "MYSQL_DBNAME": "magento_github_db"
      },
      "templates": [
        {
          "src": "tmpl/local.xml",
          "dst": "mage/app/etc/local.xml",
          "events": "post-install-cmd",
          "rewrite": true
        },
        {
          "src": "tmpl/dump.sh.init",
          "dst": "bin/dump_db/dump.sh",
          "events": [
            "post-install-cmd",
            "post-update-cmd"
          ]
        }
      ]
    }

### Configuration file structure

