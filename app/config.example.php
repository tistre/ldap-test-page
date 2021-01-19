<?php

// Configuration example

$config = [
    'host' => 'ldaps://example.com:636',
    'baseDn' => 'DC=example,DC=com',
    'searchFilter' => '(&(objectClass=person)(samaccountname=%s))',
    'managerDn' => 'CN=admin,DC=example,DC=com',
    'managerPassword' => 'secret',
    'option_LDAP_OPT_PROTOCOL_VERSION' => 3,
    'option_LDAP_OPT_REFERRALS' => 1
];
