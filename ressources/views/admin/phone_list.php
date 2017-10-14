<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 15/10/2017
 * Time: 22:15
 */

/** @var CallMeBack_Block_Admin_PhoneRequestList $listBlock */
?>
<style>
    .phonerequests .is-not-done td,
    .phonerequests .is-not-done strong {
        font-weight: bold;
    }
    .phonerequests .is-not-done a {
        font-weight: normal;
    }
</style>
<div class="wrap">
    <h2><?php _e('Phone Requests List', CallMeBack::TEXT_DOMAIN) ?></h2>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form class="phonerequests" method="post">
                        <?php
                        $listBlock->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
