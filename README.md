# Borsdata API class written in php.

I made this php class for quick and easy use of Borsdata API. \
Now, with just four lines of code, I can get the data I want. \
\
This example code gets all the instruments.

> \<?php \
> require_once 'BorsdataAPI.php'; \
> $borsdata = new BorsdataAPI(); \
> $borsdata->set_apikey('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); \
> $data = $borsdata->get_all_instruments("instruments"); \
> ?>

See <example.php> for more info about the code above, \
and take a look in <test.php> for all possible calls more in depth. \
Remember to add your API key to the files before testing.

For more info about the API: \
Borsdata's github wiki. \
https://github.com/Borsdata-Sweden/API/wiki

Borsdata API developer tool. \
https://apidoc.borsdata.se/swagger/index.html

---

Please report any issues or bugs [here](https://github.com/reinew/borsdata-api/issues).
