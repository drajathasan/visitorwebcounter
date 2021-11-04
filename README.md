# visitorwebcounter

copy paste script dibawah ini (Jika belum ada)

```php
\SLiMS\Plugins::getInstance()->execute('after_content_load');
```

pada file index.php dibawah script

```php
// main content grab
$main_content = ob_get_clean();
```

Contoh :

![alt text](./preview-code-1.png "Preview Code")
