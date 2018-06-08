<?php use_helper('Number'); ?>
<?php $data = ($data instanceof sfOutputEscaper) ? $data->getRawValue() : $data; ?>
<?php $n = count($data['list']) - 1; ?>
{ "sEcho" : "<?php echo $sf_request->getParameter('sEcho'); ?>",
  "iTotalRecords" : "<?php echo isset($data['total']) ? $data['total'] : 0; ?>",
  "iTotalDisplayRecords" : "<?php echo isset($data['totalDisplay']) ? $data['totalDisplay'] : 0; ?>",
  "aaData" : [
    <?php if ($n >= 0) {
            foreach ($data['list'] as $i => $transaction) {
              $t = new $transaction_classname($transaction); ?>
      { "DT_RowId" : "<?php echo 'transaction_' . $t->id; ?>",
        "DT_RowClass" : "<?php echo $t->getCssClass(); ?>",
        "0" :  <?php echo json_encode(get_partial($t->getBatchActionPartial(), array('object' => $t))); ?>,
        "1" : "<?php echo $t->getDate()->format($t->getDateFormat()); ?>",
        "2" : "<?php echo $t->order_id; ?>",
        "3" : "<?php echo format_currency($t->getAmount()); ?>",
        "4" : "<?php echo $t->currency_code; ?>",
        "5" : "<?php echo $t->payment_means; ?>",
        "6" : "<?php echo $t->card_number; ?>",
        "7" : "<?php echo $t->transaction_id; ?>",
        "8" : "<?php echo $t->response_code; ?>",
        "9" : "<?php echo $t->bank_response_code; ?>",
        "10" : "<?php echo $t->cvv_response_code; ?>",
        "11" : "<?php echo $t->isTest() ? addslashes(image_tag('/sfDzangocartPlugin/images/tick.png')) : ''; ?>",
        "12" :  <?php echo json_encode(get_partial($t->getActionsPartial(), array('t' => $t))); ?>
      } 
      <?php echo $i == $n ? '' : ','; ?>
      <?php } ?>
    <?php } ?>
  ]
  <?php if (sfConfig::get('sf_debug')) { ?>,
    "params" : <?php echo json_encode($params, true); ?>
  <?php } ?>
}