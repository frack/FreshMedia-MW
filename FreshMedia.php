<?php
/**
 * Fresh Media: a MediaWiki skin based on modern design ideas. It aims to provide
 * a clean experience where blocks are separated by whitespace rather than borders.
 *
 * @file
 * @ingroup Skins
 * @author Elmer de Looff (elmer.delooff@gmail.com)
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 */

if( !defined( 'MEDIAWIKI' ) ) die( "This is an extension to the MediaWiki package and cannot be run standalone." );

$wgExtensionCredits['skin'][] = array(
    'path' => __FILE__,
    'name' => 'FreshMedia',
    'version' => '0.1',
    'url' => "https://github.com/frack/FreshMedia-MW",
    'author' => array('Elmer de Looff'),
    'descriptionmsg' => 'freshmedia-desc',
);

$skinPath = dirname(__FILE__);
$skinName = basename($skinPath);

$wgValidSkinNames['freshmedia'] = 'FreshMedia';
$wgAutoloadClasses['SkinFreshMedia'] = "{$skinPath}/FreshMedia.skin.php";
$wgExtensionMessagesFiles['FreshMedia'] = "{$skinPath}/FreshMedia.i18n.php";

$wgResourceModules['skins.freshmedia'] = array(
    'styles' => array(
        "{$skinName}/styles/basetemplate.css" => array( 'media' => 'screen' ),
        "{$skinName}/styles/template.css" => array( 'media' => 'screen' ),
        "{$skinName}/styles/monobook.css" => array( 'media' => 'screen' ),
    ),
    'remoteBasePath' => &$GLOBALS['wgStylePath'],
    'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);
