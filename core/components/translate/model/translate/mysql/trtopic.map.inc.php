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

$xpdo_meta_map['trTopic']= array (
  'package' => 'translate',
  'version' => '1.1',
  'table' => 'translate_topic',
  'fields' => 
  array (
    'topic' => NULL,
    'namespace' => NULL,
  ),
  'fieldMeta' => 
  array (
    'topic' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '512',
      'phptype' => 'string',
      'null' => false,
    ),
    'namespace' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'attributes' => 'unsigned',
    ),
  ),
  'composites' => 
  array (
    'Entries' => 
    array (
      'class' => 'trEntry',
      'cardinality' => 'many',
      'foreign' => 'topic',
      'local' => 'id',
      'owner' => 'local',
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
  ),
);
