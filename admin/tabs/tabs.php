<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly
?>
<h1 class="wp-heading-inline"><?php echo esc_html__('Self Check-In', 'self-check-in');?></h1>
<div class="nav-tab-wrapper">

    <?php //overview tab
    if(isset($_GET['page']) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) =='self-check-in' && !isset($_GET['tab'])){?>
        <a href="<?php echo esc_url(menu_page_url('self-check-in', false));?>" class="nav-tab nav-tab-active"><?php echo esc_html__( 'Overview', 'self-check-in' );?></a>
    <?php }else{?>
        <a href="<?php echo esc_url(menu_page_url('self-check-in', false));?>" class="nav-tab"><?php echo esc_html__( 'Overview', 'self-check-in' );?></a>
    <?php }?>

    <?php //setting tab
    if(isset($_GET['tab']) && sanitize_text_field( wp_unslash( $_GET['tab'] ) )=='setting'){?>
        <a href="<?php echo esc_url(menu_page_url('self-check-in', false).'&tab=setting');?>" class="nav-tab nav-tab-active"><?php echo esc_html__( 'Settings', 'self-check-in' );?></a>
    <?php }else{?>
        <a href="<?php echo esc_url(menu_page_url('self-check-in', false).'&tab=setting');?>" class="nav-tab"><?php echo esc_html__( 'Settings', 'self-check-in' );?></a>
    <?php }?>

</div>

<div class="wpsci-alerts">
    <?php //saved message
    if (isset($_GET['saved']) && sanitize_text_field( wp_unslash( $_GET['saved'] ) ) == 1): ?>
        <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html__('Data successfully saved.', 'self-check-in'); ?></p>
        </div>
    <?php endif; ?>

    <?php //delete message
    if (isset($_GET['deleted']) && sanitize_text_field( wp_unslash( $_GET['deleted'] ) ) == 1): ?>
        <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html__('Data successfully deleted.', 'self-check-in'); ?></p>
        </div>
    <?php endif; ?>

    <?php //error message
    if (isset($_GET['saved']) && sanitize_text_field( wp_unslash( $_GET['saved'] ) ) == 0): ?>
        <div class="notice notice-error is-dismissible">
        <p><?php echo esc_html__('An error occurred!', 'self-check-in'); ?></p>
        </div>
    <?php endif; ?>
</div>