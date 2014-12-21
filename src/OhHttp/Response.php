<?php
/**
 * This file is part of the OhHttp library
 *
 * @author      Roger Thomas <rogere84@gmail.com>
 * @copyright   2014 Roger Thomas <rogere84@gmail.com>
 * @package     OhHttp
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace OhHttp;

use OhHttp\Exception\InvalidRedirectStatusCodeException;
use OhHttp\Exception\InvalidStatusCodeException;
/**
 * Response
 */
class Response
{
    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var integer
     */
    protected $status = 200;

    /**
     * Empty Construct
     */
    public function __construct()
    {
    }

    /**
     * Get the response headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the response body
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the response status code
     * @return the $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the headers
     * @param array $headers
     * @param boolean $override (optional) default true
     * @return Response
     */
    public function setHeaders($headers, $override = true)
    {
        if ($override == false) {
            $this->headers = array_merge($headers, $this->headers);
        } else {
            $this->headers = $headers;
        }

        return $this;
    }

    /**
     * Set a single response header
     * @param string $name
     * @param string $value
     * @param boolean $override (optional) default true
     * @return Response
     */
    public function setHeader($name, $value, $override = true)
    {
        if ($override == false) {
            if (array_key_exists($name, $this->headers)) {
                return $this;
            }
        }

        $this->headers[$name] = $value;
    }

    /**
     * Set the body
     * @param string $body
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set the response status
     * @param integer $status
     * @return Response
     */
    public function setStatus($status)
    {
        if (!array_key_exists($status, $this->getHttpResponseCodes())) {
            throw new InvalidStatusCodeException('Status code of "' . $status . '" is invalid.');
        }
        $this->status = (int)$status;

        return $this;
    }

    /**
     * Send the response
     */
    public function send()
    {
        @ob_clean();
        $this->sendHeaders();
        echo $this->body;
    }

    /**
     * Clear all pending headers
     */
    public function cleanHeaders()
    {
        /* @codeCoverageIgnoreStart() */
        @header_remove();
        /* @codeCoverageIgnoreEnd */
    }

    /**
     * Perform a redirect. This does not exit.
     *
     * @param string $url
     * @param integer $status
     * @throws InvalidRedirectStatusCodeException
     */
    public function redirect($url, $status = 302)
    {
        $this->setStatus($status);
        if (!$this->isValidRedirectStatus()) {
            throw new InvalidRedirectStatusCodeException('Invalid redirect status specified: "' . $status . '"');
        }
        $codes = $this->getHttpResponseCodes();
        $this->setHeader('Location', $url);
        $this->cleanHeaders();
        header('HTTP/1.0 ' . $this->getStatus() . ' ' . $codes[$this->getStatus()]);
        $this->outputHeaders();
        return;
    }

    /**
     * Send the HTTP Response Headers
     *
     * @throws InvalidRedirectStatusCodeException
     */
    public function sendHeaders()
    {
        $codes = $this->getHttpResponseCodes();

        if ($this->isValidRedirectStatus()) {
            throw new InvalidRedirectStatusCodeException(
                'You cannot send a redirect using a regular response. Use $this->response->redirect(url, status);'
            );
        }
        $string = 'HTTP/1.0 ' . $this->status . ' ' . $codes[$this->status];

        header($string);
        $this->outputHeaders();
    }

    /**
     * Output the headers
     */
    protected function outputHeaders()
    {
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
    }

    /**
     * Is the response code a valid redirect
     *
     * @return boolean
     */
    protected function isValidRedirectStatus()
    {
        return (in_array($this->status, array(301, 302, 307)));
    }

    /**
     * Get an array of HTTP Response Codes
     *
     * @return array
     */
    protected function getHttpResponseCodes() {
        return array (
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        );
    }
}
