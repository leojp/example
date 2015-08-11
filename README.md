# Example of Web Service REST with Silex

This Example provides the location data of an Instagram multimedia resource.

By providing an identifier number belonging to an Instagram media resource, the service returns geopoint data (latitude and longitude), location information (including number, route, locality, country, etc.), and a grop of names of near places (for reference purposes) to the place where it was taken or recorded.

This service uses Instagram web services and Gmap web services as vendors. Using his publics APIs.


##Requirements

* Apache web services with PHP 5.5.0+


##Bootstrap

* Donwload and save the packet inside a folder located inside the web server public folder. We suggest naming the folder where you save the app as 'example'.
* Download the repository inside this folder.
* Then, enter to the 'example' folder from the console.
* If you do not have 'Composer installed in your PC, you can install it by running this command: curl -sS https://getcomposer.org/installer | php
* Then, run the command 'php composer.phar install' to download all the dependencies.
* If you want, you can use your INSTAGRAM TOKEN in the application. In order to do that, go to the config folder and edit the INSTAGRAM_TOKEN value in the Config.php file.
* Once everything is downloaded and configured, run the application.


##Run

* Enter http://localhost/example/api/media/{media-id} in the web explorer. In {media-id}, enter the Instagram media identifier.

You can use the next media-id examples. These contain location data:

* 987728278054679304_144900939
* 965763257150796066_1723176595
* 988290919814027842_308439245

You can also use one example without location data:

* 448979387270691659_45818965


##Filters

You can also add filters to the request by entering these as parameters in the URL.

By entering ?filters=address you obtain position and location data (route, city, country, etc.)

By entering ?filters=places you obtain position and near places data.

By changing the filters order, you can modify the order of the response data. Ej: ?filters=places,address or ?filters=address,places.


##Response format

The response always is returned in JSON format.

When the query execution returns an error, you get a message similar to the following response:

{"reason_phrase":"Bad Request","satus_code":400}


##Test

For running the PhpUnitTest, execute this command from the main aplication folder: vendor\bin\phpunit.
