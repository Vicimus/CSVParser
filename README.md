# CSVParser
A Laravel CSV Parser / Import Wizard

### Installation #

Add to your composer.json:

Require:
```
  "vicimus/csvparser": "dev-master"
```

*Alternatively you can specify "~1" as the version constraint if you want more stable releases.

Repositories:
```
  "url": "git@github.com:Vicimus/CSVParser.git",
  "type": "git"
```

Composer update to install the package. 

Add the service provider to `app/config/app.php`
```
'Vicimus\CSVParser\CSVParserServiceProvider'
```

Make sure you remember to `php artisan asset:publish` as well.

This package requires no migrations.

### General Usage #

Generally you want to insert the interface in a view, to create a seamless experience.

In the view you've created, add the following line:

```php
@include('CSVParser::interface', ['schema' => $schema])
````

The value of `$schema` is the name of the table you are wanting to import data into.

### Styling / Look #

The interface comes with a very basic css. It uses bootstrap as a base (so your parent view must have bootstrap available). Also to ensure responsiveness, it performs best inside of a bootstrap container.

You can override any of the css with your own custom styles by simply including a css file in your view that overrides the classes/styles.
