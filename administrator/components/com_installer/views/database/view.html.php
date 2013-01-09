<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once __DIR__ . '/../default/view.php';

/**
 * Extension Manager Manage View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 * @since       1.6
 */
class InstallerViewDatabase extends InstallerViewDefault
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  void
	 *
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		// Get data from the model
		$this->state = $this->get('State');
		$this->changeSet = $this->get('Items');
		$this->errors = $this->changeSet->check();
		$this->results = $this->changeSet->getStatus();
		$this->schemaVersion = $this->get('SchemaVersion');
		$this->updateVersion = $this->get('UpdateVersion');
		$this->filterParams  = $this->get('DefaultTextFilters');
		$this->schemaVersion = ($this->schemaVersion) ?  $this->schemaVersion : JText::_('JNONE');
		$this->updateVersion = ($this->updateVersion) ?  $this->updateVersion : JText::_('JNONE');
		$this->pagination = $this->get('Pagination');
		$this->errorCount = count($this->errors);

		$errors = count($this->errors);
		if (!(strncmp($this->schemaVersion, JVERSION, 5) === 0))
		{
			$this->errorCount++;
		}
		if (!$this->filterParams)
		{
			$this->errorCount++;
		}
		if (($this->updateVersion != JVERSION))
		{
			$this->errorCount++;
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= InstallerHelper::getActions();
		/*
		 * Set toolbar items for the page
		 */
		JToolbarHelper::custom('database.fix', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_DATABASE_FIX', false, false);
		JToolbarHelper::divider();
		parent::addToolbar();
		JToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_DATABASE');
	}
}
