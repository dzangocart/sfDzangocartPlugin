<div class="refund-form-container">
  <form class="refund-form">
    <?php echo $sf_data->getRaw('form'); ?>
    <fieldset class="buttonHolder">
      <button type="submit" class="button positive primaryAction">
        <?php echo __('Confirm',  null, 'dzangocart') ?>
      </button>
      <button type="button" class="button negative secondaryAction cancelAction">
        <?php echo __('Cancel',  null, 'dzangocart') ?>
      </button>
    </fieldset>
  </form>
</div>
