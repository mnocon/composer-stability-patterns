# Composer Stability Patterns

## Description

This plugin allows you to specify Composer's minimum stability using more granular patterns, similar as the `preferred-install` and `allow-plugins` options do.

## Instalation

The plugin needs to be active during the `pre-pool-create` event - meaning that in order to work it needs to be already installed and activated before that. That's why global installation is needed:

```bash
composer global require mareknocon/composer-stability-patterns
```

## Examples

Specify the patterns and their stability level in the `exta` section in `composer.json` file:
```json
    "extra": {
        "minimum-stability": {
            "my-organization/specific-package": "stable",
            "my-organization/*": "dev",
            "partner-organization/*": "rc",
            "*": "stable"
        }
    }
```

## Doc

Together with this plugin there are three options to specify minimum stability, which are resolved in the following order:
1. Package stability level
     
    Directly specified stability (`@dev`, `@alpha`, `@beta`, `@RC`) for a package will be used.
1. Granular stability level

    If package name matches one of the specified patterns then the stability level of the first matching pattern will be used

1.  Global stability level


