ColoCrossing Portal API Library
===============================

This is a wrapper library for the ColoCrossing Portal API to be used by PHP applications. It tries to ease the integration of the API into your applications by handling all interactions with API and providing a simple interface to interact with.

Getting Started
-------------------------------
To begin using the Library, the ColoCrossing.php in the src folder in the repository must be included in your application. The ColoCrossing folder inside of the src folder must be in the same directory as ColoCrossing.php.

```php
require_once('/path/to/library/ColoCrossing.php');
```

An instance of the ColoCrossing_Client must be created to interact with the library. This Object is the gateway to all interactions with the library. The API token obtained from the [ColoCrossing Portal](https://portal.colocrossing.com/api/#keys) must be passed into the ColoCrossing_Client by calling setAPIToken.

```php
$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');
```

Alternatively the API Token may be passed in the constructor as seen below.

```php
$colocrossing_client = new ColoCrossing_Client('YOUR_API_TOKEN');
```

If you are running on a Windows system, you will need to modify the cookie jar location in `src/ColoCrossing/Client.php`. By default it is set to `/tmp/colocrossing_cookie_jar.txt` for Linux systems. Your Web Server will need permissions to write to the cookie jar file.

Accessing Resources
-------------------------------
Once the ColoCrossing_Client is created, you can access numerous resources from the client object. The resources available are devices, networks, null_routes, and subnets.

Each resource has a findAll method which will return a ColoCrossing_Collection of ColoCrossing_Object's that can then be iterated using a foreach loop.

```php
$devices = $colocrossing_client->devices->findAll();

foreach ($devices as $key => $device)
{
	echo $device->getName();
}
```

Each resource has a find method which takes an id as the parameter and will return a ColoCrossing_Object.

```php
$device = $colocrossing_client->devices->find($device_id);

echo $device->getName();
```

Using Objects
-------------------------------
The objects returned by the resources have numerous getter methods to retrieve attributes or child objects of the object. To see what methods are available on specific objects, its best to look at the objects code.

```php
echo $device->getName();
echo $device->getNickname();
echo $device->getHostname();
echo $device->getUSize();

$type = $device->getType();
echo $type->getName();

$notes = $device->getNotes()
foreach ($notes as $key => $note)
{
	echo $note->getNote();
}

$assets = $device->getAssets()
foreach ($assets as $key => $asset)
{
	echo $asset->getName();
}
```

Examples
-------------------------------
Numerous examples have been provided in the repository's examples folder. The examples demonstrate how to accomplish most actions possible in the library. You are encouraged to look at these examples to learn the best practices for using the library.

Reporting Issues/Contributing
-------------------------------
If you find an issue with the library, please report the issue to us by using the repository's issue tracker and we will try to resolve the issue. If you resolve the issue or make other improvements feel free to create a pull request so we can merge it into a future release.