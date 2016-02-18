<?php

use JonnyW\PhantomJs\Client;
use PhantomInstaller\PhantomBinary;

class HtmlPage {

    /**
     * Gets a PhantomJS client instance
     *
     * @return JonnyW\PhantomJs\Client
     */
    static private function getPhantomJsClientInstance () {
        $client = Client::getInstance();
        $client->getEngine()->setPath(PhantomBinary::BIN);
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
