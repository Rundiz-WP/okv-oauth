<?php
/**
 * @package rundiz-oauth
 */


namespace RundizOauth\Tests\App\Libraries;


class LoaderTest extends \WP_UnitTestCase
{


    public function testLoadConfig()
    {
        $Loader = new \RundizOauth\App\Libraries\Loader();
        $configVal = $Loader->loadConfig();
        $this->assertTrue(is_array($configVal) && !empty($configVal));
        $this->assertTrue(array_key_exists('rundiz_settings_config_file', $configVal));
        unset($configVal, $Loader);
    }


    public function testLoadTemplate()
    {
        $Loader = new \RundizOauth\App\Libraries\Loader();
        ob_start();
        $Loader->loadTemplate('okv-oauth/index_v');
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(is_string($contents) && !empty($contents));
        unset($contents, $Loader);
    }


    public function testLoadView()
    {
        $Loader = new \RundizOauth\App\Libraries\Loader();
        ob_start();
        $result = $Loader->loadView('admin/settings_v');
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertTrue($result);
        $this->assertTrue(is_string($contents) && !empty($contents));

        unset($contents, $Loader, $result);
    }


}
