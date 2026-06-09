<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

logout();
redirect(appUrl('/public/login.php'));
