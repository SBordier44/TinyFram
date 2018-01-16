<?php
return [
	'stripe.key'        => getenv('STRIPE_KEY', null),
	'stripe.secret'     => getenv('STRIPE_SECRET', null),
	'database.host'     => getenv('DB_HOST', 'localhost'),
	'database.username' => getenv('DB_USER', null),
	'database.password' => getenv('DB_PASSWORD', null),
	'database.name'     => getenv('DB_NAME', 'my_framework'),
	'mail.to'           => getenv('MAIL_TO', 'admin@dummy.com'),
	'mail.from'         => getenv('MAIL_FROM', 'noreply@dummy.com'),
	'smtp.host'         => getenv('MAIL_SMTP_HOST', 'localhost'),
	'smtp.port'         => getenv('MAIL_SMTP_PORT', 25),
	'smtp.auth_mode'    => getenv('MAIL_AUTH_MODE', null),
	'smtp.username'     => getenv('MAIL_SMTP_USER', null),
	'smtp.password'     => getenv('MAIL_SMTP_PASSWORD', null),
	'app.env'           => getenv('APP_ENV', 'dev')
];
