<div class="cancel">
  <h2><?php echo __('Cancel order', null, 'dzangocart'); ?></h2>
  <div class="warning">
    <p>
      <?php echo __('Are you sure you want to do this? This operation cannot be undone.'); ?>
    </p>
  </div>
  <form class="uniForm" action="<?php echo url_for('dzangocart/cancel'); ?>">
    <?php echo $form->render(); ?>
    <fieldset class="buttonHolder">
      <input type="submit" class="primaryAction" value="<?php echo __('Confirm',  null, 'dzangocart') ?>" />
      <a href="#" class="secondaryAction cancel">
        <?php echo __('Cancel',  null, 'dzangocart') ?>
      </a>
    </fieldset>
  </form>
</div>
