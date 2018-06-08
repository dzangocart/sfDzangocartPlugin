<?php echo json_encode(array('error' => ($error instanceof sfOutputEscaper) ? $error->getRawValue() : $error)); ?>
