<?php
/*
  Plugin Name: WebPageAnalyse PageRank Button
  Plugin URI: http://www.webpageanalyse.com
  Description: Shows the Google PageRank for your blog's domain
  Version: 1.0.3
  Author: webpageanalyse
  Author URI: http://www.webpageanalyse.com
  License: GNU LESSER GENERAL PUBLIC LICENSE (http://www.gnu.org/copyleft/lesser.html)
 */
class wpa_pagerank {
    private $pluginId = 'wpa_pagerank';
    private $i18n;

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_uninstall_hook(__FILE__, array($this, 'uninstall'));

        $this->i18n = new wpa_pagerank_i18n(substr(get_bloginfo('language'), 0, 2));
    }

    public function init() {
        wp_register_sidebar_widget($this->pluginId, $this->i18n->_('name'), array($this, 'sidebar'));
        wp_register_widget_control($this->pluginId, $this->i18n->_('name'), array($this, 'settings'));
    }

    public function sidebar() {
        $targetDomain = $this->getTargetDomain();
        printf('<aside id="wpa-pagerank-widget" class="widget">' .
               '<a title="%s" href="http://%s/%s" rel="nofollow">' .
               '<img src="http://%s/widget/pagerank%s/%s?s=wp" alt="%s">' .
               '</a></aside>',
            $this->i18n->_("link_title", $targetDomain),
            $this->i18n->_('domain'),
            $targetDomain,
            $this->i18n->_('domain'),
            $this->getStyle() === "1" ? "" : "2",
            $targetDomain,
            $this->i18n->_("link_title", $targetDomain)
        );
    }

    public function settings() {
        if (count($_POST) > 0) {
            if (isset($_POST['wpa_pagerank_domain'])) {
                update_option('wpa_pagerank_domain', htmlspecialchars($_POST['wpa_pagerank_domain']));
            }
            if (isset($_POST['wpa_pagerank_style'])) {
                update_option('wpa_pagerank_style', htmlspecialchars($_POST['wpa_pagerank_style']));
            } else {
                update_option('wpa_pagerank_style', "2");
            }
        }
        ?>
        <p>
            <label for="wpa_pagerank_domain"><?php echo $this->i18n->_("form.your_domain"); ?></label><br>
            <input type="text" id="wpa_pagerank_domain" name="wpa_pagerank_domain" value="<?php echo $this->getTargetDomain(); ?>">
        </p>
        <p>
            <label for="wpa_pagerank_style"><?php echo $this->i18n->_("form.style"); ?></label><br>
            <input type="radio" id="wpa_pagerank_style" name="wpa_pagerank_style" value="2" <?php if ($this->getStyle() == "2") echo "checked"; ?>>
            <img align="absmiddle" style="margin-left: 4px;" src="http://www.webpageanalyse.com/assets/common/theme/images/website-pr_90px_v02_pr8.png" alt="<?php echo $this->i18n->_("form.pagerank_example"); ?>" /><br>
            <input type="radio" name="wpa_pagerank_style" value="1" <?php if ($this->getStyle() == "1") echo "checked"; ?>>
            <img align="absmiddle" style="margin-left: 5px;" src="http://www.webpageanalyse.com/assets/common/theme/images/pr_8.png" alt="<?php echo $this->i18n->_("form.pagerank_example"); ?>" />
        </p>
    <?php
    }

    public function uninstall() {
        delete_option('wpa_pagerank_domain');
        delete_option('wpa_pagerank_style');
    }

    private function getTargetDomain() {
        $domain = get_option("wpa_pagerank_domain");
        if (empty($domain)) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $domain = $_SERVER['HTTP_HOST'];
            }
        }
        return $domain;
    }

    private function getStyle() {
        $style = get_option("wpa_pagerank_style");
        if (empty($style) || $style == "2")
            return "2";
        else
            return "1";
    }

}

class wpa_pagerank_i18n {
    private $lang;
    private $texts = array(
        "de" => array(
            "name" => "WebPageAnalyse PageRank Button"
            , "domain" => "de.webpageanalyse.com"
            , "link_title" => "PageRank für %s auf WebPageAnalyse"
            , "form.your_domain" => "Ihre Domain:"
            , "form.style" => "Stil:"
            , "form.pagerank_example" => "PageRank Beispiel"
        ),
        "en" => array(
            "name" => "WebPageAnalyse PageRank Button"
            , "domain" => "www.webpageanalyse.com"
            , "link_title" => "PageRank for %s on WebPageAnalyse"
            , "form.your_domain" => "Your domain:"
            , "form.style" => "Style:"
            , "form.pagerank_example" => "PageRank example"
        )
    );

    public function __construct($lang) {
        $this->lang = $lang;
    }

    public function _($key) {
        $args = func_get_args();
        if (sizeof($args) > 1) {
            array_shift($args);
            return vsprintf($this->texts[$this->lang][$key], $args);
        } else {
            return $this->texts[$this->lang][$key];
        }
    }
}

$wpa_pagerank = new wpa_pagerank();
