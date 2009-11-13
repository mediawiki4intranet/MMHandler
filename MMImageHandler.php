<?php

/**#@+
 * An image handler which adds support for FreeMind (.mm) files.
 *
 * @author Vitaliy Filippov <vitalif@mail.ru>
 * @copyright Copyright © 2009 Vitaliy Filippov
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * @file
 * @ingroup Media
 */

/**
 * @ingroup Media
 */
class MMPlayCode extends MediaTransformOutput
{
    /**
     * Constructor
     */
    function MMPlayCode ($file, $url, $width, $height, $path = false, $page = false)
    {
        $this->file = $file;
        $this->url = $url;
        $this->width = 0+$width;
        $this->height = 0+$height;
        $this->path = $path;
        $this->page = $page;
    }

    /**
     * Return HTML <object ... /> tag for the flash video player code.
     */
    function toHtml($options = array())
    {
        if (count(func_get_args()) == 2)
            throw new MWException(__METHOD__ .' called in the old style');

        global $wgVisorFreemind, $wgScriptPath;

        // Default address of Flash video playing applet
        if (!$wgVisorFreemind)
            $wgVisorFreemind = 'extensions/MMHandler/visorFreemind.swf';
        if (!preg_match('#^([a-z]+:/)?/#is', $wgVisorFreemind) &&
            substr($wgVisorFreemind, 0, strlen($wgScriptPath) != $wgScriptPath))
            $wgVisorFreemind = $wgScriptPath . '/'. $wgVisorFreemind;

        $prefix = '<div>';
        $postfix = '</div>';
        if (!empty($options['align']))
        {
            switch ($options['align'])
            {
                case 'center': $className = 'center'; break;
                case 'left': $className = 'floatleft'; break;
                case 'right': $className = 'floatright'; break;
                default: $className = 'floatnone'; break;
            }
            $prefix = '<div class="' . $className . '">';
        }

        $strURL = $this->file->getUrl();

        $w = $this->width;
        $h = $this->height;

        return <<<EOF
$prefix<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="$h">
    <param name="movie" value="$wgVisorFreemind" />
    <param name="allowfullscreen" value="true" />
    <param name="flashvars" value="openUrl=_blank&initLoadFile=$strURL&startCollapsedToLevel=5" />
    <param name="quality" value="high" />
    <param name="bgcolor" value="#FFFFFF" />
    <embed type="application/x-shockwave-flash" width="100%" height="$h"
        allowfullscreen="true"
        src="$wgVisorFreemind"
        flashvars="openUrl=_blank&initLoadFile=$strURL&startCollapsedToLevel=5" />
</object>$postfix
EOF;
    }
}

/**
 * @ingroup Media
 */
class MMImageHandler extends ImageHandler
{
    function isEnabled()
    {
        return true;
    }

    function getImageSize($image, $filename)
    {
        return array(640, 480);
    }

    function mustRender($file)
    {
        return true;
    }

    function normaliseParams($image, &$params)
    {
        if (!parent::normaliseParams($image, $params))
            return false;

        $params['physicalWidth'] = $params['width'];
        $params['physicalHeight'] = $params['height'];
        return true;
    }

    function doTransform($image, $dstPath, $dstUrl, $params, $flags = 0)
    {
        if (!$this->normaliseParams($image, $params))
            return new TransformParameterError($params);

        $clientWidth = $params['width'];
        $clientHeight = $params['height'];
        $physicalWidth = $params['physicalWidth'];
        $physicalHeight = $params['physicalHeight'];
        $srcPath = $image->getPath();

        return new MMPlayCode($image, $dstUrl, $clientWidth, $clientHeight, $dstPath);
    }

    function getThumbType($ext, $mime)
    {
        return array('mm', 'application/x-freemind');
    }

    function getLongDesc($file)
    {
        global $wgLang;
        wfLoadExtensionMessages('MMHandler');
        return wfMsgExt('mm-long-desc', 'parseinline',
            $wgLang->formatNum($file->getWidth()),
            $wgLang->formatNum($file->getHeight()),
            $wgLang->formatSize($file->getSize()));
    }
}
