# Introduction

Use Twig as Default Template Engine

## Manual

### Data access

It is possible to output the data directly with the field name. Example:

```twig
Hello {{ firstname }} {{ lastname }},

Thank you for your message!
```

Furthermore, we have an array in which all fields have been stored. Here is an example of how to access the array:

```twig
{% for key,value in all_values %}
  {{ key }}: {{ value }}
{% endfor %}
```

### Twig Documentation

[Twig Template](https://twig.symfony.com/doc/3.x/templates.html)
