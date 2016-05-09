<?php

/**
 * @file
 * Contains \PHPPM\Bridges\DrupalSevenKernel.
 */

namespace PHPPM\Bridges;

use PHPPM\Bridges\BridgeInterface;
use PHPPM\Bridges\HttpKernel as SymfonyBridge;
use React\Http\Request as Request;
//use React\Http\Response as ReactResponse;
use PHPPM\React\HttpResponse as Response;
use Symfony\Component\HttpKernel\TerminableInterface;


/**
 * PHP-PM bridge adapter for DrupalSevenKernel.
 *
 * Extends `\PHPPM\Bridges\HttpKernel` to populate various request
 * meta-variables specified by CGI/1.1 (RFC 3875).
 *
 * @see http://www.faqs.org/rfcs/rfc3875.html
 * @see http://php.net/manual/en/reserved.variables.server.php
 */
class DrupalSevenKernel extends SymfonyBridge implements BridgeInterface {

  /**
   * {@inheritdoc}
   */
  public function onRequest(Request $request, Response $response) {

    if (NULL === $this->application) {
      return;
    }
    $content = '';
    $headers = $request->getHeaders();
    $contentLength = isset($headers['Content-Length']) ? (int) $headers['Content-Length'] : 0;

    $request->on('data', function($data)
          use ($request, $response, &$content, $contentLength) {

        // Read data (may be empty for GET request).
        $content .= $data;

        // Handle request after receive.
      if (strlen($content) >= $contentLength) {

        try {

            $this->application->handle($request, $response);
        }
        catch (\Exception $exception) {
          // Internal server error.
              $response->writeHead(500);
              $response->end();
              return;
        }

      if ($this->application instanceof TerminableInterface) {
            $this->application->terminate($syRequest, $syResponse);
        }
      }
    });
  }
}
