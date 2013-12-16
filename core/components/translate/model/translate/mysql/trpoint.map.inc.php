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

$xpdo_meta_map['trPoint']= array (
  'package' => 'translate',
  'version' => '1.1',
  'table' => 'translate_point',
  'fields' => 
  array (
    'user' => NULL,
    'entry' => NULL,
    'languageset' => NULL,
    'points' => NULL,
    'awardedon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'entry' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'languageset' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'points' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
    'awardedon' => 
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
    'User' => 
    array (
      'class' => 'modUser',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'user',
      'owner' => 'foreign',
    ),
    'Entry' => 
    array (
      'class' => 'trEntry',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'entry',
      'owner' => 'foreign',
    ),
    'LanguageSet' => 
    array (
      'class' => 'trLanguageSet',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'languageset',
      'owner' => 'foreign',
    ),
  ),
);
