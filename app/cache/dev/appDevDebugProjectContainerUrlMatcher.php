<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appDevDebugProjectContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($rawPathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($rawPathinfo);
        $context = $this->context;
        $request = $this->request;

        if (0 === strpos($pathinfo, '/_')) {
            // _wdt
            if (0 === strpos($pathinfo, '/_wdt') && preg_match('#^/_wdt/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_wdt')), array (  '_controller' => 'web_profiler.controller.profiler:toolbarAction',));
            }

            if (0 === strpos($pathinfo, '/_profiler')) {
                // _profiler_home
                if ('/_profiler' === rtrim($pathinfo, '/')) {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', '_profiler_home');
                    }

                    return array (  '_controller' => 'web_profiler.controller.profiler:homeAction',  '_route' => '_profiler_home',);
                }

                if (0 === strpos($pathinfo, '/_profiler/search')) {
                    // _profiler_search
                    if ('/_profiler/search' === $pathinfo) {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchAction',  '_route' => '_profiler_search',);
                    }

                    // _profiler_search_bar
                    if ('/_profiler/search_bar' === $pathinfo) {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchBarAction',  '_route' => '_profiler_search_bar',);
                    }

                }

                // _profiler_purge
                if ('/_profiler/purge' === $pathinfo) {
                    return array (  '_controller' => 'web_profiler.controller.profiler:purgeAction',  '_route' => '_profiler_purge',);
                }

                // _profiler_info
                if (0 === strpos($pathinfo, '/_profiler/info') && preg_match('#^/_profiler/info/(?P<about>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_info')), array (  '_controller' => 'web_profiler.controller.profiler:infoAction',));
                }

                // _profiler_phpinfo
                if ('/_profiler/phpinfo' === $pathinfo) {
                    return array (  '_controller' => 'web_profiler.controller.profiler:phpinfoAction',  '_route' => '_profiler_phpinfo',);
                }

                // _profiler_search_results
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/search/results$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_search_results')), array (  '_controller' => 'web_profiler.controller.profiler:searchResultsAction',));
                }

                // _profiler
                if (preg_match('#^/_profiler/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler')), array (  '_controller' => 'web_profiler.controller.profiler:panelAction',));
                }

                // _profiler_router
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/router$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_router')), array (  '_controller' => 'web_profiler.controller.router:panelAction',));
                }

                // _profiler_exception
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception')), array (  '_controller' => 'web_profiler.controller.exception:showAction',));
                }

                // _profiler_exception_css
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception\\.css$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception_css')), array (  '_controller' => 'web_profiler.controller.exception:cssAction',));
                }

            }

            // _twig_error_test
            if (0 === strpos($pathinfo, '/_error') && preg_match('#^/_error/(?P<code>\\d+)(?:\\.(?P<_format>[^/]++))?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_twig_error_test')), array (  '_controller' => 'twig.controller.preview_error:previewErrorPageAction',  '_format' => 'html',));
            }

        }

        // devicemanager_default_index
        if ('' === rtrim($pathinfo, '/')) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'devicemanager_default_index');
            }

            return array (  '_controller' => 'DeviceManagerBundle\\Controller\\DefaultController::indexAction',  '_route' => 'devicemanager_default_index',);
        }

        if (0 === strpos($pathinfo, '/api/data')) {
            // machine_data_version
            if ('/api/data/version' === $pathinfo) {
                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineApiController::versionAction',  '_route' => 'machine_data_version',);
            }

            // machine_data_in_get
            if ('/api/data/get' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_machine_data_in_get;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::index_getAction',  '_route' => 'machine_data_in_get',);
            }
            not_machine_data_in_get:

            // machine_data_unixtime
            if ('/api/data/unixtime' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_machine_data_unixtime;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::unixTimeSetupAction',  '_route' => 'machine_data_unixtime',);
            }
            not_machine_data_unixtime:

            // machine_data_in_post
            if ('/api/data/post' === $pathinfo) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_machine_data_in_post;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::index_postAction',  '_route' => 'machine_data_in_post',);
            }
            not_machine_data_in_post:

        }

        if (0 === strpos($pathinfo, '/sys')) {
            if (0 === strpos($pathinfo, '/sys/update')) {
                // sys_update_timezone
                if ('/sys/update/timezone' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_sys_update_timezone;
                    }

                    return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::systemUpdateTimezoneAction',  '_route' => 'sys_update_timezone',);
                }
                not_sys_update_timezone:

                // sys_update_pubs
                if ('/sys/update/pubs' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_sys_update_pubs;
                    }

                    return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::updatePubsAction',  '_route' => 'sys_update_pubs',);
                }
                not_sys_update_pubs:

                // sys_update_departements
                if ('/sys/update/departements' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_sys_update_departements;
                    }

                    return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::updateDepartements',  '_route' => 'sys_update_departements',);
                }
                not_sys_update_departements:

            }

            // sys_clear_departements
            if ('/sys/clear/departements' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_sys_clear_departements;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::clearDepartements',  '_route' => 'sys_clear_departements',);
            }
            not_sys_clear_departements:

            if (0 === strpos($pathinfo, '/sys/update')) {
                // sys_update_employees
                if ('/sys/update/employees' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_sys_update_employees;
                    }

                    return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::updateEmployees',  '_route' => 'sys_update_employees',);
                }
                not_sys_update_employees:

                // sys_update_fingerprints
                if ('/sys/update/fingerprints' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_sys_update_fingerprints;
                    }

                    return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::updateFingerPrints',  '_route' => 'sys_update_fingerprints',);
                }
                not_sys_update_fingerprints:

                // sys_update_profilepictures
                if ('/sys/update/profilepictures' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_sys_update_profilepictures;
                    }

                    return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::updateProfilePictures',  '_route' => 'sys_update_profilepictures',);
                }
                not_sys_update_profilepictures:

            }

            // sys_reboot
            if ('/sys/reboot' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_sys_reboot;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::sysReboot',  '_route' => 'sys_reboot',);
            }
            not_sys_reboot:

            // sys_update_companyname
            if ('/sys/update/companyname' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_sys_update_companyname;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::updateCompanyname',  '_route' => 'sys_update_companyname',);
            }
            not_sys_update_companyname:

            // sys_clean_all
            if ('/sys/doclean' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_sys_clean_all;
                }

                return array (  '_controller' => 'DeviceManagerBundle\\Controller\\MachineSysController::doCleanSys',  '_route' => 'sys_clean_all',);
            }
            not_sys_clean_all:

        }

        // homepage
        if ('' === rtrim($pathinfo, '/')) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($rawPathinfo.'/', 'homepage');
            }

            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::indexAction',  '_route' => 'homepage',);
        }

        // historique
        if ('/historique' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::historiqueAction',  '_route' => 'historique',);
        }

        if (0 === strpos($pathinfo, '/view')) {
            // viewDepStat
            if ('/viewDepStat' === $pathinfo) {
                return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::viewDepStatAction',  '_route' => 'viewDepStat',);
            }

            // viewPersStat
            if ('/viewPersStat' === $pathinfo) {
                return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::viewPersStatAction',  '_route' => 'viewPersStat',);
            }

        }

        // imageVeille
        if ('/imageVeille' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::imageVeilleAction',  '_route' => 'imageVeille',);
        }

        // admin
        if ('/admin/page' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::adminAction',  '_route' => 'admin',);
        }

        // SupAdmin
        if ('/SupAdmin/page' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::SupAdminAction',  '_route' => 'SupAdmin',);
        }

        if (0 === strpos($pathinfo, '/de')) {
            // departement
            if ('/departement' === $pathinfo) {
                return array (  '_controller' => 'AppBundle\\Controller\\DepartementController::departementAction',  '_route' => 'departement',);
            }

            // deleteDepartement
            if (0 === strpos($pathinfo, '/deleteDepartement') && preg_match('#^/deleteDepartement/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'deleteDepartement')), array (  '_controller' => 'AppBundle\\Controller\\DepartementController::deleteDepartementAction',));
            }

        }

        // editDepartement
        if (0 === strpos($pathinfo, '/editDepartement') && preg_match('#^/editDepartement/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'editDepartement')), array (  '_controller' => 'AppBundle\\Controller\\DepartementController::editDepartementAction',));
        }

        // addEmployee
        if ('/addEmployee' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\EmployeController::addEmployeeAction',  '_route' => 'addEmployee',);
        }

        // editEmployee
        if (0 === strpos($pathinfo, '/editEmployee') && preg_match('#^/editEmployee/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'editEmployee')), array (  '_controller' => 'AppBundle\\Controller\\EmployeController::editEmployeeAction',));
        }

        // viewEmployee
        if ('/viewEmployee' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\EmployeController::viewEmployeeAction',  '_route' => 'viewEmployee',);
        }

        // returnEmployees
        if ('/returnEmployees' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\EmployeController::returnEmployeesAction',  '_route' => 'returnEmployees',);
        }

        // deleteEmployee
        if (0 === strpos($pathinfo, '/deleteEmployee') && preg_match('#^/deleteEmployee/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'deleteEmployee')), array (  '_controller' => 'AppBundle\\Controller\\EmployeController::deleteEmployeeAction',));
        }

        // addPermission
        if ('/addPermission' === $pathinfo) {
            return array (  '_controller' => 'AppBundle\\Controller\\PermissionController::addPermissionAction',  '_route' => 'addPermission',);
        }

        if (0 === strpos($pathinfo, '/log')) {
            if (0 === strpos($pathinfo, '/login')) {
                // fos_user_security_login
                if ('/login' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                        goto not_fos_user_security_login;
                    }

                    return array (  '_controller' => 'FOS\\UserBundle\\Controller\\SecurityController::loginAction',  '_route' => 'fos_user_security_login',);
                }
                not_fos_user_security_login:

                // fos_user_security_check
                if ('/login_check' === $pathinfo) {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_fos_user_security_check;
                    }

                    return array (  '_controller' => 'FOS\\UserBundle\\Controller\\SecurityController::checkAction',  '_route' => 'fos_user_security_check',);
                }
                not_fos_user_security_check:

            }

            // fos_user_security_logout
            if ('/logout' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_fos_user_security_logout;
                }

                return array (  '_controller' => 'FOS\\UserBundle\\Controller\\SecurityController::logoutAction',  '_route' => 'fos_user_security_logout',);
            }
            not_fos_user_security_logout:

        }

        if (0 === strpos($pathinfo, '/profile')) {
            // fos_user_profile_show
            if ('/profile' === rtrim($pathinfo, '/')) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_fos_user_profile_show;
                }

                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($rawPathinfo.'/', 'fos_user_profile_show');
                }

                return array (  '_controller' => 'FOS\\UserBundle\\Controller\\ProfileController::showAction',  '_route' => 'fos_user_profile_show',);
            }
            not_fos_user_profile_show:

            // fos_user_profile_edit
            if ('/profile/edit' === $pathinfo) {
                if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                    goto not_fos_user_profile_edit;
                }

                return array (  '_controller' => 'FOS\\UserBundle\\Controller\\ProfileController::editAction',  '_route' => 'fos_user_profile_edit',);
            }
            not_fos_user_profile_edit:

        }

        if (0 === strpos($pathinfo, '/re')) {
            if (0 === strpos($pathinfo, '/register')) {
                // fos_user_registration_register
                if ('/register' === rtrim($pathinfo, '/')) {
                    if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                        goto not_fos_user_registration_register;
                    }

                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($rawPathinfo.'/', 'fos_user_registration_register');
                    }

                    return array (  '_controller' => 'FOS\\UserBundle\\Controller\\RegistrationController::registerAction',  '_route' => 'fos_user_registration_register',);
                }
                not_fos_user_registration_register:

                if (0 === strpos($pathinfo, '/register/c')) {
                    // fos_user_registration_check_email
                    if ('/register/check-email' === $pathinfo) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_fos_user_registration_check_email;
                        }

                        return array (  '_controller' => 'FOS\\UserBundle\\Controller\\RegistrationController::checkEmailAction',  '_route' => 'fos_user_registration_check_email',);
                    }
                    not_fos_user_registration_check_email:

                    if (0 === strpos($pathinfo, '/register/confirm')) {
                        // fos_user_registration_confirm
                        if (preg_match('#^/register/confirm/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'HEAD'));
                                goto not_fos_user_registration_confirm;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'fos_user_registration_confirm')), array (  '_controller' => 'FOS\\UserBundle\\Controller\\RegistrationController::confirmAction',));
                        }
                        not_fos_user_registration_confirm:

                        // fos_user_registration_confirmed
                        if ('/register/confirmed' === $pathinfo) {
                            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'HEAD'));
                                goto not_fos_user_registration_confirmed;
                            }

                            return array (  '_controller' => 'FOS\\UserBundle\\Controller\\RegistrationController::confirmedAction',  '_route' => 'fos_user_registration_confirmed',);
                        }
                        not_fos_user_registration_confirmed:

                    }

                }

            }

            if (0 === strpos($pathinfo, '/resetting')) {
                // fos_user_resetting_request
                if ('/resetting/request' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_fos_user_resetting_request;
                    }

                    return array (  '_controller' => 'FOS\\UserBundle\\Controller\\ResettingController::requestAction',  '_route' => 'fos_user_resetting_request',);
                }
                not_fos_user_resetting_request:

                // fos_user_resetting_send_email
                if ('/resetting/send-email' === $pathinfo) {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_fos_user_resetting_send_email;
                    }

                    return array (  '_controller' => 'FOS\\UserBundle\\Controller\\ResettingController::sendEmailAction',  '_route' => 'fos_user_resetting_send_email',);
                }
                not_fos_user_resetting_send_email:

                // fos_user_resetting_check_email
                if ('/resetting/check-email' === $pathinfo) {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_fos_user_resetting_check_email;
                    }

                    return array (  '_controller' => 'FOS\\UserBundle\\Controller\\ResettingController::checkEmailAction',  '_route' => 'fos_user_resetting_check_email',);
                }
                not_fos_user_resetting_check_email:

                // fos_user_resetting_reset
                if (0 === strpos($pathinfo, '/resetting/reset') && preg_match('#^/resetting/reset/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                    if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                        goto not_fos_user_resetting_reset;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'fos_user_resetting_reset')), array (  '_controller' => 'FOS\\UserBundle\\Controller\\ResettingController::resetAction',));
                }
                not_fos_user_resetting_reset:

            }

        }

        // fos_user_change_password
        if ('/profile/change-password' === $pathinfo) {
            if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                goto not_fos_user_change_password;
            }

            return array (  '_controller' => 'FOS\\UserBundle\\Controller\\ChangePasswordController::changePasswordAction',  '_route' => 'fos_user_change_password',);
        }
        not_fos_user_change_password:

        // login
        if ('/login' === $pathinfo) {
            return array('_route' => 'login');
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
