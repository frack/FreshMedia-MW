<?php
/**
 * FreshMedia: A MediaWiki skin based on modern design ideas.
 *                         Minimalist, with more spacing and less lines.
 *
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0
 * Unported License. To view a copy of this license, visit
 * http://creativecommons.org/licenses/by-sa/3.0/ or send a letter to Creative
 * Commons, 171 Second Street, Suite 300, San Francisco, California, 94105, USA.
 *
 * @file
 * @ingroup Skins
 */

if(!defined('MEDIAWIKI'))
    die(-1);


/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @ingroup Skins
 */
class SkinFreshMedia extends SkinTemplate {
    /**
     * Initialize the skin for the page
     * @param $out OutputPage object
     */
    function initPage(OutputPage $out) {
        SkinTemplate::initPage($out);
        $this->skinname = 'freshmedia';
        $this->stylename = 'freshmedia';
        $this->template = 'FreshMediaTemplate';
        $this->useHeadElement = true;
    }

    /**
     * Load skin and user CSS files in the correct order
     * @param $out OutputPage object
     */
    function setupSkinUserCss(OutputPage $out) {
        global $wgHandheldStyle;
        // Delegate to parent to include legacy shared and print modules
        parent::setupSkinUserCss($out);
        $out->addModuleStyles('skins.freshmedia');
    }
}


/**
 * BaseTemplate class for FreshMedia skin
 * @ingroup Skins
 */
class FreshMediaTemplate extends BaseTemplate {
    /**
     * Outputs the entire contents of the page
     */
    public function execute() {
        // Suppress warnings to prevent notices about missing indexes in $this->data
        wfSuppressWarnings();

        // Print the HTML head
        $this->html('headelement');
        $this->renderHeader();
        $this->renderContent();
        $this->renderFooter();
        $this->printTrail();
        echo Html::closeElement('body');
        echo Html::closeElement('html');
        wfRestoreWarnings();
    }

    /*************************************************************************************************/
    /**
     * Renders the page header
     */
    function renderHeader() { ?>
        <header class="mainHeader noprint">
            <div class="user">
                <div class="container"> <?php $this->renderWidgetUserMenu(); ?> </div>
            </div>
            <div class="title">
                <div class="container"> <?php $this->renderWidgetTitle(); ?> </div>
            </div>
            <div class="menu">
                <div class="container"> <?php $this->renderWidgetMainMenu($this->data['sidebar']); ?> </div>
            </div>
        </header> <?php
    }

    /*************************************************************************************************/
    /**
     * Renders the main content area
     */
    function renderContent() {
        $this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getCode(); ?>

        <div class="main-content">
            <div class="container"> <?php
                if ($this->data['sitenotice']) { ?>
                    <div id="siteNotice"><?php $this->html('sitenotice') ?></div> <?php
                } ?>
                <h1 id="firstHeading" class="firstHeading" lang="<?php $this->html('pageLanguage'); ?>">
                    <span dir="auto"><?php $this->html('title') ?></span>
                </h1>
                <?php $this->renderWidgetContentActions(); ?>
                <div id="bodyContent" class="mw-body">
                    <div id="siteSub"><?php $this->msg('tagline') ?></div>
                    <div id="contentSub"<?php $this->html('userlangattributes') ?>><?php $this->html('subtitle') ?></div> <?php
                        if ($this->data['undelete']) { ?>
                            <div id="contentSub2"><?php $this->html('undelete') ?></div> <?php
                        }
                        if ($this->data['newtalk']) { ?>
                            <div class="usermessage"><?php $this->html('newtalk') ?></div> <?php
                        }
                        if ($this->data['showjumplinks']) { ?>
                            <div class="mw-jump"><?php $this->msg('jumpto') ?>
                                <a href="#nav"><?php $this->msg('jumptonavigation') ?></a><?php $this->msg('comma-separator') ?>
                                <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a>
                            </div> <?php
                        }
                        // start content
                        $this->html('bodytext');
                        if ($this->data['catlinks']) { $this->html('catlinks'); }
                        if ($this->data['dataAfterContent']) { $this->html ('dataAfterContent');
                    } ?>
                </div> <!-- /bodyContent -->
            </div> <!-- /container -->
        </div> <!-- /main-content --> <?php
    }

    /*************************************************************************************************/
    /**
     * Renders the page footer
     */
    function renderFooter() {
        $validFooterIcons = $this->getFooterIcons("icononly");
        $validFooterLinks = $this->getFooterLinks("flat"); // Additional footer links ?>

        <footer role="contentinfo"<?php $this->html('userlangattributes') ?>>
            <div class="container"> <?php
                foreach ($validFooterIcons as $blockName => $footerIcons) { ?>
                    <div id="f-<?php echo htmlspecialchars($blockName); ?>ico"> <?php
                        foreach ($footerIcons as $icon) {
                            echo $this->getSkin()->makeFooterIcon($icon);
                        } ?>
                    </div> <?php
                }
                if (count($validFooterLinks) > 0) { ?>
                    <ul id="f-list"> <?php
                        foreach ($validFooterLinks as $aLink) { ?>
                            <li id="<?php echo $aLink ?>"><?php $this->html($aLink) ?></li> <?php
                        } ?>
                    </ul> <?php
                } ?>
            </div>
        </footer> <?php
    }

