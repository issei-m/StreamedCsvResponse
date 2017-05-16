StreamedCsvResponse
===================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/bf485e84-b260-4e4e-a752-e9f8fea1a8bb/small.png)](https://insight.sensiolabs.com/projects/bf485e84-b260-4e4e-a752-e9f8fea1a8bb)

[![Build Status](https://travis-ci.org/issei-m/StreamedCsvResponse.svg?branch=master)](https://travis-ci.org/issei-m/StreamedCsvResponse)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/issei-m/StreamedCsvResponse/?branch=master)
[![License](https://poser.pugx.org/issei-m/streamed-csv-response/license.svg)](https://packagist.org/packages/issei-m/streamed-csv-response)

Extending the `Symfony\Component\HttpFoundation\StreamedResponse` to send a CSV file to client.
It works with Symfony 2.3 and newer (including 3) on PHP 5.x (5.3.3 and newer) / 7.x / hhvm.

Usage
-----

Very easy, just pass **two** arguments to the constructor. For instance in Symfony's controller:

```php
public function exportCustomersAction(Request $request)
{
    return new StreamedCsvResponse(
        // 1st parameter: any iterable CSV rows
        (function () {
            yield ['Full Name', 'Email', 'Gender'];

            foreach ($this->get('user_repository')->getAllUsers() as $user) {
                yield [
                    $user->getFullName(),
                    $user->getEmail(),
                    $user->getGender(),
                ];
            }

            // Of course, you can also use any iterable for cell representation
            yield (function () {
                yield '村澤 逸生';
                yield 'issei.m7@gmail.com';
                yield '男性';
            })();
        })(),

        // 2nd parameter: the filename the browser uses in downloading 
        'customers.csv'
    ); 
}
```

### auto encoding

If the response has been set any `charset`, every cell content will be encoded accordingly when sending:

```php
$response = new StreamedCsvResponse($rows, 'customers.csv');
$response->setCharset('SJIS-win');

$response->send(); // Every cells are automatically encoded to SJIS-win.
```

Installation
------------

Use [Composer] to install the package:

    $ composer require issei-m/streamed-csv-response

[Composer]: https://getcomposer.org
