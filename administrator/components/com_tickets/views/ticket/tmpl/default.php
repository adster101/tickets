<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');

$user = JFactory::getUser();
$userId = $user->get('id');
?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_tickets&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="validate form-vertical">

<div class="row-fluid">

    <div id="j-main-container" class="span12">        

      <?php foreach ($this->form->getFieldSets() as $fieldset) :?>


      <fieldset>
        <legend>
			<?php echo JText::_($fieldset->label); ?>
		</legend>
        <?php foreach ($this->form->getFieldset($fieldset->name) as $field): ?>
			
          <div class="control-group">
            <?php echo $field->label; ?>
            <div class="controls">
              <?php echo $field->input; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </fieldset>  
      <?php if ($fieldset->name == 'notes') : ?>
 
      <div class="notes" style="max-height: 700px;overflow-y: auto">
        <?php krsort($this->item->notes, 1); ?>
        <?php foreach ($this->item->notes as $note): ?>
          <p><strong><?php echo $note['date']; ?> - <?php echo $note['user'] ?></strong></p>
          <p><?php echo $note['description']; ?></p>
          <hr />
        <?php endforeach; ?>
    </div>
      <?php endif; ?>
      <?php endforeach; ?>   

  </div>

  <?php echo JHtml::_('form.token'); ?>
  <input type="hidden" name="task" value="" />
</div>
</form>


<script type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if (task == 'ticket.cancel' || document.formvalidator.isValid(document.id('adminForm')))
    {
      Joomla.submitform(task, document.getElementById('adminForm'));
    }
  }
</script>


