<?php use_helper('Number'); ?>
<?php $data = ($data instanceof sfOutputEscaper) ? $data->getRawValue() : $data; ?>
<?php $details = ($details instanceof sfOutputEscaper) ? $details->getRawValue() : $details; ?>
<?php $n = count($data['list']) - 1; ?>
{ "sEcho" : "<?php echo $sf_request->getParameter('sEcho'); ?>",
  "iTotalRecords" : "<?php echo isset($data['total']) ? $data['total'] : 0; ?>",
  "iTotalDisplayRecords" : "<?php echo isset($data['totalDisplay']) ? $data['totalDisplay'] : 0; ?>",
  "aaData" : [
    <?php if ($data['list']) {
            foreach ($data['list'] as $i => $order) {
              $o = new $order_classname($order); ?>
      { "DT_RowId" : "<?php echo 'order_' . $o->id; ?>",
        "DT_RowClass" : "<?php echo $o->getCssClass() . (in_array($o->id, $details) ? ' with-details' : ''); ?>",
        "0" :  <?php echo json_encode(get_partial($o->getBatchActionPartial(), array('object' => $o))); ?>,
        "1" : "<?php echo $o->getDate()->format($o->getDateFormat()); ?>",
        "2" : "<?php echo $o->id; ?>",
        "3" : <?php echo json_encode($o->getCustomerName()); ?>,
        "4" : "<?php echo $o->currency; ?>",
        "5" : "<?php echo format_currency($o->amount_excl_tax); ?>",
        "6" : "<?php echo format_currency($o->amount_tax); ?>",
        "7" : "<?php echo format_currency($o->amount_incl_tax); ?>",
        "8" : "<?php echo format_currency($o->amount_paid); ?>",
        "9" : "<?php echo $o->affiliate_id; ?>",
        "10" : "<?php echo $o->isTest() ? addslashes(image_tag('/sfDzangocartPlugin/images/tick.png')) : ''; ?>",
        "11" : <?php echo json_encode(get_partial($o->getActionsPartial(), array('order' => $o))); ?>,
        "details" : <?php echo json_encode(get_partial($o->getDetailsPartial(), array('items' => $o->items))); ?>
      }
      <?php echo $i == $n ? '' : ','; ?>
      <?php } ?>
    <?php } ?>
  ]
  <?php if (sfConfig::get('sf_debug')) { ?>,
    "params" : <?php echo json_encode($params, true); ?>
  <?php } ?>
}