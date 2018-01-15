The `inc/ClientNamespace` path is a place to put namespaced classes that are specific to client functionality. This path is configured in the `composer.json` file located in the theme root.

Usage example:

**inc/ClientNamespace/ClientCustomClass.php**

```
<?php
namespace ClientNamespace;

class ClientCustomClass {
	// properties, methods, etc...
}
```

Usage in any other PHP file after Composer has been included:

```
\ClientNamespace\ClientCustomClass::static_method();

$custom_class = new \ClientNamespace\ClientCustomClass();
$custom_class->method();

/* or with a 'use' directive */

use \ClientNamespace;

ClientCustomClass::static_method();

$custom_class = new ClientCustomClass();
$custom_class->method();
```