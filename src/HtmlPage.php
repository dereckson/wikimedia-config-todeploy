<?php

namespace Wikimedia\Deployments\ToDeploy;

use JonnyW\PhantomJs\Client;
use Wikimedia\Deployments\ToDeploy\Utils\OSUtils;

class HtmlPage {

    /**
     * Gets a PhantomJS client instance
     *
     * @return JonnyW\PhantomJs\Client
     */
    static private function getPhantomJsClientInstance () {
        $client = Client::getInstance();
        $command = OSUtils::getCommandRealPath("phantomjs");
        $client->getEngine()->setPath($command);
        return $client;
    }

    /**
     * Gets the HTML content of the page, as rendered by Webkit
     *
     * @param string $URL the URL to fetch
     * @return string
     */
    static public function getRenderedContents ($url) {
        $client = self::getPhantomJsClientInstance();

        $request  = $client->getMessageFactory()->createRequest($url, 'GET');
        $response = $client->getMessageFactory()->createResponse();

        $client->send($request, $response);

        return $response->getContent();
    }
}
