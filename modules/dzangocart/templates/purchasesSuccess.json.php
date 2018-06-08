<?php use_helper('Number'); ?>
<?php $n = count($data['results']) - 1; ?>
{
  "sEcho" : "<?php echo $sf_request->getParameter('sEcho'); ?>",
  "iTotalRecords" : "0",
  "iTotalDisplayRecords" : "<?php echo isset($data['count']) ? $data['count'] : 0; ?>",
  "aaData" : [
    <?php foreach ($data['results'] as $i => $purchase) { 
            $p = new $purchase_classname($purchase); ?> 
      { "DT_RowId" : "<?php echo 'purchase_' . $p->order_id . '_' . $p->id; ?>",
        "DT_RowClass" : "<?php echo $p->getCssClass(); ?>",
        "0" :  "<?php echo $p->getDate()->format($p->getDateFormat()); ?>",
        "1" : "<?php echo $p->order_id; ?>",
        "2" : <?php echo json_encode($p->getCustomerName()); ?>,
        "3" : <?php echo json_encode(link_to($p->name, 'seminar2/edit?id=' . $p->code_generic)); ?>,
        "4" : "<?php echo format_currency($p->amount_excl); ?>",
        "5" : "<?php echo format_currency($p->amount_tax); ?>",
        "6" : "<?php echo format_currency($p->amount_incl); ?>",
        "7" : "<?php echo $p->isPaid() ? addslashes(image_tag('/sfDzangocartPlugin/images/tick.png')) : ''; ?>",
        "8" : "<?php echo $p->isTest() ? addslashes(image_tag('/sfDzangocartPlugin/images/tick.png')) : ''; ?>",
        "9" : "<?php /* include_partial($p->getActionsPartial(), array('purchase' => $p)); */ ?>"
      }
      <?php echo $i == $n ? '' : ','; ?>
    <?php } ?>
  ]
}