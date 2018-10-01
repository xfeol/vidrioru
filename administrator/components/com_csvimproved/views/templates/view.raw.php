<?php
/**
 * Templates view
 *
 * @package CSVImproved
 * @author Roland Dalmulder
 * @link http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: view.raw.php 945 2009-07-30 07:18:43Z Roland $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.view' );

/**
 * Templates View
 *
 * @package CSVImproved
 */
class CsvimprovedViewTemplates extends JView {
	/**
	 * Templates view display method
	 * @return void
	 **/
	function display($tpl = null) {
		
		$task = JRequest::getVar('task');
		
		switch ($task) {
			case 'getimexsettings':
				/* Get all template settings */
				$this->TemplateConfigurator();
				break;
			case 'listfields':
				$model = $this->getModel('templates');
				$model->setTemplateId(JRequest::getInt('template_id'));
				$templatename = $model->getTemplateName();
				$this->assignRef('templatename', $templatename);
				$fields = $model->GetFields(JRequest::getInt('template_id'), false, 0, 0);
				$this->assignRef('fields', $fields);
				break;
		}
		
		/* Display it all */
		parent::display($tpl);
	}
	
	/**
	 * Template configurator
	 */
	private function TemplateConfigurator() {
		/* Get the task */
		$task = JRequest::getCmd('task');
		
		/* Get the template ID */
		$template_id = JRequest::getInt('template_id', 0);
		
		/* Load template model */
		$template_model = $this->getModel('templates');
		
		/* Set the template ID */
		if ($template_id > 0) $template_model->setTemplateId($template_id);
		
		if ($task != 'newtemplate') {
			/* Get the template details */
			$template = $this->get('Template');
		}
		else if ($task == 'newtemplate') {
			/* For new templates load an empty template */
			$template = $this->get('EmptyTemplate');
		}
		
		/* Get the template type */
		$type = JRequest::getCmd('type');
		
		/* Get the supported fields */
		$supportedfields = $template_model->getTemplateTypes($type);
		$lists['supportedfields'] = JHTML::_('select.genericlist', $supportedfields, 'template_type', '' , 'name', 'value', $template->template_type);
		
		/* Get the thumbnail formats */
		$thumb_format = array();
		$thumb_format[0]->name = 'none';
		$thumb_format[0]->value = JText::_('Default');
		$thumb_format[1]->name = 'jpg';
		$thumb_format[1]->value = JText::_('JPG');
		$thumb_format[2]->name = 'png';
		$thumb_format[2]->value = JText::_('PNG');
		$thumb_format[3]->name = 'gif';
		$thumb_format[3]->value = JText::_('GIF');
		$lists['thumbnailformat'] = JHTML::_('select.genericlist', $thumb_format, 'thumb_extension', '' , 'name', 'value', $template->thumb_extension);
		
		/* Get the shopper groups */
		$shopper_groups = $this->get('shoppergroups');
		
		/* Get the shopper groups */
		$manufacturers = $this->get('manufacturers');
		
		/* Assign the data so it can be displayed */
		$this->assignRef('task', $task);
		$this->assignRef('template', $template);
		$this->assignRef('lists', $lists);
		$this->assignRef('type', $type);
		$this->assignRef('supportedfields', $supportedfields);
		$this->assignRef('shopper_groups', $shopper_groups);
		$this->assignRef('manufacturers', $manufacturers);
	}
}
?>
