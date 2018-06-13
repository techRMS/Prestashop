Compatibility
=================================

Compatible with Version 1.7 and above only

Cardstream Payment Gateway module for PrestaShop
=================================


**Step 1:**

Copy the contents of the httpdocs folder into your root PrestaShop directory. If you are asked if you want to replace any existing files, click “Yes”.

**Step 2:**

Log in to the Admin area of PrestaShop, then from the left menu, click Modules and then 'Modules and Services'. Next, from the search box start typing "Cardstream" and when the module shows up below (follow the second paragraph if this does not occur); Click 'Install'. The page should automatically refresh when the module installs. Clicking on 'Configure' will automatically direct you to the module settings.

Otherwise if the module does not show up you can get to the module by going to 'Installed Modules' at the top of the page and configuring the Cardstream Payment Gateway module that is displayed under the 'installed modules' sub-heading before 'built-in modules'.

**Step 3:**

From here, enter your Merchant ID, Currency Code, Country ID and Passphrase. In the 'Frontend' box, enter a sentence asking your customer to pay with Cardstream, i.e. "Process payments with Cardstream", or "Cardstream".

**Step 4:**

Whilst in the module settings, go to 'Manage Hooks' at the top right of the page. Then go to 'Transplant a module' at the top right, select 'Cardstream Payment Gateway' as the module and transplant it to 'displayOrderConfirmation'. Repeat the latter to additionally add the 'displayPaymentReturn' hook.

NOTE:
- Processing direct payments without HTTPS enabled on PrestaShop is prohibited.
- All settings must be saved before use.
- The Frontend box MUST be filled in for the module to work. Click Update Settings. 
- Debug can only be used by developers and debuggers and is not intended for real-time use as it is insecure and can make your shop perform slower.