    /*************************************************************************************************/
    /**
     * Renders the title and contained search
     */
    function renderWidgetTitle() {
        global $freshMediaSitename;
        $headerSiteName = $freshMediaSitename ? $freshMediaSitename : $this->data['sitename'];
        $linkAttributes = array('href' => $this->data['nav_urls']['mainpage']['href']);
        $linkAttributes += Linker::tooltipAndAccesskeyAttribs('p-logo');
        echo Html::rawElement('h1', null, Html::element('a', $linkAttributes, $headerSiteName));
        $this->renderWidgetSearch();
    }

    /*************************************************************************************************/
    /**
     * Renders the search box.
     */
    function renderWidgetSearch() {
        global $wgUseTwoButtonsSearchForm; ?>

        <form action="<?php $this->text('wgScript') ?>" id="searchform">
            <label for="searchInput"><?php $this->msg('search') ?></label>
            <input type='hidden' name="title" value="<?php $this->text('searchtitle') ?>"/> <?php
            echo $this->makeSearchInput(array("id" => "searchInput"));
            echo $this->makeSearchButton("go", array("id" => "searchGoButton", "class" => "searchButton"));
            if ($wgUseTwoButtonsSearchForm) {
                echo "&#160";
                echo $this->makeSearchButton("fulltext", array("id" => "mw-searchButton", "class" => "searchButton"));
            } else { ?>
                <div><a href="<?php $this->text('searchaction') ?>" rel="search"><?php $this->msg('powersearch-legend') ?></a></div> <?php
            } ?>
        </form> <?php
    }

    /*************************************************************************************************/
    /**
     * Renders the personal tools.
     */
    function renderWidgetUserMenu() {
        // Don't render the personal tools title
        # echo Html::openElement('h2', array('class' => 'hidden'));
        # $this->msg('personaltools');
        # echo Html::closeElement('h2');
        echo Html::openElement('ul', array('class' => 'userMenu', 'lang' => $this->data['userlang'], 'dir' => $this->data['dir']));
        foreach ($this->getPersonalTools() as $key => $item) {
                echo $this->makeListItem($key, $item);
        }
        echo Html::closeElement('ul');
    }

    /*************************************************************************************************/
    /**
     * Renders the main menu
     * @param $portals array
     */
    function renderWidgetMainMenu($portals) {
        if (!isset($portals['SEARCH'])) $portals['SEARCH'] = true;
        if (!isset($portals['TOOLBOX'])) $portals['TOOLBOX'] = true;
        if (!isset($portals['LANGUAGES'])) $portals['LANGUAGES'] = true;

        echo Html::openElement('ul', array('class' => 'mainMenu'));
        foreach ($portals as $boxName => $content) {
            if ($content === false)
                continue;
            if ($boxName == 'SEARCH') {
                // The searchbox is disabled, because we already have one in the header.
                // Uncomment the line below to enable it again.
                //$this->renderSearch();
            } elseif ($boxName == 'TOOLBOX') {
                $this->renderWidgetToolbox();
            } elseif ($boxName == 'LANGUAGES') {
                $this->renderWidgetLanguageBox();
            } else {
                $this->renderWidgetCustomMenuBox($boxName, $content);
            }
        }
        echo Html::closeElement('ul');
    }

    /*************************************************************************************************/
    /**
     * Renders the toolbox menu section.
     */
    function renderWidgetToolbox() {
        echo Html::openElement('li');
        echo Html::element('span', null, $this->msg('toolbox'));
        echo Html::openElement('ul');
        foreach ($this->getToolbox() as $key => $tbitem) {
            echo $this->makeListItem($key, $tbitem);
        }
        wfRunHooks('MonoBookTemplateToolboxEnd', array(&$this));
        wfRunHooks('SkinTemplateToolboxEnd', array(&$this, true));
        echo Html::closeElement('ul');
        echo Html::closeElement('li');
    }

    /*************************************************************************************************/
    /**
     * Renders the Language selection menu.
     */
    function renderWidgetLanguageBox() {
        if ($this->data['language_urls']) {
            echo Html::openElement('li');
            echo Html::element('span', null, $this->msg('otherlanguages'));
            echo Html::openElement('ul');
            foreach ($this->data['language_urls'] as $key => $langlink) {
                echo $this->makeListItem($key, $langlink);
            }
            echo Html::closeElement('ul');
            echo Html::closeElement('li');
        }
    }

    /*************************************************************************************************/
    /**
     * Renders a custom content menu section.
     * @param $name string
     * @param $contents array|string
     */
    function renderWidgetCustomMenuBox($name, $contents) {
        $msg = wfMessage($name);
        echo Html::openElement('li');
        echo Html::element('span', null, htmlspecialchars($msg->exists() ? $msg->text() : $name));
        if (is_array($contents)) {
            echo Html::openElement('ul');
            foreach ($contents as $key => $val) {
                echo $this->makeListItem($key, $val);
            }
            echo Html::closeElement('ul');
        } else {
            print $contents;
        }
        echo Html::closeElement('li');
    }

    /*************************************************************************************************/
    /**
     * Renders the content-actions menu.
     */
    function renderWidgetContentActions() {
        echo Html::openElement('ul');
        foreach ($this->data['content_actions'] as $key => $tab) {
            echo "\n" . $this->makeListItem($key, $tab);
        }
        echo Html::closeElement('ul');
    }
} // end of FreshMediaTemplate class
