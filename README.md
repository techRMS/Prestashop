Version 1.6 and Above please use: https://github.com/cardstream/prestashop-hosted-module/tree/1.6

Compatibility
=================================

Compatible with Version 1.5 and 1.6

Hosted Form Module for PrestaShop
=================================


**Step 1:**

Copy the contents of the httpdocs folder into your root PrestaShop directory. If you
are asked if you want to replace any existing files, click “Yes”.

**Step 2:**

Log in to the Admin area of PrestaShop, then from the left menu, click Modules. Next, from the 'Module List', select 'Payments and Gateways'. From the list, select Cardstream and click the green 'install' button on the right.

**Step 3:**

From here, enter your Merchant ID, Currency Code, Country ID and Passphrase. In the 'Frontend' box, enter a sentence asking your customer to pay with Cardstream, i.e. "Process payments with Cardstream". 
NOTE: The Frontend box MUST be filled in for the module to work. Click Update Settings. 

**Step 4:**

Click 'Manage Hooks' from the top right of the page. Next, click “Transplant a module” on the top right of page. Make sure “Cardstream Hosted Form” is selected from the Module dropdown then from the “Hook into” dropdown select “displayPayment” and click save. Next, repeat the previous transplant process, but this time select from the “Hook into” dropdown displayPaymentReturn.

Your payment link to Cardstream is now ready. 

![prestashop display payment hook](/images/cardstream-hook-1.png)

![prestashop action payment confirmation](/images/cardstream-hook-payment-2.png)
