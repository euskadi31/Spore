# Spore

[![Build Status](https://secure.travis-ci.org/euskadi31/Spore.png)](http://travis-ci.org/euskadi31/Spore)

This function is EXPERIMENTAL. The behaviour of this function, its name, 
and surrounding documentation may change without notice in a future release of PHP. 


[SPORE](https://github.com/SPORE/) is a way to describe public services HTTP APIs such as [twitter] [1] 
or applications with an HTTP interface such as [Apache CouchDB] [2] 
using a simple language-agnostic JSON document that can be used by 
various language-specific implementations to dynamically or statically 
generate high level client objects.

The goal of this git repository is to host:

 * the specifications for the [SPORE description language] [3]
 * the specifications for writing a [SPORE client implementation] [4]


[1]: http://github.com/SPORE/api-description/blob/master/services/twitter.json
[2]: http://github.com/SPORE/api-description/blob/master/apps/couchdb.json
[3]: http://github.com/SPORE/specifications/blob/master/spore_description.pod
[4]: http://github.com/SPORE/specifications/blob/master/spore_implementation.pod

## Applications and services API descriptions

 *  [API Descriptions](http://github.com/spore/api-description)


## Install

Use [Composer.phar](http://getcomposer.org/)

    cd Spore/
    curl -s https://getcomposer.org/installer | php
    php composer.phar install

## Usage

The examples are a good place to start. The minimal you'll need to
have is:

``` php
<?php
namespace Application;

require __DIR__ . '/../vendor/autoload.php';

use Spore;

$client = new Spore\Client();
$client->loadSpec(__DIR__ . '/spec/github.json');
$response = $client->call('GET', 'get_user', array('user' => 'euskadi31'));

print_r($response->getContent());

?>
```

Output

    stdClass Object
    (
        [type] => User
        [company] => Audiofanzine
        [public_gists] => 6
        [followers] => 12
        [created_at] => 2010-02-03T10:25:00Z
        [blog] => 
        [following] => 42
        [email] => 
        [public_repos] => 14
        [location] => Toulouse, France
        [html_url] => https://github.com/euskadi31
        [name] => Axel Etcheverry
        [hireable] => 
        [url] => https://api.github.com/users/euskadi31
        [gravatar_id] => 6171ad2ceddde3288b87c546e92f2909
        [avatar_url] => https://secure.gravatar.com/avatar/6171ad2ceddde3288b87c546e92f2909?d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png
        [id] => 195383
        [bio] => PHP & Zend Framework developer
        [login] => euskadi31
    )

### Generated client

``` php
<?php
namespace Application;

require __DIR__ . '/../vendor/autoload.php';

use Acme;

$client = new Acme\Client\Github;
print_r($client->getUser(array(
    'user' => 'euskadi31'
)));

?>
```

Output

    stdClass Object
    (
        [type] => User
        [company] => Audiofanzine
        [public_gists] => 6
        [followers] => 12
        [created_at] => 2010-02-03T10:25:00Z
        [blog] => 
        [following] => 42
        [email] => 
        [public_repos] => 14
        [location] => Toulouse, France
        [html_url] => https://github.com/euskadi31
        [name] => Axel Etcheverry
        [hireable] => 
        [url] => https://api.github.com/users/euskadi31
        [gravatar_id] => 6171ad2ceddde3288b87c546e92f2909
        [avatar_url] => https://secure.gravatar.com/avatar/6171ad2ceddde3288b87c546e92f2909?d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png
        [id] => 195383
        [bio] => PHP & Zend Framework developer
        [login] => euskadi31
    )

## Test with [Atoum](https://github.com/mageekguy/atoum)

The tests can be executed by using this command from the base directory:

    ./vendor/mageekguy/atoum/bin/atoum --glob Tests/Units/


## Report Issues/Bugs

[Bugs](https://github.com/euskadi31/Spore/issues)

