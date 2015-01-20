<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instanciated directly, as they
 * are automatically instanciated by the JObserverMapper
 *
 * @package     Joomla.Libraries
 * @subpackage  Table
 * @link        http://docs.joomla.org/JTableObserver
 * @since       3.1.2
 */
class JTableObserverTicket extends JTableObserver
{
	/**
	 * Helper object for managing tags
	 *
	 * @var    JHelperTags
	 * @since  3.1.2
	 */
	protected $tagsHelper;

	/**
	 * The pattern for this table's TypeAlias
	 *
	 * @var    string
	 * @since  3.1.2
	 */
	protected $typeAliasPattern = null;

	/**
	 * Not public, so marking private and deprecated, but needed internally in parseTypeAlias for
	 * PHP < 5.4.0 as it's not passing context $this to closure function.
	 *
	 * @var         JTableObserverTags
	 * @since       3.1.2
	 * @deprecated  Never use this
	 * @private
	 */
	public static $_myTableForPregreplaceOnly;

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 * Creates the associated tags helper class instance
	 * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JObservableInterface  $observableObject  The subject object to be observed
	 * @param   array                 $params            ( 'typeAlias' => $typeAlias )
	 *
	 * @return  JTableObserverTags
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{

		include_once(JPATH_ADMINISTRATOR . '/components/com_tickets/helpers/ticket.php');
		
		$observer = new self($observableObject);
		$observer->ticketHelper = new JHelperTicket;

		return $observer;
	}

	/**
	 * Pre-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  $updateNulls  The result of the load
	 * @param   string   $tableKey     The key of the table
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onBeforeStore($updateNulls, $tableKey)
	{
		$this->ticketHelper->preStoreProcess($this->table, (array) $this->table->ticketHelper->tags);
	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 * You can change optional params newTags and replaceTags of ticketHelper with method setNewTagsToAdd
	 *
	 * @param   boolean  &$result  The result of the load
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onAfterStore(&$result)
	{
		if ($result)
		{
			// Here we want to get the notes and save them in the notes table
			
		}
	}

	/**
	 * Pre-processor for $table->delete($pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 * @throws  UnexpectedValueException
	 */
	public function onBeforeDelete($pk)
	{
		$this->parseTypeAlias();
		$this->ticketHelper->deleteTagData($this->table, $pk);
	}



}
