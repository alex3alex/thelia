<?php

namespace Thelia\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'tax_i18n' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Thelia.Model.map
 */
class TaxI18nTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Thelia.Model.map.TaxI18nTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('tax_i18n');
        $this->setPhpName('TaxI18n');
        $this->setClassname('Thelia\\Model\\TaxI18n');
        $this->setPackage('Thelia.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('id', 'Id', 'INTEGER' , 'tax', 'id', true, null, null);
        $this->addPrimaryKey('locale', 'Locale', 'VARCHAR', true, 5, 'en_US');
        $this->addColumn('title', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Tax', 'Thelia\\Model\\Tax', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // TaxI18nTableMap