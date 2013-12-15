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

$xpdo_meta_map['trLanguage']= array (
  'package' => 'translate',
  'version' => '1.1',
  'table' => 'translate_language',
  'fields' => 
  array (
    'name' => NULL,
    'code' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '512',
      'phptype' => 'string',
      'null' => false,
    ),
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '5',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'composites' => 
  array (
    'Entries' => 
    array (
      'class' => 'trEntry',
      'cardinality' => 'many',
      'foreign' => 'language',
      'local' => 'id',
      'owner' => 'local',
    ),
    'Maintainers' => 
    array (
      'class' => 'trMaintainer',
      'cardinality' => 'many',
      'foreign' => 'language',
      'local' => 'id',
      'owner' => 'local',
    ),
  ),
);
