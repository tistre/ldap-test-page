<?php

// By Tim Strehle <tim@strehle.de>
// https://github.com/tistre/ldap-test-page

function xmlChars($str)
{
    // See http://www.w3.org/TR/2000/REC-xml-20001006#charsets

    $replace_pairs = [];

    for ($i = 0; $i < 32; ++$i) {
        if (($i != 9) && ($i != 10) && ($i != 13)) {
            $replace_pairs[chr($i)] = ' ';
        }
    }

    return strtr($str, $replace_pairs);
}

session_start(['name' => 'LdapTest']);

$defaults = [
    'host' => '',
    'baseDn' => '',
    'searchFilter' => '',
    'searchValue' => '',
    'returnAttributes' => '',
    'managerDn' => '',
    'managerPassword' => '',
    'option_LDAP_OPT_PROTOCOL_VERSION' => 3,
    'option_LDAP_OPT_REFERRALS' => 0,
    'option_LDAP_OPT_SIZELIMIT' => 1000
];

$configFile = dirname(__DIR__) . '/config.php';

if (file_exists($configFile)) {
    $config = [];
    include $configFile;
    $defaults = array_merge($defaults, $config);
}

if (!isset($_SESSION['ldapConfig'])) {
    $_SESSION['ldapConfig'] = [];
}

foreach ($defaults as $key => $value) {
    if (isset($_POST[$key])) {
        $_SESSION['ldapConfig'][$key] = trim($_POST[$key]);
    } elseif (!isset($_SESSION['ldapConfig'][$key])) {
        $_SESSION['ldapConfig'][$key] = $value;
    }
}

