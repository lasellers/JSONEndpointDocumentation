# JSONEndpointDocumentation
Creates REST API Endpoint documentation returned as JSON indexing per class (PHP/Laravel 5 or Lumen)

This class (for PHP/Laravel 5) scans the DOCBLOCK comments for a function and generates a JSON-able object
that contains an index of public functions and various data that is for use by developers to consume a public API.
  
 In other words, if you have Endpoints in a class such as:
 
````
 /api 
 /api/posts
 /api/post?id=5
 ````
 then you could call the createJSONForAllClassFunctions function on the index function (ie, /api) and return the
 JSON version of the data. This acts as a GET-able index of the functions and descriptions of their use.

See https://github.com/lasellers/JSONEndpointDocumentation/blob/master/app/JSONEndpointDocumentation.php for the actual class.

![JSONEndpointDocumentation](https://github.com/lasellers/JSONEndpointDocumentation/blob/master/screenshot1.png)

## Installation / Use

If you setup the project using Laravel Homestead (vagrant) and alias the local host as  _jsonendpointdocumentation_ 
then the example api is browseable as.
```
http://jsonendpointdocumentation.app/api
```

Copy / Paste the JSONEndpointDocumentation.php file into your project. See the APIController.php 
file for an example of how it create an instance and call the function.

You may need to change the namespacing, depending on which framework you use and how you organize 
your classes.

## Versions

 * **0.0.5** 
 Updated documentation and allowed documentTypes.
 
 * **0.0.4**
  Noticed an issue with the original regex that extracted the phpdoc:
  (\/\*\*)([^*][^\/]*)(\*[\/])(\s*)(\w*)\s(function)\s(\w*)\(
  It would skip docblocks that had urls in them. Reworked to overcome that bug.
  Also fixed camelCase bug.
  Todo: Auto-pop summary + description?
 
 * **0.0.3**
  Add auto url, summary, description
 
 * **0.0.2**
 Started as Trait, but the pollution of names in the class the trait was used in
 bothered me, so changed to an instance class.
 
 * **0.0.1**
  Initially we tried using ReflectorClass and getDocComment to get at the function comments,
  however when used on a production server with opcache enabled (and what production server isn't using an opcache these days?)
  the comments may be stripped. Problem. The next obvious solution thus is to read the source file and regex out the
  comments ourselves.
  