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

require_once 'Piece/Unity/Plugin/Common.php';

// {{{ Piece_Unity_Plugin_Interceptor_ProxyBasePath

/**
 * An interceptor to adjust the base path and the script name of the current
 * request which are held in the Piece_Unity_Context object.
 * This interceptor is used and only works if the web servers where your
 * application is running on are used as back-end servers for reverse proxy
 * servers.
 *
 * The base path and the script name are both relative paths since they are
 * based on REQUEST_URI environment variable. The following is a example of
 * a context change when 'proxyPath' configuration point is set to '/foo' in
 * Configurator_Env plug-in.
 *
 * <pre>
 * Configuration Point 'proxyPath' in Configurator_Env plug-in: /foo
 *
 * Requested URL (front-end): http://example.org/foo/bar/baz.php
 * Requested URL (back-end):  http://back-end.example.org/bar/baz.php
 * Base Path (original):     /bar
 * Base Path (adjusted):     /foo/bar
 * Script Name (original):   /bar/baz.php
 * Script Name (adjusted):   /foo/bar/baz.php
 * </pre>
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_ProxyBasePath
 * @copyright  2006-2007, 2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 * @deprecated Class deprecated in Release 1.0.0
 */
class Piece_Unity_Plugin_Interceptor_ProxyBasePath extends Piece_Unity_Plugin_Common
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

    // }}}
    // {{{ invoke()

    /**
     * Invokes the plugin specific code.
     *
     * @return boolean
     */
    function invoke()
    {
        if (!$this->_context->usingProxy()) {
            return true;
        }

        $path = $this->_context->getProxyPath();
        if (!is_null($path)) {
            $this->_context->setBasePath($path . $this->_context->getBasePath());
            $this->_context->setScriptName($path . $this->_context->getScriptName());

            $adjustSessionCookiePath = $this->_getConfiguration('adjustSessionCookiePath');
            if ($adjustSessionCookiePath) {
                ini_set('session.cookie_path',
                        $path . str_replace('//', '/', ini_get('session.cookie_path'))
                        );
            }
        }

        return true;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes extension points and configuration points.
     */
    function _initialize()
    {
        $this->_addConfigurationPoint('adjustSessionCookiePath', true);
    }
 
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