header('Content-Type: application/xhtml+xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8"/>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>LDAP test page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"/>

</head>
<body>

<div class="container">

    <h1>LDAP test page</h1>

    <form method="post" action="">

        <div class="form-group">
            <label for="host">LDAP server URL:</label>
            <input name="host" type="text" required="required" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['ldapConfig']['host']) ?>"/>
            <small class="form-text text-muted">
                Example: <em>ldaps://dc1.example.com:3269</em>
            </small>
        </div>
        <div class="form-group">
            <label for="baseDn">Base DN:</label>
            <input name="baseDn" type="text" required="required" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['ldapConfig']['baseDn']) ?>"/>
            <small class="form-text text-muted">
                Example: <em>DC=example,DC=com</em>
            </small>
        </div>
        <div class="form-row">
            <div class="form-group col-md-8">
                <label for="managerDn">Manager DN:</label>
                <input name="managerDn" type="text" required="required" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['managerDn']) ?>"/>
                <small class="form-text text-muted">
                    The user to connect as. Example: <em>CN=admin,DC=example,DC=com</em>
                </small>
            </div>
            <div class="form-group col-md-4">
                <label for="managerPassword">Manager password:</label>
                <input name="managerPassword" type="password" required="required" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['managerPassword']) ?>"/>
                <small class="form-text text-muted">
                    The password.
                </small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="option_LDAP_OPT_PROTOCOL_VERSION">Protocol version:</label>
                <input name="option_LDAP_OPT_PROTOCOL_VERSION" type="text" required="required" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['option_LDAP_OPT_PROTOCOL_VERSION']) ?>"/>
                <small class="form-text text-muted">
                    LDAP protocol version. Use <em>3</em> for Active Directory (AD).
                </small>
            </div>
            <div class="form-group col-md-4">
                <label for="option_LDAP_OPT_REFERRALS">Referrals:</label>
                <input name="option_LDAP_OPT_REFERRALS" type="text" required="required" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['option_LDAP_OPT_REFERRALS']) ?>"/>
                <small class="form-text text-muted">
                    Use <em>1</em> for AD.
                </small>
            </div>
            <div class="form-group col-md-4">
                <label for="option_LDAP_OPT_SIZELIMIT">Size limit:</label>
                <input name="option_LDAP_OPT_SIZELIMIT" type="text" required="required"
                       class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['option_LDAP_OPT_SIZELIMIT']) ?>"/>
                <small class="form-text text-muted">
                    Use <em>1000</em> (or greater?) for AD.
                </small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-8">
                <label for="searchFilter">Search pattern:</label>
                <input name="searchFilter" type="text" required="required" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['searchFilter']) ?>"/>
                <small class="form-text text-muted">
                    LDAP search. "%s" will be replaced with the search value. Example: <em>(&amp;(objectClass=person)(mail=%s))</em>
                </small>
            </div>
            <div class="form-group col-md-4">
                <label for="searchValue">Search value:</label>
                <input name="searchValue" type="text" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['ldapConfig']['searchValue']) ?>"/>
                <small class="form-text text-muted">
                    Replaces "%s" in the pattern. Example: <em>me@example.com</em>
                </small>
            </div>
        </div>
        <div class="form-group">
            <label for="returnAttributes">Attributes to return:</label>
            <input name="returnAttributes" type="text" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['ldapConfig']['returnAttributes']) ?>"/>
            <small class="form-text text-muted">
                Comma-separated list. Leave blank for all attributes. Example: <em>mail,cn,dn</em>
            </small>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Run LDAP test</button>
        </div>

    </form>

    <pre><code>&lt;?php
            <?php

            // Display errors to ease debugging of LDAP connection problems

            echo "\nini_set('display_errors', 'stderr');\n";
            echo "ini_set('error_reporting', E_ALL);\n";

            ini_set('display_errors', 1);
            ini_set('error_reporting', E_ALL);
            ini_set('html_errors', 1);

            try {
                $required = ['host', 'baseDn', 'searchFilter', 'managerDn', 'managerPassword'];

                foreach ($required as $key) {
                    if (strlen($_SESSION['ldapConfig'][$key]) === 0) {
                        throw new \Exception('Not all required fields are filled in.');
                    }
                }

                // Create LDAP "link identifier" - the actual connection gets established later, by ldap_bind()

                echo "\n" . htmlspecialchars(sprintf("\$conn = ldap_connect('%s');",
                        $_SESSION['ldapConfig']['host'])) . "\n";

                $ldapConnection = ldap_connect($_SESSION['ldapConfig']['host']);

                if (!$ldapConnection) {
                    throw new \Exception('ldap_connect() failed.');
                }

                // Set LDAP options

                foreach ($_SESSION['ldapConfig'] as $key => $value) {
                    if (substr($key, 0, 7) !== 'option_') {
                        continue;
                    }

                    if (strlen($value) === 0) {
                        continue;
                    }

                    $optionName = substr($key, 7);
                    $option = constant($optionName);

                    echo "\n" . htmlspecialchars(sprintf("ldap_set_option(\$conn, %s, %s);", $optionName,
                            $value)) . "\n";

                    $setOptionOk = ldap_set_option($ldapConnection, $option, $value);

                    if (!$setOptionOk) {
                        throw new \Exception("ldap_set_option() failed.");
                    }
                }

                // Connect and "bind" (authenticate)

                echo "\n" . htmlspecialchars(sprintf("ldap_bind(\$conn, '%s', '***');",
                        $_SESSION['ldapConfig']['managerDn'])) . "\n";

                $bindOk = ldap_bind($ldapConnection, $_SESSION['ldapConfig']['managerDn'],
                    $_SESSION['ldapConfig']['managerPassword']);

                if (!$bindOk) {
                    throw new \Exception("ldap_bind() failed.");
                }

                // Search

                $ldapSearch = sprintf(
                    $_SESSION['ldapConfig']['searchFilter'],
                    ldap_escape($_SESSION['ldapConfig']['searchValue'], null, LDAP_ESCAPE_FILTER)
                );

                $returnAttributes = [];

                if (strlen($_SESSION['ldapConfig']['returnAttributes']) > 0) {
                    $returnAttributes = array_map('trim', explode(',', $_SESSION['ldapConfig']['returnAttributes']));
                }

                echo "\n" . htmlspecialchars(sprintf("\$res = ldap_search(\$conn, '%s', '%s', %s);",
                        $_SESSION['ldapConfig']['baseDn'],
                        $ldapSearch,
                        var_export($returnAttributes, true))) . "\n";

                $searchResult = ldap_search(
                    $ldapConnection,
                    $_SESSION['ldapConfig']['baseDn'],
                    $ldapSearch,
                    $returnAttributes
                );

                if (!$searchResult) {
                    ldap_unbind($ldapConnection);
                    throw new \Exception("ldap_search() failed.");
                }

                echo "\n\$entries = ldap_get_entries(\$conn, \$res);\n";

                $entries = ldap_get_entries($ldapConnection, $searchResult);

                echo "\nprint_r(\$entries);\n";

                echo "\n/* Output:\n" . htmlspecialchars(xmlChars(print_r($entries, true)), ENT_SUBSTITUTE) . "*/\n";

                // Disconnect

                echo "\nldap_unbind(\$conn);\n";

                ldap_unbind($ldapConnection);

                // Show ldapsearch command

                echo "\n/* Equivalent ldapsearch command line:\n\n";

                echo htmlspecialchars(sprintf(
                    "ldapsearch \\\n-H %s \\\n-D %s \\\n-W \\\n-b %s \\\n%s %s\n",
                    escapeshellarg($_SESSION['ldapConfig']['host']),
                    escapeshellarg($_SESSION['ldapConfig']['managerDn']),
                    escapeshellarg($_SESSION['ldapConfig']['baseDn']),
                    escapeshellarg($ldapSearch),
                    implode(' ', array_map('escapeshellarg', $returnAttributes))
                ));

                echo "\n*/\n";

            } catch (Exception $e) {
                echo "\nERROR: " . htmlspecialchars($e->getMessage()) . "\n";
            }

            ?></code></pre>
</div>
</body>
</html>
