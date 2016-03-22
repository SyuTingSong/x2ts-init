<?php

use x2ts\http\Request;
use x2ts\http\Response;

require_once('xts.php');

session_start();
X::router()->route(new Request(), new Response());
