The addon comes in the form of a zip file. To install the addon, there are 2 ways of doing so:

1) First method: FTP the zip file to your server in the WHMCS modules/addons directory and unzip it there. This can usually be done using modern FTP tools such as Filezilla or even using the cPanel File Manager if you have a cPanel hosting account.

2) Second method: Unzip the files locally on your PC and then transfer the files in binary mode using FTP. Make sure the transfer mode is set to binary or you will have messages saying the files are corrupt when you try to activate or use the addon.
To activate the addon, go to Setup > Addon Modules in WHMCS. This will display a list of all installed addons, amongst which you will see the Facebook Promotions addon.

Make sure you tick the ‘Full Administrator’ access control flag. 

Click ‘Save Changes’ at the bottom of the page when you’re done.

To set up the module, please refer to the included [documentation](Documentation.pdf).

Enjoy the module!

=== Changelog ===

= v1.3.1 =
* Add documentation

= v1.3.0 =
* Released under GPL

= v1.2.1 =
* Updated link to documentation

= v1.2.0 =
* Added possibility to define a promotions text appearing on the shopping cart pages

= v1.1.1 =
* Removed Facebook Login button from page (use default Login button instead)
* Improved checking of Facebook signed request

= v1.1.0 =
* Don't display issued FB promo codes in FB promo addon administration menu

= v1.0.6 =
* Display error message in case FB parsed request can't be processed

= v1.0.5 =
* Minor fixes

= v1.0.4 =
* Updated license expiry date

= v1.0.3 =
* Changed to ascii encoding

= v1.0.2 =
* Fixed issue with ?m=fbpromo redirecting to WHMCS main page

= v1.0.1 =
* Removed trailing 'exists' text

= v1.0.0 =
* Initial release
