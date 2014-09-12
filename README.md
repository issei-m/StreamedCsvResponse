StreamedCsvResponse
===================

[![Build Status](https://travis-ci.org/issei-m/StreamedCsvResponse.svg?branch=master)](https://travis-ci.org/issei-m/StreamedCsvResponse)

Extends the `Symfony\Component\HttpFoundation\StreamedResponse` to send a CSV file to client.

Install
=======

Add require clause of your `composer.json` following line:

    "issei-m/streamed-csv-response": "dev-master"

Usage
=====

in Symfony's controller:

```php
public function exportMembersAction(Request $request)
{
    $rows = [
        '名前', 'メアド', '性別',
        '村澤 逸生', 'issei.m7@gmail.com', '男性',
    ];

    // 2nd parameter is a filename of CSV file which will be downloaded.
    return new StreamedCsvResponse($rows, 'members.csv'); 
}
```

### encoding

if you `setCharset` content will be encoded automatically and relevantly.

```
$response = new StreamedCsvResponse($rows, 'members.csv');
$response->setCharset('Shift-JIS');

return $response; // Every cells are encoded to Shift-JIS.
```
