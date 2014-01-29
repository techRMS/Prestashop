Hosted Form Module for PrestaShop
=================================


Installation Instructions

Step 1:
Copy the contents of the httpdocs folder into your root PrestaShop directory. If you
are asked if you want to replace any existing files, click “Yes”.

Step 2:
Log in to the Admin area of PrestaShop, then from the top menu click Modules. Next,
click the plus symbol for the row Payments and Gateways. Finally, click Install on the
Cardstream row.

Step 3:
Click “Configure” below the cardstream module title and then click “Manage Hooks”
from the above menu. Next, click “Transplant a module” top right of page. Select “Cardstream Hosted Form” from the Module dropdown then from the “Hook into” dropdown select “displayPayment” and click save, Next, repeat the previous transplant process, but this time select from the “Hook into” dropdown actionPaymentConfirmation.

![prestashop display payment hook](/images/displayPaymentHook.png)

![prestashop action payment confirmation](/images/actionPaymentConfirmation.png)
