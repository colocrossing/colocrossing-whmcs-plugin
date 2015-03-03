ColoCrossing WHMCS Module
===============================
This is a plugin for WHMCS that integrates with the ColoCrossing Portal. It provides you access to and control of all your ColoCrossing devices from within the WHMCS administrator panel. It also allows you to provide your customers access to their devices from the WHMCS client panel.

Installation
-------------------------------
* Download or Git clone the contents of this repository
* Merge the repository directory with the root directory of your WHMCS installation.
* This should add `event.php` to `<whmcs_root>/event.php` and add an addon module to `<whmcs_root>/modules/addons/colocrossing/` and add a provisioning module to `<whmcs_root>/modules/servers/colocrossing/`
* If you are running on a Windows system, you will need to modify the cookie jar location in `<whmcs_root>/modules/addons/colocrossing/API.php`. By default it is set to `/tmp/colocrossing_cookie_jar.txt` for Linux systems. Your Web Server will need permissions to write to the cookie jar file.
* If your WHMCS installation is not at the root of the domain (i.e. example.com/manage/ is your WHMCS client area), then you will need to edit `WHMCS_BASE_URL` in `<whmcs_root>/modules/addons/colocrossing/Configuration.php`. In the previous example, `WHMCS_BASE_URL` would be set to '/manage/'.

Configuration
-------------------------------
* Login to the [ColoCrossing Portal](https://portal.colocrossing.com/api).
* Go to the API section by clicking on the link at the footer of the page.
* Go to the API Keys tab and generate an API Key with the IP address of your WHMCS server.
* Go to the API Webhooks tab and create a webhook for the `Ticket Created` event. Randomly generate a token for the secret. The URL should point to `https://your-whmcs-installation/event.php`.
* Login to the WHMCS administrator panel.
* In WHMCS, setup a support ticket department to handle abuse complaints along with a system user acount to be used by the module.
* Go to Setup > Addon Modules
* Activate the ColoCrossing Portal module.
* Configure the module by entering the API Key and API Webhook you generated above and by setting the access controls, abuse department, and system account accordingly.
* Go to Setup > Products/Services > Products/Services
* Select a product you would like to enable ColoCrossing integrations for.
* Edit the module settings of the product by setting the Module Name to Colocrossing.

Getting Started
-------------------------------
* Go to Addons > ColoCrossing Portal.
* Here you will see a list of your ColoCrossing Devices. In the left side navigation you will see the other ColoCrossing resources you have access to.
* To get started assigning devices to your clients go to the Unassigned Services link in the left side navigation. Here you are able to mass assign ColoCrossing devices whose hostnames match those of existing WHMCS services.
* You can also assign ColoCrossing devices to WHMCS services by navigating to the client and finding the corresponding product/service.

Reporting Issues/Contributing
-------------------------------
If you find an issue with the addon, please report the issue to us by using the repository's issue tracker and we will try to resolve the issue. If you resolve the issue or make other improvements feel free to create a pull request so we can merge it into a future release.