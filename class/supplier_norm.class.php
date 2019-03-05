<?php

if (!class_exists('SeedObject'))
{
    /**
     * Needed if $form->showLinkedObjectBlock() is call
     */
    define('INC_FROM_DOLIBARR', true);
    require_once dirname(__FILE__) . '/../config.php';
}


class TSupplierNorm extends SeedObject
{
    /**
     * Draft status
     */
    const STATUS_DRAFT = 0;
    /**
     * Validated status
     */
    const STATUS_VALIDATED = 1;
    /**
     * Refused status
     */
    const STATUS_REFUSED = 3;
    /**
     * Accepted status
     */
    const STATUS_ACCEPTED = 4;

    public static $TStatus = array(
        self::STATUS_DRAFT => 'Draft'
        ,self::STATUS_VALIDATED => 'Validate'
        ,self::STATUS_REFUSED => 'Refuse'
        ,self::STATUS_ACCEPTED => 'Accept'
    );

    public $table_element = 'supplier_norm';

    public $element = 'supplier_norm';

    public function __construct($db)
    {
        global $conf,$langs;

        $this->db = $db;

        $this->fields=array(
            'fk_supplier'=>array('type'=>'integer','index'=>true),
            'serial_number_start'=>array('type'=>'integer'), // date, integer, string, float, array, text
            'serial_number_end'=>array('type'=>'integer'),
            'lot_number_start'=>array('type'=>'integer'),
            'lot_number_end'=>array('type'=>'integer'),
            'eatbydate_start'=>array('type'=>'integer'),
            'eatbydate_end'=>array('type'=>'integer'),
            'sellbydate_start'=>array('type'=>'integer'),
            'sellbydate_end'=>array('type'=>'integer'),
        );

        $this->init();

    }

    public function save()
    {
        global $user;

        if (!$this->id) $this->fk_user_author = $user->id;

        $res = $this->id>0 ? $this->updateCommon($user) : $this->createCommon($user);



        return $res;
    }


    public function loadBy($value, $field, $annexe = false)
    {
        $res = parent::fetchBy(  $value,$field, $annexe);

        return $res;
    }

    public function load($id, $ref, $loadChild = true)
    {
        global $db;

        $res = parent::fetchCommon($id, $ref);

        if ($loadChild) $this->fetchObjectLinked();

        return $res;
    }

    public function delete(User &$user)
    {

        $this->generic->deleteObjectLinked();

        parent::deleteCommon($user);
    }



}