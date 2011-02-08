<?php
const PDO_DB_TYPE = "pgsql";
const PDO_DB_HOST = "";
const PDO_DB_USER = "";
const PDO_DB_NAME = "project";
const PDO_DB_PASSWORD= "";

const BASE_URL = "http://localhost/posts/source/core/";
const DEBUG = true;
const PAGES = 7; // should be > 2

const REPLIES_PER_PAGE = 2;
const TOPICS_PER_PAGE = 4;

$paths = implode(PATH_SEPARATOR, array(
  '../config',
'../classes',
'../classes/XML'

));

set_include_path($paths);	

?>
