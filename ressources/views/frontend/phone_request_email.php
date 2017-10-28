<?php
/**
 * @var $phoneRequest CallMeBack_Model_PhoneRequest
 */
?>
<html>
<title><?php echo __('[CallMeBack] new Request', CallMeBack::TEXT_DOMAIN)?></title>
<body>
<h1><?php echo __('A new callme back request has been submitted !', CallMeBack::TEXT_DOMAIN)?></h1>
<p><?php echo __('Here are the details :', CallMeBack::TEXT_DOMAIN) ?></p>
<p><?php echo __('Name : ', CallMeBack::TEXT_DOMAIN) ?> <?php echo $phoneRequest->getName()?></p>
<p><?php echo __('Phone number : ', CallMeBack::TEXT_DOMAIN) ?> <?php echo $phoneRequest->getPhoneNumber()?></p>
</body>
</html>
