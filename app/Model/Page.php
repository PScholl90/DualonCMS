<?php
App::uses('AppModel', 'Model');
/**
 * Page Model
 *
 * @property Container $Container
 * @property MenuEntry $MenuEntry
 */
class Page extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    
    public $validate = array(
        'name' => array(
            'custom' => array(
                'rule' => array('custom', '#^/[a-z0-9\-/]*$#'),
                'message' => 'Not a valid URL. (Starting with / and characters a-z, 0-9 and -)'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'URL already in use.'
            ),            
            'notempty' => array(
				'rule' => array('notempty'),
            	'message' => 'Page name can not be empty.'
            )
        )
    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    public $hasOne = array(
        'Container' => array(
            'className' => 'Container',
            'dependent' => true
        ),
        'MenuEntry' => array(
        	'className' => 'MenuEntry',
            'dependent' => false
        )
    );
}