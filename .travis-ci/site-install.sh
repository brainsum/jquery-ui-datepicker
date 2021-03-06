#!/usr/bin/env bash
# Note: This should run inside docker.

## @todo: brainsum/tieto_modules:master should install the proper version (e.g for PR-s, etc).
# Or just copy it over from the local version. Maybe docker volume mounting would be the best.
# Install composer dependencies and the site.
## @note: {{ include_placeholder }}
# Hack for seemingly no way to easily add array values.
# @todo: Pull in tieto_modules/patches, too.
composer config extra.patches-file "composer.patches.json" \
  && composer config extra.enable-patching true \
  && composer config extra.merge-plugin.replace false \
  && composer config extra.merge-plugin.merge-dev true \
  && composer config extra.merge-plugin.merge-extra true \
  && composer config discard-changes true \
  && composer require brainsum/jquery_ui_datepicker:master \
  && composer config extra.merge-plugin.include "{{ include_placeholder }}" \
  && sed -i 's/\"{{ include_placeholder }}\"/\[\"web\/modules\/contrib\/jquery_ui_datepicker\/composer.json\"\]/' composer.json \
  && composer update --lock \
  && cd web \
  && drush site-install --site-name="Test" --account-pass=123 --db-url=mysql://drupal:drupal@mariadb/drupal standard -y \
  && drush en jquery_ui_datepicker -y \
  && drush cr
