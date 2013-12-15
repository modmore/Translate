<?php
/**
 * Translate
 *
 * Copyright 2013 by Mark Hamstra <hello@markhamstra.com>
 *
 * This file is part of Translate
 *
 * Translate is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Translate is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Translate; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package translate
*/

$xpdo_meta_map['trEntry']= array (
  'package' => 'translate',
  'version' => '1.1',
  'table' => 'translate_entry',
  'fields' => 
  array (
    'namespace' => NULL,
    'topic' => NULL,
    'language' => NULL,
    'key' => '',
    'translation' => '',
    'flagged' => 1,
    'skipped' => 1,
    'translated' => 1,
    'flaggedby' => NULL,
    'skippedby' => NULL,
    'translatedby' => NULL,
    'flaggedon' => NULL,
    'skippedon' => NULL,
    'translatedon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'namespace' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'attributes' => 'unsigned',
    ),
    'topic' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'attributes' => 'unsigned',
    ),
    'language' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'attributes' => 'unsigned',
    ),
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '250',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'translation' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'flagged' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 1,
    ),
    'skipped' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 1,
    ),
    'translated' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 1,
    ),
    'flaggedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'skippedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'translatedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'flaggedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '15',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'skippedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '15',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'translatedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '15',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
  ),
  'aggregates' => 
  array (
    'Namespace' => 
    array (
      'class' => 'trNamespace',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'namespace',
      'owner' => 'foreign',
    ),
    'Topic' => 
    array (
      'class' => 'trTopic',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'topic',
      'owner' => 'foreign',
    ),
  ),
);
