=== QR Module ===
Contributors: kmudigitalisierung
Tags: QR Rechnung,QR Invoice,QR Facture,QR Fattura
Requires at least: 5.5
Tested up to: 6.0
Stable tag: 1.0.10
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 5.5.0
WC tested up to: 5.5.2

With this plugin you can create QR payment parts or even complete QR invoices according to the official Swiss specifications.

== Description ==

From autumn 2022, payment slips will disappear and be replaced by QR bills or QR payment parts. With this plugin you can create QR invoices that comply with Swiss standards.

This plugin can:
- Create QR invoices and send them to your customers via WooCommerce.
- Create QR invoices according to your design specifications.
- Send QR invoices optionally by post.

Install the plugin and create an account at https://qrmodul.ch/en/step-by-step/. On QR Modul you can manage your data and create invoices according to your design requirements. When a customer purchases from your Webshop (WooCommerce), a QR invoice is created based on this order and your settings and sent to the customer by email from your website. 
In addition, you have the option of sending the invoices by post via the QR module. Invoices sent by post are often paid more quickly than by e-mail. Therefore, QR Module offers the flexibility for both options.

QR Module Video Tutorial:
[youtube https://www.youtube.com/watch?v=Ja07ZEoAbK4]


<h2>Installing the plugin</h2>

To install the plugin from your WordPress Backend:
1. Navigate to Plugins > Add New.
2. Enter QR Module in the search field on the top left side.
3. Click Install Now.
4. Click Activate.

<h2>Setting up the plugin</h2>

To setup the plugin:
1. Navigate to Settings > QR Invoice Plugin.

Fill out the fields:
<strong>Client ID & Client Secret</strong>
– Get from QR Modul account to verify 

<strong>Client Token Duration</strong>
– Default value 2592000

<strong>Profile ID</strong> 
– If this field is left blank, then the default profile is used.
– You can get a Profile ID on the page Master data.

<strong>Send Invoice</strong>
– If checked, then invoices will send via Postal Mail. This chargeable option is billed via QR module. 
– By default, invoices are sent via WooCommerce as an email including the QR payment part.

<strong>Letter Dispatch Priority</strong>
– The letter's priority. The possible values are: 'standard' or 'express'.

<strong>My Climate</strong>
– A flag indicating if your invoice shall be clima compensated via My Climate. This chargeable option is billed via QR module.

<strong>Colour</strong>
– The letter's colour. The possible values are: 'Colour' or 'Black and White'.

<strong>Franking Envelopes</strong>
– The letter's franking envelopes. The possible values are: 'A', 'B' or 'none'. If the generated file is the payment part only then 'none' is the valid option.
– If the generated file is a template invoice then 'A', 'B' or 'none' are valid options.

<strong>Address</strong>
– The address to send the letter. The address cannot contain more than 4 lines, with a max of 35 characters for each line.

<strong>Postal Tariff</strong>
– The collective address' postal tariff. The possible values are: 'postpac_priority' or 'swiss_express'.

<strong>Comments for the Printshop</strong>
– The letter dispatch's comments for the printshop. The comments cannot contain more than 2 lines, with a max of 45 characters for each line. 
If the envelope should contain your logo (add-on Service) then you can mention it here.

<strong>Type Invoice</strong>
– If checked, invoices will display only the payment part 
– By default, invoices display the products part and QR payment part. The design of the invoice can be set up in your QR Modul Account.

<strong>Invoice Template Name</strong>
– If this field is left blank, then the default template invoice is used.
– You can get the Invoice Template Name on the page Invoice Template in the Template Name column.

<strong>Invoice VAT</strong>
– A flag to indicate to the system if it should calculate the VAT based on the amount or over the amount.



<h2>Setting up the reference information</h2>

<strong>Reference Type</strong>
– The type of reference to generate. The possible values are: 'QRR','SCOR'.

<strong>Reference Custom Id</strong>
– The id to use to generate reference. i if the reference type is QRR the maximum length of this id is 10, other hand if is SCOR the length is 6.

<strong>Reference Date</strong>
– The date to use to generate reference. The format of date is DD/MM/YYYY

After filling out all fields, click the button Connection & Authorization. After the page is reloaded, click the Save Changes button.



<h2>Uninstalling the plugin</h2>

To uninstall the plugin:
1. Go to Plugins.
2. Under All Extensions, you can view all the installed plugins.
3. Browse for QR Invoice Plugin and click the corresponding Deactivate link.
4. Click Delete to confirm and uninstall the plugin.


== Frequently Asked Questions ==

= Is this Plugin free =

Yes, download and installation of the Plugin is free, but you need an account on QRModul.ch to get the access key and be able to generate QR Invoices. However, there are costs for the use of this account on QR Modul. 

= Is this Plugin Secure =

QR Invoices are generated in QR Modul and send to the website to be attached in the e-Mail to the client. Data of the invoices is stored in Switzerland on a Swiss server and hosting provider of QR Modul.

= Does the QR Code work at any Bank or Post =

Yes, absolutely. QR Module is developed based on the Swiss payment standards and it is regularly updated when new payment rules are set up by SIX as the owner of this payment standard. 
QR Invoices, generated with QR Modul can be used at any Swiss Bank or Post counter.


== Screenshots ==

1. This screen shot „QR Invoice Plugin Set up QR Modul Schweiz.png“ shows the Backend where you need to enter the key to connect your QR Module Account.
2. Create a account to get the key to create QR Invoices, screen shot: Create a account on qrmodul.png

== Changelog ==

= 1.0.10 =
* Fix bug  

= 1.0.9 =
* Add Translation  

= 1.0.8 =
* Add VAT on Shipping Costs  

= 1.0.7 =
* Display shipping price 

= 1.0.6 =
* Fix invoice name 

= 1.0.5 =
* Add new parameters 

= 1.0.1 =
* Fixed Settings Link 

= 1.0.1 =
* Added function to generate Reference Number 

= 1.0.0 =
* A change since the previous version.
* Another change.

== Upgrade Notice ==
= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.