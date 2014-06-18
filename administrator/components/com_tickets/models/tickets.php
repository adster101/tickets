<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of tickets
 */
class TicketsModelTickets extends JModelList {

  /**
   * Constructor.
   *
   * @param    array    An optional associative array of configuration settings.
   * @see        JController
   * @since    1.6
   */
  public function __construct($config = array()) {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
          'id', 'a.id',
          'created_by', 'a.created_by',
          'date_created', 'a.date_created',
          'date_updated', 'a.date_updated',
          'state', 'a.state',
          'severity', 'a.severity',
          '', 'a.severity',
      );
    }

    parent::__construct($config);
  }

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   */
  protected function populateState($ordering = null, $direction = null) {
    // Initialise variables.
    $app = JFactory::getApplication('administrator');

    // Load the filter state.
    $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
    $this->setState('filter.state', $published);

    $area= $app->getUserStateFromRequest($this->context . '.filter.area', 'filter_area', '', 'string');
    $this->setState('filter.area', $area);
   
    $severity = $app->getUserStateFromRequest($this->context . '.filter.severity', 'filter_severity', '', 'string');
    $this->setState('filter.severity', $severity);
    
    // Load the parameters.
    $params = JComponentHelper::getParams('com_invoices');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('a.id', 'asc');
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param	string		$id	A prefix for the store id.
   * @return	string		A store id.
   * @since	1.6
   */
  protected function getStoreId($id = '') {
    // Compile the store id.
    $id.= ':' . $this->getState('filter.search');
    $id.= ':' . $this->getState('filter.state');

    return parent::getStoreId($id);
  }

  /**
   * Build an SQL query to load the list data.
   *
   * @return	JDatabaseQuery
   * @since	1.6
   */
  protected function getListQuery() {
    // Create a new query object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Select the required fields from the table.
    $query->select(
            $this->getState(
                    'list.select', 'a.id,
                    date_format(a.date_created, "%D %M %Y") as date_created,
                    date_format(a.date_updated, "%D %M %Y") as date_updated,
                    a.state,
                    c.title as severity,
                    a.title,
                    a.description,
                    d.title as area,
                    a.checked_out,
                    e.name as editor,
                    a.checked_out_time,
                    b.name')
    );
    $query->from('`#__tickets` AS a');

    $query->leftJoin('#__users b on a.assigned_to = b.id');
    $query->leftJoin('#__users e on a.checked_out = e.id');
    $query->leftJoin('#__tickets_severity c on a.severity = c.id');
    $query->leftJoin('#__categories d on a.area = d.id');

    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
      $query->where('a.state = ' . (int) $published);
    } else {
      $query->where('a.state in (1,2,3)');
    }
    
    // Filter by project area
    $area = $this->getState('filter.area');
    if (is_numeric($area)) {
      $query->where('a.area = ' . (int) $area);
    } 
    
    // Filter by severity 
    $severity = $this->getState('filter.severity');
    if (is_numeric($severity)) {
      $query->where('a.severity = ' . (int) $severity);
    } 
    
    
    // Filter by search on property number
    $search = $this->getState('filter.search');
    if (!empty($search)) {

      $search_string = $db->Quote('%' . $db->escape($search, true) . '%');
      $query->where('(a.title like ' . $search_string . ' OR a.description LIKE ' . $search_string . ' OR a.id = ' . (int) $search . ')');
    }



    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn = $this->state->get('list.direction');
    if ($orderCol && $orderDirn) {
      $query->order($db->escape($orderCol . ' ' . $orderDirn));
    }



    return $query;
  }

}
