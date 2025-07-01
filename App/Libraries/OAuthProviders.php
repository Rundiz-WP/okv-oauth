<?php
/**
 * @package rundiz-oauth
 * @since 1.5.7
 */


namespace RundizOauth\App\Libraries;


if (!class_exists('\\RundizOauth\\App\\Libraries\\OAuthProviders')) {
    /**
     * OAuth providers.
     * 
     * @since 1.5.7
     */
    class OAuthProviders
    {


        /**
         * @var array List of providers keys and its class names (without name space).
         */
        protected $providers = [
            'google' => 'Google',
            'facebook' => 'Facebook',
            'linenaver' => 'Line',
        ];


        /**
         * Get all OAuth classes.
         * 
         * @return array|null Return `null` if found nothing enabled, or return array with provider key as keys with class that implemented `\RundizOauth\App\Libraries\MyOauth\Interfaces\MyOAuthInterface` as array value.
         */
        public function getAllClasses()
        {
            $output = [];

            foreach ($this->providers as $prokey => $className) {
                $classString = '\\RundizOauth\\App\\Libraries\\MyOauth\\' . $className;
                $interfaces = class_implements($classString);
                if (isset($interfaces['RundizOauth\\App\\Libraries\\MyOauth\\Interfaces\\MyOAuthInterface'])) {
                    $output[$prokey] = new $classString();
                }
            }// endforeach;
            unset($className, $prokey);

            if (empty($output)) {
                $output = null;
            }
            return $output;
        }// getAllClasses


        /**
         * Get OAuth a single class object based on `$check` argument.
         * 
         * @param string $check The string to check with `$providers` property. Only matched will return its class
         * @return \RundizOauth\App\Libraries\MyOauth\Interfaces\MyOAuthInterface|null Return class that implemented `MyOAuthInterface`.
         * @throws \InvalidArgumentException Throw exception if invalid argument type.
         */
        public function getClass($check)
        {
            if (gettype($check) !== 'string') {
                throw new \InvalidArgumentException('The argument $check must be string.');
            }

            foreach ($this->providers as $prokey => $className) {
                if ($check === $prokey) {
                    $classString = '\\RundizOauth\\App\\Libraries\\MyOauth\\' . $className;
                    $interfaces = class_implements($classString);
                    if (isset($interfaces['RundizOauth\\App\\Libraries\\MyOauth\\Interfaces\\MyOAuthInterface'])) {
                        $classObj = new $classString();
                        unset($classString, $interfaces);
                        return $classObj;
                    }
                    unset($classString, $interfaces);
                }
            }// endforeach;
            unset($className, $prokey);

            return null;
        }// getClass


        /**
         * Get OAuth classes based on setting in `$rundizoauth_options` argument.
         * 
         * @param array $rundizoauth_options The setting option which OAuth is enabled.
         * @return array|null Return `null` if found nothing enabled, or return array with provider key as keys with class that implemented `\RundizOauth\App\Libraries\MyOauth\Interfaces\MyOAuthInterface` as array value.
         */
        public function getClasses(array $rundizoauth_options)
        {
            $output = [];

            foreach ($this->providers as $prokey => $className) {
                if (
                    array_key_exists($prokey . '_login_enable', $rundizoauth_options) && 
                    '1' === $rundizoauth_options[$prokey . '_login_enable']
                ) {
                    $classString = '\\RundizOauth\\App\\Libraries\\MyOauth\\' . $className;
                    $interfaces = class_implements($classString);
                    if (isset($interfaces['RundizOauth\\App\\Libraries\\MyOauth\\Interfaces\\MyOAuthInterface'])) {
                        $output[$prokey] = new $classString();
                    }
                }
            }// endforeach;
            unset($className, $prokey);

            if (empty($output)) {
                $output = null;
            }
            return $output;
        }// getClasses


    }// OAuthProviders
}// endif;
