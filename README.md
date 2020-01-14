# jQuery UI Datepicker

[![Latest Stable Version](https://poser.pugx.org/brainsum/jquery_ui_datepicker/v/stable)](https://packagist.org/packages/brainsum/jquery_ui_datepicker)
[![License](https://poser.pugx.org/brainsum/jquery_ui_datepicker/license)](https://packagist.org/packages/brainsum/jquery_ui_datepicker)
[![Build Status](https://travis-ci.org/brainsum/jquery-ui-datepicker.svg?branch=master)](https://travis-ci.org/brainsum/jquery-ui-datepicker)

## About

Adds jQuery datepicker widgets to Drupal 8.

Originally was part of the [Tieto Modules](https://packagist.org/packages/brainsum/jquery_ui_datepicker) umbrella package.

## Breaking change

In 2.1 the static "tieto_date" format was removed, the value is now taken from config. The default for that has been set to "medium".
Sites relying on the "tieto_date" format have to manually set the "date_format" config key back to "tieto_date".
