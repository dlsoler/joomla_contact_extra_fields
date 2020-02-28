<?php

/**
 * @package     ContactFields.Plugin
 * @subpackage  User.dlsprofile
 *
 * @author Diego Luis Soler <solerdiego@gmail.com>
 * @copyright   Copyright (C) 2018 Inteliar.biz, Inc. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

/**
 * User Contact Fields plugin.
 */
class PlgUserDLSContactFields extends JPlugin {

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An array that holds the plugin configuration
     *
     * @since   1.5
     */
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        JFormHelper::addFieldPath(__DIR__ . '/fields');
    }

    /**
     * adds additional fields to the user editing form
     *
     * @param   JForm  $form  The form to be altered.
     * @param   mixed  $data  The associated data for the form.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    public function onContentPrepareForm($form, $data) {
        
        if (!($form instanceof JForm)) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }
        
        $app = JFactory::getApplication();
        
        if(!$app->isSite()) {
            return true;
        }
        
        // Check we are manipulating a valid form.
        $name = $form->getName();

        if (!in_array($name, array('com_contact.contact'))) {
            return true;
        }


        // Add the contact fields to the form.
        JForm::addFormPath(__DIR__ . '/forms');
        $form->loadFile('extrafields', false);
        
        $enableSelect = $this->params->get('contact_select_enable', '0');
        if($enableSelect !== '1') {
            return true;
        }
        
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_contact/models', 'ContactModel');
        $contactModel = JModelLegacy::getInstance('Contact', 'ContactModel', array('ignore_request' => true));
        $params = $app->getParams();
        $contactModel->setState('params', $params);
        
        // Get the contact ids, separated by commas
        $contactIdsStr = $this->params->get('contact_ids', '');
        $contactIds = explode(',', $contactIdsStr);
        
        $contactsArray = array();
        foreach($contactIds as $key => $contactId) {
            $contact = $contactModel->getItem((int)$contactId);
            if(!empty($contact)) {
                $contactsArray[] = $contact;
            }
        }
        // Check if there are contacts to add to the contact selector
        if(count($contactsArray) == 0) {
            return true;
        }

        // Create the contact selector XML Element
        $contactSelect_field = new SimpleXMLElement('<field />');
        $contactSelect_field->addAttribute('type', 'list');
        $contactSelect_field->addAttribute('name', 'contactids');
        $contactSelect_field->addAttribute('label', 'PLG_USER_DLS_CONTACT_FIELDS_FIELD_SELECT_CONTACT_LABEL');
        $contactSelect_field->addAttribute('fieldset', 'dlscontactfields');
        $contactSelect_field->addAttribute('default', '0');
        
        // Add an option for each contact
        foreach($contactsArray as $contact) {
            $option = $contactSelect_field->addChild('option', $contact->name);
            $option->addAttribute('value', (string) $contact->id);
        }

        // Add the field to the form
        $form->setField($contactSelect_field);
        
        return true;
    }
    

    /**
     * 
     * @param stdClass $contact Contact information
     * @param array $data  Data to send
     * @return boolean
     */
    public function onSubmitContact(&$contact, &$data) {
        
        $app = JFactory::getApplication();
        $params = $app->getParams();
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_contact/models', 'ContactModel');
        $contactModel = JModelLegacy::getInstance('Contact', 'ContactModel', array('ignore_request' => true));
        $contactModel->setState('params', $params);
        $selectedContact = $contactModel->getItem((int)$data['contactids']);
        if(!empty($selectedContact)) {
            $contact = $selectedContact;
        }

        // Create the email message to send
        $tmp = $data['contact_message'];
        $data['contact_message'] = Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_MESSAGE_LABEL') . ': ' . $data['contact_subject'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_CONTACT_NAME_LABEL') . ': ' . $data['contact_name'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_LASTNAME_LABEL') . ': ' . $data['lastname'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_EMAIL_LABEL') . ': ' . $data['contact_email'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_PHONE_LABEL') . ': ' . $data['phone'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_CITY_LABEL') . ': ' . $data['city'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_STATE_LABEL'). ': ' . $data['state'] . "\r\n";
        $data['contact_message'] .= Text::_('PLG_USER_DLS_CONTACT_FIELDS_FIELD_MESSAGE_LABEL') . ': ' . $tmp . "\r\n";
        return true;
    }
    
}
