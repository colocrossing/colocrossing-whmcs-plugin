ColoCrossing WHMCS Module
===============================
This is a plugin for WHMCS that integrates with the ColoCrossing Portal. It provides you access to and control of all your ColoCrossing devices from within the WHMCS administrator panel. It also allows you to provide your customers access to their devices from the WHMCS client panel.

Installation
-------------------------------
* Download or Git clone the contents of this repository
* Merge the `modules` with the `modules` directory in the root directory of your WHMCS installation.
* This should add an addon module to `<whmcs_root>/modules/addons/colocrossing/` and a provisioning module to `<whmcs_root>/modules/servers/colocrossing/`
* Download or Git clone the contents of the [Portal API Library](https://github.com/colocrossing/portal-api-php) repository into `<whmcs_root>/modules/servers/colocrossing/api/`.
* This should result in the library being at `<whmcs_root>/modules/servers/colocrossing/api/src/ColoCrossing.php`

Configuration
-------------------------------
* Login to the [ColoCrossing Portal](https://portal.colocrossing.com/api).
* Go to the API section by clicking on the link at the footer of the page.
* Go to the API Keys tab and generate an API Key with the IP address of your WHMCS server.
* Login to the WHMCS administrator panel.
* Go to Setup > Addon Modules
* Activate the ColoCrossing Portal module.
* Configure the module by entering the API Key you generated above and by setting the access controls accordingly.
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