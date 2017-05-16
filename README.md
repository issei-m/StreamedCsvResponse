StreamedCsvResponse
===================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/bf485e84-b260-4e4e-a752-e9f8fea1a8bb/small.png)](https://insight.sensiolabs.com/projects/bf485e84-b260-4e4e-a752-e9f8fea1a8bb)

[![Build Status](https://travis-ci.org/issei-m/StreamedCsvResponse.svg?branch=master)](https://travis-ci.org/issei-m/StreamedCsvResponse)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/?branch=master)
[![License](https://poser.pugx.org/issei-m/streamed-csv-response/license.svg)](https://packagist.org/packages/issei-m/streamed-csv-response)

Extends the `Symfony\Component\HttpFoundation\StreamedResponse` to send a CSV file to client.

It works with Symfony 2.3 and newer (including 3) on PHP 5.x (5.3.3 and newer)/7.x/hhvm.

Usage
-----

in Symfony's controller:

```php
public function exportMembersAction(Request $request)
{
    $rows = [
        ['名前', 'メアド', '性別'],
        ['村澤 逸生', 'issei.m7@gmail.com', '男性'],
    ];

    // 2nd parameter is a filename of CSV file which will be downloaded.
    return new StreamedCsvResponse($rows, 'members.csv'); 
}
```

with Generator:

```php
$rows = function (UserRepository $userRepository) {
    yield ['名前', 'メアド', '性別'];

    foreach ($userRepository->findAll() as $user) {
        yield [
            $user->getName(),
            $user->getEmail(),
            $user->getGender(),
        ];
    }
};

return new StreamedCsvResponse($rows($this->getDoctrine()->getRepository('Example\User')), 'members.csv');
```

### encoding

if you `setCharset` content will be encoded automatically and relevantly.

```php
$response = new StreamedCsvResponse($rows, 'members.csv');
$response->setCharset('Shift-JIS');

return $response; // Every cells are encoded to Shift-JIS.
```

Installation
------------

Use [Composer] to install the package:

    $ composer require issei-m/streamed-csv-response

[Composer]: https://getcomposer.org
