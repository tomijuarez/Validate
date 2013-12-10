Validation
==========
Tool for validate received data [```POST|GET|FILE```].
Usage
=====
`````php
void setFlags(Array $flags);
`````
The ```array``` param passed in ```setFlags``` contains all the expected values and its types.

Here is a list of the types that you can select.

For data via POST or GET:

- ```int```
- ```float```
- ```alpha```
- ```alphanumeric```
- ```symbols```
- ```path```
- ```nick```
- ```password```
- ```birthday```
- ```email```
- ```name```
- ```country```
- 
For Files (images, videos, music, documents):

- ```image```
  - ```jpg```
  - ```jpeg```
  - ```bmp```
  - ```gif```
  - ```png```
- ```video```
  - ```wmv```
  - ```mp4```
  - ```3gp```
- ```music```
  - ```mp3```
  - ```ogg```
- ```doc```
  - ```doc```
  - ```docx```
  - ```ppt```
  - ```pdf```
  - ```txt```

Once you set the flags, you can now call the ```validate()``` method.

`````php
boolean validate(Array $dataContainer, String category);
`````

The category paramether is the type of the data that must to be validate.

Actually, exists only 5 types of data:
- ```data```
- ```image```
- ```video```
- ```music```
- ```doc```
If some value does not match with the flags that you define, then the validate method will return ```false```, if everything goes ok, it will be return ```true```.

If the data is corrupted, you can get the errors using the ```getErrors()``` method.

`````php
array getErrors();
`````
Here's a list with the possible errors:
- ```keyName must be defined``` -> when you define a flag, but it does not lies in the data.
- ```keyName does not match with the keyValue property``` -> when the data does not have the expected type.
- ```keyName can't be empty``` -> when the data is empty.
- ```There's no file assigned``` -> when there is not any file assigned.
- ```The uploaded file size must be less than 5MB``` -> when the file is bigger than 5MB*.
- ```The uploaded file extension is not allowed``` -> when the file is not in the supported extensions*.

Example
=======
`````php
<?php
require_once 'Validation.php';
$validation = new Validation();
//?title=foo&category=bar will be pass
//?title=15&category=65 or ?title=grte not.

$validation->setFlags ( array ( 'title' => 'alpha', 'category' => 'alpha') ); //data

//The second param is 'data' by default.
$passed = $validation->validate ( $_GET );

if ( !$passed ) { //Some error 
  print '<pre>';
  var_dump($validation->getMessages()); //Get the error description
  print '</pre>';
}
else { //If the expected data is ok
  $validation->setFlags ( array ( 'tmp_name' => 'path', 'size' => 'int', 'name' => 'path' ) ); //file

  $_files = [
        "tmp_name" => @$_FILES ['file']['tmp_name']
      , "size"     => @$_FILES ['file']['size']
      , "name"     => @$_FILES ['file']['name']
  ];

  $filesPassed = $validation->validate ( $_files, 'image' ); //image param to validate an image object

  if ( !$filesPassed ) { //Some error
    print '<pre>';
    var_dump ( $validation->getMessages() ); //get description of the errors
    print '</pre>';
  }
  else {
    print 'GOOD :)'; //everything it's ok
  }
}
`````

Pretty soon
===========
- Add specific types dynamically.
- Better error handling.
- More regular expressions (URL, IPv4, IPv6, Credit Card, Coordinates, Phone, etc.)
- Better support for files```*```.

Contact
=======
If you want to help, please send me an email tomirammstein@gmail.com
