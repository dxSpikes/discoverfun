<?php
function init_accomodation() {
  include_once(PLUGIN_PATH ."class/config.php");
  global $wpdb;

  if( $_POST ) 
  {
      $data = $_POST;
      $result = validate_accomodation_fields($data);
      
      if($result['error_count'] == 0) 
      {
          process_payment();
      } 
      else 
      {
          display_message($result);
      }

  }

  $countries  = get_country();
  $transports = get_transportation();
?>
  <form method="post" action="#"> <!-- 94-->    
    <div class="left-column">
      <p>
        <label for="firstname"><?php _e('First Name:'); ?></label>  
        <input type="text" name="firstname" value="<?php echo $data['firstname']; ?>" />
      </p>
      <p>
        <label for="lastname"><?php _e('Last Name:'); ?></label>  
        <input type="text" name="lastname" value="<?php echo $data['lastname']; ?>" />
      </p>
      <p>
        <label for="email"><?php _e('Email Address:'); ?></label>  
        <input type="text" name="email" value="<?php echo $data['email']; ?>" />
      </p>
      <p>
        <label for="confirm_email"><?php _e('Confirm Email:'); ?></label>  
        <input type="text" name="confirm_email" value="<?php echo $data['confirm_email']; ?>" />
      </p>
      <p>
        <label for="mobile"><?php _e('Mobile Number:'); ?></label>  
        <input type="text" name="mobile" value="<?php echo $data['mobile']; ?>"/>
      </p>
      <p>
        <label for="cus_address"><?php _e('Address:'); ?></label>  
        <input type="text" name="cus_address" value="<?php echo $data['cus_address']; ?>" />
      </p>
      <p>
        <label for="country"><?php _e('Country:'); ?></label>  
        <select name="country">
          <?php foreach($countries as $country):
                $selected = ($data['country'] == $country['code']) ? 'selected="selected"' : '';
          ?>

          <option value="<?php echo $country['code'];?>" <?php echo $selected; ?>><?php echo $country['country'];?></option>
          <?php endforeach;?>
        </select>
      </p>
      <p>
        <label for="state"><?php _e('State:'); ?></label>  
        <input type="text" name="state" />
      </p>
      <p>
        <label for="city"><?php _e('City:'); ?></label>  
        <input type="text" name="city" />
      </p>
      <p>
        <label for="zip_code"><?php _e('Zip Code:'); ?></label>  
        <input type="text" name="zip_code" />
      </p>
    </div><!-- .left-column -->
    <div class="right-column">
      <p>
        <label for="check_in"><?php _e('Check-in:'); ?></label>  
        <input type="text" name="check_in" />
      </p>
      <p>
        <label for="check_out"><?php _e('Check-out:'); ?></label>  
        <input type="text" name="check_out" />
      </p>
      <p>
        <label for="email"><?php _e('Number of Persons:'); ?></label>  
       <select name="no_of_persons">
          <?php for($i = 1; $i <= 10; $i++):?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
          <?php endfor?>
       </select>
      </p>
      <p>
        <label for="with_transfortation"><?php _e('Other Request:'); ?></label>  
        <input type="checkbox" value="1" name="with_transformation" /><?php _e('With Transport'); ?>
      </p>
    </div><!-- .right-column -->
    <div class="vehicle-tour"> 
      <div><h3><?php _e('VEHICLE & TOURS'); ?></h3></div>
      <div class="left-column">
        <h3><?php _e('Travel Dates');?></h3>
        <p>
          <label for="travel_date_from"><?php _e('From:'); ?></label>  
          <input type="text" name="travel_date_from" />
        </p>
        <p>
          <label for="travel_date_to"><?php _e('To:'); ?></label>  
          <input type="text" name="travel_date_to" />
        </p>
         <h3><?php _e('Address');?></h3>
         <p>
          <label for="pick_up_point"><?php _e('Pick-up Point:'); ?></label>  
          <input type="text" name="pick_up_point" />
        </p>
        <p>
          <label for="specific_address"><?php _e('Specific Address:'); ?></label>  
          <input type="text" name="specific_address" />
        </p>
        <p>
          <textarea name="message" cols="50" rows="5" placeholder="Message"></textarea>
        </p>
      </div>
      <div class="right-column">
        <h3><?php _e('Transport Services');?></h3>
        <?php foreach($transports as $vehicle):

            $checked = is_array($data['transport_service']) ? in_array($vehicle['ID'], $data['transport_service']) ? 'checked="checked"' : '' :'';
        ?>
        <p>
          <input type="checkbox" value="<?php echo $vehicle['ID']; ?>" name="transport_service[]" <?php echo $checked; ?> /><span><?php echo $vehicle['transportation'];?></span>
          <select name="trasport_quantity[<?php echo $vehicle['ID']; ?>]">
            <?php for($i = 1; $i <= 10; $i++):
                $selected = is_array($data['transport_service']) ? in_array($i, $data['transport_service']) ? 'selected="selected"' : '' : '';
            ?>

              <option value="<?php echo $i;?>" <?php echo $selected; ?>><?php echo $i;?></option>
            <?php endfor?>
          </select>  
        </p>
      <?php endforeach;?>
      </div>
    </div>
    <div class="payment-method">
        <h3><?php _e('Payment Method');?></h3>
        <p>
          <input type="radio" name="payment_method" checked="checked" value="paypal" />
          <label for="paypal"><?php _e('Paypal'); ?></label>  
        </p>
        <p>
          <input type="radio" name="payment_method" value="credit_card" />
          <label for="paypal"><?php _e('Credit Card'); ?></label>  
          <ul>
            <li><label for="card_type"><?php _e('Credit Card Type:');?></label>
                 <select id="cc_type" name="cc_type">
                    <option value="">-- Select --</option>
                    <option>American Express</option>
                    <option>Visa</option>
                    <option>MasterCard</option>
                    <option>Diners Club</option>
                    <option>JCB</option>  
                </select>
            </li>
            <li><label for="cc_number"><?php _e('Credit Card Number:');?></label><input type="text" name="cc_number" /></li>
            <li><label for="cc_holder_name"><?php _e('Card Holder\' Name: ');?></label><input type="text" name="card_holder_name" /></li>
            <li><label for="cc_expiry_date"><?php _e('Expiry Date: ');?></label>
              <select name="cc_month">
                <?php for($i = 1; $i <= 12; $i++):
                  $m = ($i < 10) ? '0'. $i : $i;
                ?>
                <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                <?php endfor;?>
              </select>
              <select name="cc_year">
                <?php 
                $year = date('Y');
                for($i = $year; $i <= ($year + 12); $i++):
                  $y = ($i < 10) ? '0'. $i : $i;
                ?>
                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor;?>
              </select>

            </li>
            <li><label for="cc_cvv_number"><?php _e('CVV Number:');?></label><input type="text" name="cc_cvv_number" /></li>
          </ul> 
        </p>
        <p>
          <input type="radio" name="payment_method" value="bank_transfer" />
          <label for="paypal"><?php _e('Bank Transfer'); ?></label>  
          <ul>
              <li><input type="radio" name="bdo" value="1" /><label for="paypal"><?php _e('BDO'); ?></label></li>
              <li><input type="radio" name="union_bank" value="1" /><label for="paypal"><?php _e('Union Bank'); ?></label></li>
          </ul>
        </p>
    </div>
    <input type="submit" name="submit" value="Checkout Package" />  
  </form>


<?php
}

add_shortcode('accomodation','init_accomodation');
