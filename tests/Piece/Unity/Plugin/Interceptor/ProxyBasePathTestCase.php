<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007, 2009 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_ProxyBasePath
 * @copyright  2006-2007, 2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 1.0.0
 * @deprecated File deprecated in Release 1.0.0
 */

require_once realpath(dirname(__FILE__) . '/../../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Unity/Plugin/Interceptor/ProxyBasePath.php';
require_once 'Piece/Unity/Context.php';
require_once 'Piece/Unity/Config.php';

// {{{ Piece_Unity_Plugin_Interceptor_ProxyBasePathTestCase

/**
 * TestCase for Piece_Unity_Plugin_Interceptor_ProxyBasePath
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_ProxyBasePath
 * @copyright  2006-2007, 2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 * @deprecated Class deprecated in Release 1.0.0
 */
class Piece_Unity_Plugin_Interceptor_ProxyBasePathTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    function tearDown()
    {
        Piece_Unity_Context::clear();
    }

    function testProxy()
    {
        $previousScriptName = @$_SERVER['REQUEST_URI'];
        $_SERVER['REQUEST_URI'] = '/bar/baz.php';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';
        $previousSessionCookiePath = ini_get('session.cookie_path');
        ini_set('session.cookie_path', '/bar');

        $config = &new Piece_Unity_Config();
        $context = &Piece_Unity_Context::singleton();
        $context->setProxyPath('/foo');
        $context->setConfiguration($config);

        $interceptor = &new Piece_Unity_Plugin_Interceptor_ProxyBasePath();
        $interceptor->invoke();

        $this->assertEquals('/foo/bar', $context->getBasePath());
        $this->assertEquals('/foo/bar/baz.php', $context->getScriptName());
        $this->assertEquals('/foo/bar', ini_get('session.cookie_path'));

        ini_set('session.cookie_path', $previousSessionCookiePath);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REQUEST_URI'] = $previousScriptName;
    }

    function testNonProxy()
    {
        $previousScriptName = $_SERVER['REQUEST_URI'];
        $_SERVER['REQUEST_URI'] = '/bar/baz.php';

        $config = &new Piece_Unity_Config();
        $context = &Piece_Unity_Context::singleton();
        $context->setProxyPath('/foo');
        $context->setConfiguration($config);

        $interceptor = &new Piece_Unity_Plugin_Interceptor_ProxyBasePath();
        $interceptor->invoke();

        $this->assertEquals('/bar', $context->getBasePath());
        $this->assertEquals('/bar/baz.php', $context->getScriptName());

        $_SERVER['REQUEST_URI'] = $previousScriptName;
    }

    function testAdjustingSessionCookiePathToOff()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';
        $previousSessionCookiePath = ini_get('session.cookie_path');
        ini_set('session.cookie_path', '/bar');

        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Interceptor_ProxyBasePath', 'adjustSessionCookiePath', false);
        $context = &Piece_Unity_Context::singleton();
        $context->setProxyPath('/foo');

        $context->setConfiguration($config);

        $interceptor = &new Piece_Unity_Plugin_Interceptor_ProxyBasePath();
        $interceptor->invoke();

        $this->assertEquals('/bar', ini_get('session.cookie_path'));

        ini_set('session.cookie_path', $previousSessionCookiePath);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
