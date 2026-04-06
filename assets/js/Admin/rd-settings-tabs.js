/**
 * Rundiz settings page (WP plugin).
 * Tabs functional
 * 
 * @package okv-oauth
 */


// on dom ready --------------------------------------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', (event) => {
    const tabsContainer = document.querySelector('.rd-settings-tabs');
    if (!tabsContainer) {
        return;
    }

    const tabList = tabsContainer.querySelector('.tab-pane'); // the <ul> containing <li>s
    const tabContentPanels = tabsContainer.querySelectorAll('.tab-content > div');
    const localStorageKey = 'rd-yte-settings-tabs-active_' + (window.location.href).replace(/[:\/\\?&=]/g, '');

    let rememberedActiveTabId = null;

    if (typeof(Storage) !== 'undefined') {
        rememberedActiveTabId = localStorage.getItem(localStorageKey);

        if (rememberedActiveTabId) {
            // try to activate the remembered tab
            const rememberedLink = tabList.querySelector(`a[href="${rememberedActiveTabId}"]`);
            if (rememberedLink) {
                // remove active from all tabs
                tabList.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                // add active to the parent <li> of the remembered link
                rememberedLink.closest('li').classList.add('active');
            }
        }
    }// endif; local storage supported check

    let activeTabLi = tabList.querySelector('li.active');
    if (!activeTabLi) {
        // if not found currently active tab, set first tab as active.
        activeTabLi = tabList.querySelector('li');
        if (activeTabLi) {
            activeTabLi.classList.add('active');
        }
    }

    // activate the corresponding tab content
    if (activeTabLi) {
        const activeLink = activeTabLi.querySelector('a');
        const targetId = activeLink ? activeLink.getAttribute('href') : null;

        if (targetId) {
            tabContentPanels.forEach(panel => panel.classList.remove('active'));
            const targetPanel = tabsContainer.querySelector('.tab-content ' + targetId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        }
    }// endif;

    // listening on click on the tabs use event delegation.
    tabList.addEventListener('click', function(tablistEvent) {
        // Only respond to clicks on <a> elements inside <li>
        const link = tablistEvent.target.closest('a');
        if (!link || link.parentElement.parentElement !== tabList) {
            return;
        }

        tablistEvent.preventDefault();

        const targetId = link.getAttribute('href');
        if (!targetId) {
            return;
        }

        // update active tab (<li>)
        tabList.querySelectorAll('li').forEach(li => li.classList.remove('active'));
        link.closest('li').classList.add('active');

        // update active tab content
        tabContentPanels.forEach(panel => panel.classList.remove('active'));
        const targetPanel = tabsContainer.querySelector('.tab-content ' + targetId);
        if (targetPanel) {
            targetPanel.classList.add('active');
        }

        // save to localStorage only on user click
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem(localStorageKey, targetId);
        }
    });
});
