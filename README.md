# Twig SortByField Extension
A Twig Extension filter to sort an array of entries (objects or arrays) by an entry field.

# Install
With composer

    composer require snilius/twig-sort-by-field

# Sample
The list could look like this, but obviously with more than one key per array
.
```php
$base = array(
  array(
    "name" => "Redmine"
  ),
  array(
    "name" => "GitLab"
  ),
  array(
    "name" => "Jenkins"
  ),
  array(
    "name" => "Piwik"
  )
);
```

```twig
{% for item in base | sortbyfield('name') %}

    {{ item.name }}

{% endfor %}
```

# License
    Copyright 2015 Victor Häggqvist

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
