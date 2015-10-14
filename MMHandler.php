<?php

if (!defined('MEDIAWIKI'))
    die();

/**#@+
 * An image handler which adds support for FreeMind mindmap (*.mm) files.
 *
 * @addtogroup Extensions
 *
 * @link http://lib.custis.ru/index.php/MMHandler_(MediaWiki) Documentation
 *
 * @author Vitaliy Filippov <vitalif@mail.ru>
 * @copyright Copyright © 2009 Vitaliy Filippov
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
    'name'         => 'FreeMind mindmap Handler',
    'version'      => 'r2',
    'author'       => 'Vitaliy Filippov',
    'url'          => 'http://lib.custis.ru/index.php/MMHandler_(MediaWiki)',
    'description'  => 'Allows mindmap (.mm) files to be used in standard image tags (e.g. <nowiki>[[Image:Mindmap.mm]]</nowiki>)',
    'descriptionmsg' => 'mmhandler_desc'
);

// Register the media handler
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['MMHandler'] = $dir . 'MMHandler.i18n.php';
$wgAutoloadClasses['MMImageHandler'] = $dir . 'MMImageHandler.php';
$wgMediaHandlers['application/x-freemind'] = 'MMImageHandler';
if (!in_array('mm', $wgFileExtensions))
    $wgFileExtensions[] = 'mm';
$wgXMLMimeTypes['map'] = 'application/x-freemind';
$wgHooks['MimeMagicInit'][] = 'egInstallMMHandlerTypes';
$wgExtensionFunctions[] = 'egInitMMHandler';

function egInitMMHandler()
{
    global $wgVersion;
    if (version_compare($wgVersion, '1.24', '>='))
        return;
    $mm = MimeMagic::singleton();
    if (empty($mm->mExtToMime['mm']))
        $mm->mExtToMime['mm'] = 'application/x-freemind';
    elseif (strpos($mm->mExtToMime['mm'], 'application/x-freemind') === false)
        $mm->mExtToMime['mm'] = trim($mm->mExtToMime['mm']) . ' application/x-freemind';
    if (empty($mm->mMimeToExt['application/x-freemind']))
        $mm->mMimeToExt['application/x-freemind'] = 'mm';
    elseif (strpos($mm->mMimeToExt['application/x-freemind'], 'mm') === false)
        $mm->mMimeToExt['application/x-freemind'] = trim($mm->mMimeToExt['application/x-freemind']) . ' mm';
}

function egInstallMMHandlerTypes($mm)
{
    $mm->addExtraTypes("application/x-freemind mm");
    return true;
}
