<?php

use App\Contact\Action\ContactAction;
use function DI\get;
use function DI\object;

return [
    'contact.to'         => get('mail.to'),
    ContactAction::class => object()->constructorParameter('to', get('contact.to'))
];
