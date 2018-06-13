{*
* 2018 Cardstream
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*
*
*  @author Matthew James <support@cardstream.com>
*  @copyright  2018 Cardstream Ltd
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<form action="{$url}" method="post" id="payment-form" style="margin-top: 1em;">

  <div class="form-group row">
    <label class='col-md-3 form-control-label required'>{l s='Card number'}</label>
    <div class="col-md-6">
      <input class='form-control' type="text" size="20" maxlength=20 autocomplete="off" name="cardNumber" required>
    </div>
  </div>

  <div class="form-group row">
    <label class='col-md-3 form-control-label required'>{l s='CVC'}</label>
    <div class="col-md-6">
      <input class='form-control' type="text" size="4" maxlength=4 autocomplete="off" name="cardCVV" required>
    </div>
  </div>

  <div class="form-group row">
    <label class='col-md-3 form-control-label required'>{l s='Expiration (MM/YY)'}</label>
    <div class="col-md-2">
      <input class='form-control' id="month" name="cardExpiryMonth" size=2 maxlength=2 required>
    </div>
    <h3 class='col-md-1 col-form-label'>/</h3>
    <div class="col-md-3">
      <input class='form-control' id="year" name="cardExpiryYear" size=4 maxlength=4 required>
    </div>
  </div>
</form>