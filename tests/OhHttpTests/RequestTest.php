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
namespace OhHttpTests;

use OhHttp\Request;

/**
 * RequestTest
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    private $request = null;

    public function testGetBody()
    {
        $this->request = new Request();
        $this->assertFalse($this->request->getBody());
        $this->assertFalse($this->request->getRawBody());
    }

    public function testGetIp()
    {
        @$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->request = new Request();
        $this->assertEquals('127.0.0.1', $this->request->getIp());
        $this->assertEquals('127.0.0.1', $this->request->getClientIp());
    }

    public function testGetIpForwarded()
    {
        @$_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.2';
        $this->request = new Request();
        $this->assertEquals('127.0.0.2', $this->request->getIp());
        $this->assertEquals('127.0.0.2', $this->request->getClientIp());
    }

    public function testGetClientIp()
    {
        @$_SERVER['HTTP_CLIENT_IP'] = '127.0.0.3';
        $this->request = new Request();
        $this->assertEquals('127.0.0.3', $this->request->getIp());
        $this->assertEquals('127.0.0.3', $this->request->getClientIp());
    }

    public function testXmlHttpRequest()
    {
        $this->request = new Request();
        $this->assertFalse($this->request->isXmlHttpRequest());
        @$_SERVER['X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->request = new Request();
        $this->assertTrue($this->request->isXmlHttpRequest());
    }

    public function testRequestMethods()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->request = new Request();
        $this->assertTrue($this->request->isGet());
        $this->assertFalse($this->request->isPost());
        $this->assertFalse($this->request->isPut());
        $this->assertFalse($this->request->isDelete());
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->request = new Request();
        $this->assertTrue($this->request->isPost());
        $this->assertFalse($this->request->isGet());
        $this->assertFalse($this->request->isPut());
        $this->assertFalse($this->request->isDelete());
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $this->request = new Request();
        $this->assertTrue($this->request->isPut());
        $this->assertFalse($this->request->isGet());
        $this->assertFalse($this->request->isPost());
        $this->assertFalse($this->request->isDelete());
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->request = new Request();
        $this->assertTrue($this->request->isDelete());
        $this->assertFalse($this->request->isGet());
        $this->assertFalse($this->request->isPost());
        $this->assertFalse($this->request->isPut());
    }

    public function testParams()
    {
        $_GET = array('a' => 'b');
        $_POST = array('c' => 'd');
        $this->request = new Request();
        $this->request->setParam('e', 'f');
        $this->assertCount(1, $this->request->getGetParams());
        $this->assertCount(1, $this->request->getPostParams());
        $this->assertCount(1, $this->request->getUserParams());
        $this->assertCount(3, $this->request->getParams());
        $this->assertEquals('b', $this->request->getParam('a'));
        $this->assertEquals('d', $this->request->getParam('c'));
        $this->assertEquals(null, $this->request->getParam('noparam', null));
        $this->request->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->request->getParam('foo'));
    }

    public function testParamSetGetSpecific()
    {
        $_GET = array('a' => 'get');
        $_POST = array('a' => 'post');
        $this->request = new Request();
        $this->request->setParam('a', 'custom');
        $this->assertCount(1, $this->request->getGetParams());
        $this->assertCount(1, $this->request->getPostParams());
        $this->assertCount(1, $this->request->getUserParams());
        $all = $this->request->getParams();
        $this->assertCount(1, $all);
        $this->assertEquals('custom', $all['a']);
        $this->assertEquals('get', $this->request->getParamGet('a'));
        $this->assertEquals('post', $this->request->getParamPost('a'));
        $this->assertEquals('custom', $this->request->getParamCustom('a'));

        $this->assertNull($this->request->getParamGet('b'));
        $this->assertNull($this->request->getParamPost('b'));
        $this->assertNull($this->request->getParamCustom('b'));
    }

    public function testGetMethodWhenValidAndInvalid()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->request = new Request();
        $this->assertEquals('GET', $this->request->getRequestMethod());
        unset($_SERVER['REQUEST_METHOD']);
        $this->request = new Request();
        try {
            $this->request->getRequestMethod();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\OhHttp\Exception\RequestMethodNotSetException', $e);
            return;
        }
        $this->fail('Expected exception');
    }

    public function testGetHeader()
    {
        $_SERVER['HTTP_HEADERONE'] = 'foo';
        $_SERVER['HTTP_HEADER_TWO'] = 'bar';
        $this->request = new Request();
        $this->assertNull($this->request->getHeader(''));
        $this->assertFalse($this->request->getHeader('', false));
        $this->assertEquals('foo', $this->request->getHeader('headerone'));
        $this->assertNull($this->request->getHeader('abcdef'));
    }

    public function testRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/foo';
        $this->request = new Request();
        $this->assertEquals('/foo', $this->request->getRawRequestUri());
        unset($_SERVER['REQUEST_URI']);
        $this->request = new Request();
        $this->assertEquals('/', $this->request->getRawRequestUri());
        $this->assertEquals('/', $this->request->getRequestUri());
        $_SERVER['REQUEST_URI'] = '/?foo=bar';
        $this->request = new Request();
        $this->assertEquals('/?foo=bar', $this->request->getRawRequestUri());
        $_SERVER['REQUEST_URI'] = '/?foo=bar';
        $this->request = new Request();
        $this->assertEquals('/', $this->request->getRequestUri());
    }

    public function testHttps()
    {
        $_SERVER['HTTPS'] = 'off';
        $this->request = new Request();
        $this->assertFalse($this->request->isHttpsRequest());
        $_SERVER['HTTPS'] = 'on';
        $this->request = new Request();
        $this->assertTrue($this->request->isHttpsRequest());
    }
}
