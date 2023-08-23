# Borsdata API class written in php.

I made this php class for a quick and easy use of Borsdata API.

#### Features:
- Easy to use
- All calls are made with a single function
- All functions are documented in the class file
- All data is returned as JSON

#### Requirements:
- API key from borsdata.se
- php 7.4 or higher

#### Setup:
Create .env file in the same folder as the class file, add your API key on a single line.
> API_KEY="\<your api key goes here\>"

#### Usage:
With just three lines of code, you can get the data you want. \
This example code gets all the instruments.
> \<?php \
> require_once 'BorsdataAPI.php'; \
> $borsdata = new BorsdataAPI(); \
> $data = $borsdata->getAllInstruments('instruments');

See <example.php> for more info about the code above. \
Take a look in <test.php> for examples of all possible calls.

---

For more info about the API: \
Borsdata's github wiki. \
https://github.com/Borsdata-Sweden/API/wiki

Borsdata API developer tool. \
https://apidoc.borsdata.se/swagger/index.html

---

Please report any issues or bugs [here](https://github.com/reinew/borsdata-api/issues).
