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
    function renderHeader() {
        echo Html::openElement('header', array('class' => 'mainHeader noprint'));
        echo Html::rawElement('div', array('class' => 'user'), Html::rawElement(
            'div', array('class' => 'container'), $this->widgetUserMenu()););
        echo Html::rawElement('div', array('class' => 'title'), Html::rawElement(
            'div', array('class' => 'container'), $this->widgetTitle()););
        echo Html::rawElement('div', array('class' => 'menu'), Html::rawElement(
            'div', array('class' => 'container'), $this->widgetMainMenu($this->data['sidebar'])););
        echo Html::closeElement('header');
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
                <?php echo $this->widgetContentActions(); ?>
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
                        if ($this->data['dataAfterContent']) { $this->html('dataAfterContent');
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
        $validFooterLinks = $this->getFooterLinks("flat"); // Additional footer links

        echo Html::openElement('footer', array(
            'role' => 'contentinfo',
            'lang' => $this->data['lang'],
            'dir' => $this->data['dir']));
        echo Html::openElement('div', array('class' => 'container'));
        foreach ($validFooterIcons as $blockName => $footerIcons) {
            echo Html::openElement('div', array('id' => "f-{$blockName}ico"));
            foreach ($footerIcons as $icon) {
                echo $this->getSkin()->makeFooterIcon($icon);
            }
            echo Html::closeElement('div');
        }
        if (count($validFooterLinks) > 0) {
            echo Html::openElement('ul', array('id' => 'f-list'));
            foreach ($validFooterLinks as $aLink) {
                echo Html::rawElement('li', array('id' => $aLink), $this->data[$aLink]);
            }
            echo Html::closeElement('ul');
        }
        echo Html::closeElement('div');
        echo Html::closeElement('footer');
    }

    /*************************************************************************************************/
    /**
     * Renders the title and contained search
     */
    function widgetTitle() {
        global $freshMediaSitename;

        $headerSiteName = $freshMediaSitename ? $freshMediaSitename : $this->data['sitename'];
        $linkAttributes = array('href' => $this->data['nav_urls']['mainpage']['href']);
        $linkAttributes += Linker::tooltipAndAccesskeyAttribs('p-logo');

        $output = '';
        $output .= Html::rawElement('h1', null, Html::element('a', $linkAttributes, $headerSiteName));
        $output .= $this->widgetSearch();
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders the search box.
     */
    function widgetSearch() {
        global $wgUseTwoButtonsSearchForm;

        $output = '';
        $output .= Html::openElement('form', array('action' => $this->data['wgScript'], 'id' => 'searchform'));
        $output .= Html::element('label', array('for' => 'searchInput'), $this->getMsg('search'));
        $output .= Html::element('input', array(
            'type' => 'hidden',
            'name' => 'title',
            'value' => $this->data['searchtitle']));
        $output .=  $this->makeSearchInput(array("id" => "searchInput"));
        $output .=  $this->makeSearchButton("go", array("id" => "searchGoButton", "class" => "searchButton"));
        if ($wgUseTwoButtonsSearchForm) {
            $output .= $this->makeSearchButton("fulltext", array("id" => "mw-searchButton", "class" => "searchButton"));
        } else {
            $output .= Html::openElement('div');
            $output .= Html::element('a', array('href' => $this->text('searchaction'), 'rel' => 'search'),
                                     $this->getMsg('powersearch-legend'));
            $output .= Html::closeElement('div');
        }
        $output .= Html::closeElement('form');
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders the personal tools.
     */
    function widgetUserMenu() {
        $output = '';
        // Don't render the personal tools title
        // $output .= Html::rawElement('h2', array('class' => 'hidden'), $this->getMsg('personaltools'));
        $output .= Html::openElement('ul', array(
            'class' => 'userMenu',
            'lang' => $this->data['userlang'],
            'dir' => $this->data['dir']));
        foreach ($this->getPersonalTools() as $key => $item) {
            $output .= $this->makeListItem($key, $item);
        }
        $output .= Html::closeElement('ul');
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders the main menu
     * @param $portals array
     */
    function widgetMainMenu($portals) {
        if (!isset($portals['SEARCH'])) $portals['SEARCH'] = true;
        if (!isset($portals['TOOLBOX'])) $portals['TOOLBOX'] = true;
        if (!isset($portals['LANGUAGES'])) $portals['LANGUAGES'] = true;

        $output = '';
        $output .= Html::openElement('ul', array('class' => 'mainMenu'));
        foreach ($portals as $boxName => $content) {
            if ($content === false)
                continue;
            if ($boxName == 'SEARCH') {
                // The searchbox is disabled, because we already have one in the header.
                // Uncomment the line below to enable it again.
                //$this->renderSearch();
            } elseif ($boxName == 'TOOLBOX') {
                $output .= $this->widgetToolbox();
            } elseif ($boxName == 'LANGUAGES') {
                $output .= $this->widgetLanguageBox();
            } else {
                $output .= $this->widgetCustomMenuBox($boxName, $content);
            }
        }
        $output .= Html::closeElement('ul');
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders the toolbox menu section.
     */
    function widgetToolbox() {
        $output = '';
        $output .= Html::openElement('li');
        $output .= Html::element('span', null, $this->getMsg('toolbox'));
        $output .= Html::openElement('ul');
        foreach ($this->getToolbox() as $key => $tbitem) {
            $output .= $this->makeListItem($key, $tbitem);
        }
        //XXX(Elmer): This is a dirty hack to capture the 'echo' performed by hooks
        ob_start();
        wfRunHooks('SkinTemplateToolboxEnd', array(&$this, true));
        $output .= ob_get_contents();
        ob_end_clean();
        // End of dirty hack
        $output .= Html::closeElement('ul');
        $output .= Html::closeElement('li');
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders the Language selection menu.
     */
    function widgetLanguageBox() {
        $output = '';
        if ($this->data['language_urls']) {
            $output .= Html::openElement('li');
            $output .= Html::element('span', null, $this->msg('otherlanguages'));
            $output .= Html::openElement('ul');
            foreach ($this->data['language_urls'] as $key => $langlink) {
                $output .= $this->makeListItem($key, $langlink);
            }
            $output .= Html::closeElement('ul');
            $output .= Html::closeElement('li');
        }
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders a custom content menu section.
     * @param $name string
     * @param $contents array|string
     */
    function widgetCustomMenuBox($name, $contents) {
        $msg = wfMessage($name);
        $output = '';
        $output .= Html::openElement('li');
        $output .= Html::element('span', null, htmlspecialchars($msg->exists() ? $msg->text() : $name));
        if (is_array($contents)) {
            $output .= Html::openElement('ul');
            foreach ($contents as $key => $val) {
                $output .= $this->makeListItem($key, $val);
            }
            $output .= Html::closeElement('ul');
        } else {
            $output .= $contents;
        }
        $output .= Html::closeElement('li');
        return $output;
    }

    /*************************************************************************************************/
    /**
     * Renders the content-actions menu.
     */
    function widgetContentActions() {
        $output = '';
        $output .= Html::openElement('ul');
        foreach ($this->data['content_actions'] as $key => $tab) {
            $output .= "\n" . $this->makeListItem($key, $tab);
        }
        $output .= Html::closeElement('ul');
        return $output;
    }
} // end of FreshMediaTemplate class
