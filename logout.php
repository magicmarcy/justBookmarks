<?php

include('static/Konst.php');
include('functions.php');

deleteCookieIfExists();

session_start();
session_unset();
session_destroy();

header("Location: " . PROJECT_STARTPAGE);
