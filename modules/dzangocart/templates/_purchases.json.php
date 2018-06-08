<?php use_helper('Number'); ?>
<?php $data = ($data instanceof sfOutputEscaper) ? $data->getRawValue() : $data; ?>
<?php $n = count($data['results']) - 1; ?>
{ "sEcho" : "<?php echo $sf_request->getParameter('sEcho'); ?>",
  "iTotalRecords" : "<?php echo isset($data['totalCount']) ? $data['totalCount'] : 0; ?>",
  "iTotalDisplayRecords" : "<?php echo isset($data['count']) ? $data['count'] : 0; ?>",
  "aaData" : [
    <?php foreach ($data['results'] as $i => $purchase) { 
            $p = new $purchase_classname($purchase); ?> 
      { "DT_RowId" : "<?php echo 'purchase_' . $p->order_id . '_' . $p->id; ?>",
        "DT_RowClass" : "<?php echo $p->getCssClass(); ?>",
        "0" :  <?php echo json_encode(get_partial($p->getBatchActionPartial(), array('object' => $p))); ?>,
        "1" :  "<?php echo $p->getDate()->format($p->getDateFormat()); ?>",
        "2" : "<?php echo $p->order_id; ?>",
        "3" : <?php echo json_encode(get_partial($p->getCustomerPartial(), array('purchase' => $p))); ?>,
        "4" : <?php echo json_encode(get_partial($p->getProductPartial(), array('purchase' => $p))); ?>,
        "5" : "<?php echo format_currency($p->amount_excl); ?>",
        "6" : "<?php echo format_currency($p->amount_tax); ?>",
        "7" : "<?php echo format_currency($p->amount_incl); ?>",
        "8" : "<?php echo $p->isPaid() ? addslashes(image_tag('/sfDzangocartPlugin/images/tick.png')) : ''; ?>",
        "9" : "<?php echo $p->isTest() ? addslashes(image_tag('/sfDzangocartPlugin/images/tick.png')) : ''; ?>",
        "10" : <?php echo json_encode(get_partial($p->getActionsPartial(), array('purchase' => $p))); ?>
      } 
      <?php echo $i == $n ? '' : ','; ?>
    <?php } ?>
  ]
  <?php if (sfConfig::get('sf_debug')) { ?>,
    "params" : <?php echo json_encode($params, true); ?>
  <?php } ?>
}