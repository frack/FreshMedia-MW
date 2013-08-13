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
     * Renders the wiki page according to the formatting of the skin.
     */
    public function execute() {
        // Suppress warnings to prevent notices about missing indexes in $this->data
        # wfSuppressWarnings();

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
     * Renders the page header.
     */
    function renderHeader() {
        echo Html::openElement('header', array('class' => 'main-header noprint'));
        echo Html::rawElement('div', array('class' => 'user'), Html::rawElement(
            'div', array('class' => 'container'), $this->widgetUserMenu()));
        echo Html::rawElement('div', array('class' => 'title'), Html::rawElement(
            'div', array('class' => 'container'), $this->widgetTitle()));
        echo Html::rawElement('div', array('class' => 'menu'), Html::rawElement(
            'div', array('class' => 'container'), $this->widgetMainMenu($this->data['sidebar'])));
        echo Html::closeElement('header');
    }

    /*************************************************************************************************/
    /**
     * Renders the main content area.
     */
    function renderContent() {
        $this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getCode();

        echo Html::openElement('div', array('class' => 'main-content'));
        echo Html::openElement('div', array('class' => 'container'));
        if ($this->data['sitenotice']) {
            echo Html::rawElement('div', array('id' => 'siteNotice'), $this->data['sitenotice']);
        }
        echo Html::openElement('h1', array(
            'id' => 'first-heading',
            'class' => 'first-heading',
            'lang' => $this->data['pageLanguage']));
        echo Html::rawElement('span', array('dir' => 'auto'), $this->data['title']);
        echo Html::closeElement('h1');
        echo $this->widgetContentActions();

        echo Html::openElement('div', array('id' => 'bodyContent', 'class' => 'body-content'));
        echo Html::rawElement('div', array('id' => 'siteSub'), $this->getMsg('tagline'));
        echo Html::rawElement(
            'div', array('id' => 'contentSub', 'dir' => $this->data['dir'], 'lang' => $this->data['lang']),
            $this->data['subtitle']);
        if ($this->data['undelete']) {
            echo Html::rawElement('div', array('id' => 'contentSub2'), $this->data['undelete']);
        }
        if ($this->data['newtalk']) {
            echo Html::rawElement('div', array('class' => 'usermessage'), $this->data['newtalk']);
        }
        if ($this->data['showjumplinks']) {
            echo Html::openElement('div', array('class' => 'mw-jump'));
            $this->msg('jumpto');
            echo Html::rawElement('a', array('href' => '#nav'), $this->getMsg('jumptonavigation'));
            $this->msg('comma-separator');
            echo Html::rawElement('a', array('href' => '#searchInput'), $this->getMsg('jumptosearch'));
            echo Html::closeElement('div');
        }
        // start content
        $this->html('bodytext');
        $this->html('catlinks');
        $this->html('dataAfterContent');
        echo Html::closeElement('div'); // bodyContent
        echo Html::closeElement('div'); // container
        echo Html::closeElement('div'); // main-content
    }

    /*************************************************************************************************/
    /**
     * Renders the page footer.
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
     * Returns the title and contained search as a string.
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
     * Returns the search box as a string.
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
        $output .=  $this->makeSearchButton("go", array("id" => "searchGoButton", "class" => "search-button"));
        if ($wgUseTwoButtonsSearchForm) {
            $output .= $this->makeSearchButton("fulltext", array("id" => "mw-searchButton", "class" => "search-button"));
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
     * Returns the user menu (or login/creat account section) as a string.
     */
    function widgetUserMenu() {
        $output = '';
        // Don't render the personal tools title
        // $output .= Html::rawElement('h2', array('class' => 'hidden'), $this->getMsg('personaltools'));
        $output .= Html::openElement('ul', array(
            'class' => 'user-menu',
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
     * Renders the main menu as a string.
     * @param $portals array
     */
    function widgetMainMenu($portals) {
        if (!isset($portals['SEARCH'])) $portals['SEARCH'] = true;
        if (!isset($portals['TOOLBOX'])) $portals['TOOLBOX'] = true;
        if (!isset($portals['LANGUAGES'])) $portals['LANGUAGES'] = true;

        $output = '';
        $output .= Html::openElement('ul', array('class' => 'main-menu'));
        foreach ($portals as $boxName => $content) {
            if ($content === false)
                continue;
            if ($boxName == 'SEARCH') {
                // The searchbox is disabled, because we already have one in the header.
                // Uncomment the line below to enable it again.
                // $output .= $this->widgetSearch();
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
     * Returns the toolbox menu section as a string.
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
     * Returns the language selection menu as a string.
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
     * Returns a custom content menu section as a string.
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
     * Returns the content-actions menu as a string.
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
