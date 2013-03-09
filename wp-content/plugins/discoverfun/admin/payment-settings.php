<?php

/*
Bank
Type
Account Name
Account Number
Currency
Branch
*/


function init_payment_manager() {
  if($_POST)
  { 
      $data = $_POST;
      switch($data['payment_type']) {
        case 'paypal':
          $options = array('paypal_mode'          => $data['paypal_mode'],
                           'paypal_username'      => $data['paypal_username'],
                           'paypal_pwd'           => $data['paypal_pwd'],
                           'paypal_signature'     => $data['paypal_signature'],
                           'paypal_currency_code' => $data['paypal_currency_code'],
                           'paypal_return_url'    => $data['paypal_return_url'],
                           'paypal_cancel_url'    => $data['paypal_cancel_url']);
          update_option('paypal', $options);
          break;
        case 'bank-transfer':
          echo 'test';
          break;

      }

      
  }  


 $page =  $_GET['page']; 
 $tab = (isset($_GET['tab']) && $_GET['tab'] != '' ) ?  $_GET['tab'] : 'paypal';

?>
<style type="text/css">
  <?php echo '#'.$tab; ?>{
    display:block !important;
  }
  <?php echo '.'. $tab; ?>{ border: solid 1px red;
    background: red;
    color: white;
}
</style>

  <div class="wrap">
    <div id="icon-options-general" class="icon32 icon32-posts-post"><br></div>
    <h2><?php _e('Payment Settings') ?></h2>
    <ul id="highlight-tabs" style="">
      <li><a href="admin.php?page=<?php echo $page; ?>&tab=paypal" class="paypal"><?php _e('Paypal'); ?></a></li> 
      <li><a href="admin.php?page=<?php echo $page; ?>&tab=credit-card" class="credit-card"><?php _e('Credit Card'); ?></a></li> 
      <li><a href="admin.php?page=<?php echo $page; ?>&tab=bank-transfer" class="bank-transfer"><?php _e('Bank Transfer'); ?></a></li> 
    </ul>
    <div id="tabs-wrapper"> 
        <div class="tabs" id="paypal">
            <div class="content">
              <form method="post" action="#">
                <?php 
                    if(is_array(get_option('paypal'))) 
                    {
                      extract(get_option('paypal'));
                    }
                ?>
                    
                <p>
                  <label for="paypal_mode"><?php _e('Paypal Mode');?></label>
                  <select name="paypal_mode">
                    <option value="sandbox">sandbox</option>
                    <option value="live">live</option>
                  </select>
                </p>
                <p>
                  <label for="paypal_username"><?php _e('Paypal API Username');?></label>
                  <input type="text" name="paypal_username" value="<?php echo $paypal_username;?>" />
                </p>
                <p>
                  <label for="paypal_pwd"><?php _e('Paypal Password');?></label>
                  <input type="text" name="paypal_pwd" value="<?php echo $paypal_pwd;?>" />
                </p>
                <p>
                  <label for="paypal_signature"><?php _e('Paypal Signature');?></label>
                  <input type="text" name="paypal_signature" value="<?php echo $paypal_signature;?>" />
                </p>
                <p>
                  <label for="paypal_currency_code"><?php _e('Currency Code');?></label>
                  <input type="text" name="paypal_currency_code" value="<?php echo $paypal_currency_code; ?>" />
                </p>
                <p>
                  <label for="paypal_return_url"><?php _e('Return Url');?></label>
                  <input type="text" name="paypal_return_url" value="<?php echo $paypal_return_url;?>" />
                </p>
                <p>
                  <label for="paypal_cancel_url"><?php _e('Cancel Url');?></label>
                  <input type="text" name="paypal_cancel_url" value="<?php echo $paypal_cancel_url; ?>" />
                </p>
                <input type="hidden" name="payment_type" value="paypal" />
                 <?php submit_button(); ?>
            </form>
            </div>
        </div><!--.tabs-->
        <div class="tabs" id="credit-card">

        </div><!--.tabs-->
        <div class="tabs" id="bank-transfer">
           <div class="content">
              <form action="#" method="post">
                <p>
                  <label for="bank"><?php _e('Bank');?></label>
                  <input type="text" name="bank" value="" />
                </p>
                 <p>
                  <label for="bank_type"><?php _e('Bank Type');?></label>
                  <input type="text" name="bank" value="" />
                </p>
                 <p>
                  <label for="accnt_name"><?php _e('Account Name');?></label>
                  <input type="text" name="accnt_name" value="" />
                </p>
                <p>
                  <label for="currency"><?php _e('Currency');?></label>
                  <input type="text" name="currency" value="" />
                </p>
                <p>
                  <label for="branch"><?php _e('Branch');?></label>
                  <input type="text" name="branch" value="" />
                </p>
                  <input type="hidden" name="payment_type" value="bank-transfer" />
                  <?php submit_button(); ?>
              </form>
           </div>
        </div><!--.tabs-->
      </div><!--#tabs-wrapper-->
  </div>
<?php

}// init_payment_manager
