# Borsdata API class written in php.

I made this php class for quick and easy use of Borsdata API. \
Now, with just four lines of code, I can get the data I want. \
\
This example code will get all the instruments.

> \<?php \
> require_once 'apiClass.php'; \
> $borsdata = new Borsdata(); \
> $borsdata->set_apikey('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); \
> $data = $borsdata->get_all_instruments("instruments"); \
> ?>

See <example.php> for more info about the code above. \
And take a look in <test.php> for all possible calls more in depth.

For more info about the API: \
Borsdata's github wiki. \
https://github.com/Borsdata-Sweden/API/wiki

Borsdata API developer tool. \
https://apidoc.borsdata.se/swagger/index.html

---

Please report any issues, bugs or ideas [here]().
