<?php
/**
 * @version 3.3.5
 * @package Stripe/Templates
 */

?>
<button class="apple-pay-button <?php echo esc_attr( $style ) ?> apple-pay-button-<?php echo esc_attr( $design ) ?>"
        style="<?php echo '-apple-pay-button-style: ' . esc_attr( $button_type ) . '; -apple-pay-button-type:' . esc_attr( apply_filters( 'wc_stripe_applepay_button_type', $type ) ) ?>"></button>